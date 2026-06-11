<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'lojista'){
    exit("Acesso negado");
}

$id_lojista = $_SESSION['usuario_id'];
$id_item = intval($_POST['id']);
$status = $_POST['status'];

$pdo = Conexao::conectar();

// Atualiza SOMENTE o item que pertence ao lojista
$sql =
    "UPDATE itens_pedido i
    SET status = :status
    WHERE i.id = :id_item
    AND EXISTS (
        SELECT 1
        FROM produtos p
        WHERE p.id = i.id_produto
        AND p.id_lojista = :id_lojista
    )
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':status', $status);
$stmt->bindValue(':id_item', $id_item, PDO::PARAM_INT);
$stmt->bindValue(':id_lojista', $id_lojista, PDO::PARAM_INT);

$stmt->execute();

$stmtPedido = $pdo->prepare(
    "UPDATE pedidos
     SET status = :status
     WHERE id = (
        SELECT id_pedido
        FROM itens_pedido
        WHERE id = :id_item
     )"
);

$stmtPedido->bindValue(':status', $status);
$stmtPedido->bindValue(':id_item', $id_item, PDO::PARAM_INT);

$stmtPedido->execute();

echo "ok";