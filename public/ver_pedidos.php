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

$conexao = Conexao::conectar();

// Busca todos os pedidos do cliente
$stmt = $conexao->prepare(
    "SELECT id, data, total, status
    FROM pedidos
    WHERE id_cliente = ?
    ORDER BY data DESC"
);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();
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
if($resultado->num_rows > 0){
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID do Pedido</th>
            <th>Data</th>
            <th>Total</th>
            <th>Ações</th>
            <th>Ações</th>
         </tr>";

    while($pedido = $resultado->fetch_assoc()){
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

$conexao->close();
?>

</body>
</html>
