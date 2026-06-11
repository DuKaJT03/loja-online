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
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/painel_cliente.css">
</head>
<body>
    <div class="container painel-page">
        <div class="painel-header">
            <h1>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
            <p>Escolha uma opção</p>
        </div>

        <div class="painel-grid">
            <a href="ver_produtos.php">
                <div class="painel-card">
                    <div class="painel-icon">
                        🛍️
                    </div>
                    <h3>Produtos</h3>
                </div>
            </a>

            <a href="ver_carrinho.php">
                <div class="painel-card">
                    <div class="painel-icon">
                        🛒
                    </div>
                    <h3>Carrinho</h3>
                </div>
            </a>

            <a href="ver_pedidos.php">
                <div class="painel-card">
                    <h3>Ver Pedidos</h3>
                </div>
            </a>

            <a href="logout.php">
                <div class="painel-card">
                    <h3>Sair</h3>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
