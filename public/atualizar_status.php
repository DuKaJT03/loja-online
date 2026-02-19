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

$conexao = Conexao::conectar();

$stmt = $conexao->prepare(
    "UPDATE pedidos
    SET status = ?
    WHERE id = ?
    AND EXISTS (
        SELECT 1
        FROM itens_pedido i
        INNER JOIN produtos p ON p.id = i.id_produto
        WHERE i.id_pedido = pedidos.id
        AND p.id_lojista = ?
    )"
);

if(!$stmt){
    die("Erro prepare: ".$conexao->error);
}

$stmt->bind_param("sii", $status, $id_pedido, $id_lojista);
$stmt->execute();

echo"ok";