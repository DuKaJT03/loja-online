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

$pdo = Conexao::conectar();

$sql =
    "SELECT id, nome, preco 
    FROM produtos 
    WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$produto){
    http_response_code(404);
    echo "Produto não encontrado";
    exit;
}

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