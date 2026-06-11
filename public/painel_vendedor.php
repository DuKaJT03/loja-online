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
 $pdo = Conexao::conectar();
    $id = $_SESSION['usuario_id'];
    
    $sql =
        "SELECT COUNT(*) as total FROM produtos WHERE id_lojista = :id_lojista";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_lojista', $id, PDO::PARAM_INT);
    $stmt->execute();

    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Vendedor</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/painel_vendedor.css">
</head>

    <div class="painel-page">
        <div class="painel-header">
            <h1>
                Painel do Vendedor
            </h1>

            <a href="logout.php" class="btn btn-danger">
                Sair
            </a>
        </div>

        <div class="dashboard-card">
            <h2>
                Total de Produtos
            </h2>

            <span class="dashboard-number">
                <?= $total ?>
            </span>
        </div>

        <div class="menu-grid">
            <a href="cadastro_produto.php" class="menu-card">
                <h3>Cadastrar Produto</h3>
                <p>
                    Adicione novos produtos à loja.
                </p>
            </a>

            <a href="lista_produtos.php" class="menu-card">
                <h3>Meus Produtos</h3>
                <p>
                    Gerencie seu catálogo.
                </p>
            </a>

            <a href="ver_vendas_lojista.php" class="menu-card">
                <h3>Pedidos</h3>
                <p>Visualize as vendas realizadas.</p>
            </a>
        </div>
    </div>

</html>