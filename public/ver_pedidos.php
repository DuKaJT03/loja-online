<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

// Verifica se o cliente está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'cliente'){
    header('Location: login.html');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

$pdo = Conexao::conectar();

// Busca todos os pedidos do cliente
$sql =
    "SELECT id, data, total, status
    FROM pedidos
    WHERE id_cliente = :id_cliente
    ORDER BY data DESC
    ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt->execute();

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Meus Pedidos</h2>

<a href="painel_cliente.php">Voltar para o painel</a><br><br>

<?php
if(count($pedidos) > 0){
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID do Pedido</th>
            <th>Data</th>
            <th>Total</th>
            <th>Ações</th>
            <th>Ações</th>
         </tr>";

    foreach($pedidos as $pedido){
        echo "<tr>
            <td>{$pedido['id']}</td>
            <td>{$pedido['data']}</td>
            <td>R$ ". number_format($pedido['total'], 2, ',', '.')."</td>
            <td>{$pedido['status']}</td>
            <td>
                <a href='ver_itens_pedido.php?id={$pedido['id']}'>Ver Itens</a>
            </td>
        </tr>";
    }

    echo "</table>";
}else{
    echo "<p>Você ainda não fez nenhum pedido.</p>";
}

?>

</body>
</html>
