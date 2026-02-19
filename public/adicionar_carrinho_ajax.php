<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

//Verifica se foi enviado o ID via POST
if(!isset($_POST['id'])){ // 'id' :nome da variável esperada (vinda do JS)
    http_response_code(400);
    echo "ID não enviado";
    exit;
}

$id = (int) $_POST['id'];

$conexao = Conexao::conectar();

$stmt = $conexao->prepare(
    "SELECT id, nome, preco FROM produtos WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows !== 1){
    http_response_code(404);
    echo "Produto não encontrado";
    exit;
}

$produto = $resultado->fetch_assoc();

if (!isset($_SESSION['carrinho'])){
    $_SESSION['carrinho'] = [];
}

if(isset($_SESSION['carrinho'][$id])){
    $_SESSION['carrinho'][$id]['quantidade']++;
}else{
    $_SESSION['carrinho'][$id] = [
        'id' => $produto['id'],
        'nome' => $produto['nome'],
        'preco'=> $produto['preco'],
        'quantidade' => 1
    ];
}

echo "ok";

?>