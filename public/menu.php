Não utilizado em nada era só um redicionador.
<?php
session_start();
?>

<nav>
<ul>
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <li>Olá, <?php echo $_SESSION['usuario_nome']; ?></li>
        <?php if($_SESSION['usuario_tipo'] == 'lojista'): ?>
            <li><a href="painel_vendedor.php">Painel do Lojista</a></li>
            <li><a href="cadastro_produto.php">Cadastrar Produto</a></li>
            <li><a href="lista_produtos.php">Meus Produtos</a></li>
        <?php elseif($_SESSION['usuario_tipo'] == 'cliente'): ?>
            <li><a href="painel_cliente.php">Painel do Cliente</a></li>
            <li><a href="ver_produtos.php">Ver Produtos</a></li>
            <li><a href="ver_carrinho.php">Carrinho</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Sair</a></li>
    <?php else: ?>
        <li><a href="index.html">Cadastro</a></li>
        <li><a href="login.html">Login</a></li>
        <li><a href="loja.php">Ver Produtos</a></li>
    <?php endif; ?>
</ul>
</nav>
