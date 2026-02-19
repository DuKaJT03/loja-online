<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

// Verifica se o usuário está logado e se é vendedor
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !='lojista'){
//NÃO existe a variável 'usuario_id' na sessão (usuario não esta logado) || Se p tipo de usuário da sessão NÃO for vendedor
    //"Se o usuário NÃO está logado (!isset($_SESSION['usuario_id'])) ou se ele está logado mas NÃO é vendedor ($_SESSION['usuario_tipo'] != 'vendedor'), então execute o que está dentro do bloco."
    header('Location: login.html'); //Header, Função do PHP que envia um cabeçalho HTTP para o navegador.
     //Isso faz o navegador sair da página atual e abrir automaticamente o login.html.
    exit;
}
 $conexao = Conexao::conectar();
    $id = $_SESSION['usuario_id'];
    
    $stmt = $conexao->prepare(
        "SELECT COUNT(*) as total FROM produtos WHERE id_lojista = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $total = $resultado->fetch_assoc()['total'];

    $stmt->close();
    $conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Vendedor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Bem-vindo, Vendedor <?php echo $_SESSION['usuario_nome']; ?>!</h1>

    <p style="color:green;">
        <strong>Total de Produtos:</strong> <?php echo $total; ?>
    </p>
   
    <ul>
        <li><a href="cadastro_produto.php">Cadastrar Produto</a></li>
        <li><a href="lista_produtos.php">Meus Produtos</a></li>
        <li><a href="ver_vendas_lojista.php">Pedidos</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</body>
</html>