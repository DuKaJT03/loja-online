<?php
session_start();

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Seu Carrinho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Seu Carrinho</h2>

<a href="ver_produtos.php">Continuar Comprando</a> | 
<a href="painel_cliente.php">Voltar para o Painel</a><br><br>

<?php
if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0){
    echo "<p>Seu carrinho está vazio.</p>";
}else{

    echo "<a href='finalizar_compra.php'>Finalizar Compra</a><br><br>";

    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>Produto</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
            <th>Ações</th>
         </tr>";

    $total = 0;

    //Loop que percorre todos os produtos no carrinho 
    foreach($_SESSION['carrinho'] as $item){

        //Garantia mínima de integridade
        if(!isset($item['id'], $item['nome'], $item['preco'], $item['quantidade'])){
            continue;
        }

        $subtotal = $item['preco'] * $item['quantidade'];
        $total += $subtotal;

        echo "<tr>";
        echo "<td>". htmlspecialchars($item['nome']) ."</td>";
        echo "<td>R$ ". number_format($item['preco'], 2, ',', '.')."</td>";
        echo "<td>". (int)$item['quantidade']."</td>";
        echo "<td>R$ ". number_format($subtotal, 2, ',', '.')."</td>";
        echo "<td>
                <a href='remover_carrinho.php?id=".(int)$item['id']."'
                    onclick=\"return confirm('Remover este item do carrinho?');\">
                    Remover
                </a>
            </td>";
        echo "</tr>";
    }

    echo "<tr>
            <td colspan='3' align='right'><strong>Total:</strong></td>
            <td><strong>R$ ".number_format($total, 2, ',', '.')."</strong></td>
            <td></td>
          </tr>";
    echo "</table>";
}
?>

</body>
</html>
