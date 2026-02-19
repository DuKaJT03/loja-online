<?php
session_start(); //inicia a sessão, se esquecer, não funciona

//Verifica se o usuário está logado e se é cliente
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !=  'cliente'){
    //Se NÂO estiver logado ou NÂO for cliente, volta pro login
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Cliente</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
    
    <ul> <!--Lista não ordenada(pontinhos) -->
        <li><a href="ver_produtos.php">Ver Produtos</a></li> <!-- <li> item da lista -->
        <li><a href="ver_carrinho.php">Ver Carrinho</a></li>
        <li><a href="logout.php">Sair</a></li>
        <li><a href="ver_pedidos.php">Ver Pedidos</a></li>
    </ul>
</body>
</html>
