<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

// Verifica se está logado e se é cliente
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'cliente'){
    header('Location: login.html');
    exit;
}

// Verifica se recebeu o ID do pedido pela URL
if(!isset($_GET['id'])){
    echo "Pedido não especificado.";
    exit;
}

$id_pedido = intval($_GET['id']);
$id_cliente = $_SESSION['usuario_id'];

$conexao = Conexao::conectar();

//Verifica se o pedido pertence ao cliente
$stmt_pedido = $conexao->prepare(
    "SELECT id, data, total
    FROM pedidos
    WHERE id = ? AND id_cliente = ?"
);
$stmt_pedido->bind_param("ii", $id_pedido, $id_cliente);
$stmt_pedido->execute();
$resultado_pedido = $stmt_pedido->get_result();

if($resultado_pedido->num_rows !== 1){
    echo "Pedido não encontrado ou acesso negado.";
    exit;
}

$pedido = $resultado_pedido->fetch_assoc();

// Busca os itens do pedido
$stmt_itens = $conexao->prepare(
    "SELECT i.quantidade, i.preco, p.nome        
    FROM itens_pedido i
    INNER JOIN produtos p ON p.id = i.id_produto
    WHERE i.id_pedido = ?"
);
if(!$stmt_itens){
    die("Erro no prepare: " . $conexao->error);
}
$stmt_itens->bind_param("i", $id_pedido);
$stmt_itens->execute();
$resultado_itens = $stmt_itens->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Itens do Pedido</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Itens do Pedido nº <?= $pedido['id']; ?> - <?= $pedido['data']; ?></h2>

<a href="ver_pedidos.php">Voltar</a><br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Produto</th>
        <th>Preço Unitário</th>
        <th>Quantidade</th>
        <th>Subtotal</th>
    </tr>

<?php
$total = 0;

if($resultado_itens->num_rows > 0){
    while($item = $resultado_itens->fetch_assoc()){
        $subtotal = $item['preco'] * $item['quantidade'];
        $total += $subtotal;

        echo "<tr>
            <td>{$item['nome']}</td>
            <td>R$ ".number_format($item['preco'], 2, ',', '.')."</td>
            <td>{$item['quantidade']}</td>
            <td>R$ ".number_format($subtotal, 2, ',', '.')."</td>
        </tr>";
    }

    echo "<tr>
            <td colspan='3' align='right'><strong>Total:</strong></td>
            <td><strong>R$ ".number_format($total, 2, ',', '.')."</strong></td>
          </tr>";
}else{
    echo "<tr><td colspan='4'>Nenhum item neste pedido.</td></tr>";
}
?>
</table>

</body>
</html>

<?php $conexao->close();?>
