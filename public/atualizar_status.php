<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'lojista'){
    exit("Acesso negado");
}

$id_lojista = $_SESSION['usuario_id'];
$id_pedido = intval($_POST['id']);
$status = $_POST['status'];

$pdo = Conexao::conectar();

$sql =
    "UPDATE pedidos
    SET status = :status
    WHERE id = :id_pedido
    AND EXISTS (
        SELECT 1
        FROM itens_pedido i
        INNER JOIN produtos p ON p.id = i.id_produto
        WHERE i.id_pedido = pedidos.id
        AND p.id_lojista = :id_lojista
    )
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':status', $status);
$stmt->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->bindValue(':id_lojista', $id_lojista, PDO::PARAM_INT);

$stmt->execute();

echo "ok";