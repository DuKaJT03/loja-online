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

$pdo = Conexao::conectar();

//Verifica se o pedido pertence ao cliente
$stmt_pedido = $pdo->prepare(
    "SELECT id, data, total
    FROM pedidos
    WHERE id = :id_cliente AND id_cliente = :cliente"
);
$stmt_pedido->bindValue(':pedido', $id_pedido, PDO::PARAM_INT);
$stmt_pedido->bindValue(':cliente', $id_cliente, PDO::PARAM_INT);
$stmt_pedido->execute();

$pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

if(!$pedido){
    echo "Pedido não encontrado ou acesso negado.";
    exit;
}

// Busca os itens do pedido
$stmt_itens = $pdo->prepare(
    "SELECT i.quantidade, i.preco, p.nome        
    FROM itens_pedido i
    INNER JOIN produtos p ON p.id = i.id_produto
    WHERE i.id_pedido = :pedido"
);

$stmt_itens->bindValue(':pedido', $id_pedido, PDO::PARAM_INT);
$stmt_itens->execute();

$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
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

if(count($itens) > 0){
    foreach($itens as $item){
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