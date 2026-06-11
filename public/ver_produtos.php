<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}

$pdo = Conexao::conectar();

//Captura da categoria (URL)
$categoria = $_GET['categoria'] ?? '';

//SQL base
$sql = "
    SELECT id, nome, descricao, preco, estoque, imagem
    FROM produtos
    WHERE estoque > 0
";

$params = [];

// Filtro por categoria (opcional)
if($categoria !== ''){
    $sql .= "AND categoria = :categoria";
    $params[':categoria'] = $categoria;
}

//prepara
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja - Ver Produtos</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/produtos.css">
    <script src="js/carrinho.js?v=2"></script>
</head>
<body>

<div class="produtos-page">
    <div class="produtos-header">
        <h1>Produtos Disponíveis</h1>
        <a
            href="painel_cliente.php"
            class="btn btn-secondary"
        >
            Voltar ao Painel
        </a>
    </div>

    <form
        method="get"
        class="filtro-form"
    >

        <select name="categoria">

            <option value="">
                Todas as categorias
            </option>

            <option value="camiseta">
                Camiseta
            </option>

            <option value="calca">
                Calça
            </option>

            <option value="tenis">
                Tênis
            </option>

        </select>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Filtrar
        </button>

    </form>

    <div class="produtos-grid">

        <?php if(count($produtos) > 0){ ?>

            <?php foreach($produtos as $produto){ ?>

                <div class="produto-card">

                    <div class="produto-imagem">

                        <?php if(!empty($produto['imagem'])){ ?>
                        
                            <img
                                src="<?= htmlspecialchars($produto['imagem']) ?>"
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                            >

                        <?php } else { ?>

                            <div class="sem-imagem">
                                Sem imagem
                            </div>

                        <?php } ?>

                    </div>

                    <div class="produto-info">

                        <h3>
                            <?= htmlspecialchars($produto['nome']) ?>
                        </h3>

                        <p class="produto-descricao">
                            <?= htmlspecialchars($produto['descricao']) ?>
                        </p>

                        <p class="produto-preco">
                            R$
                            <?= number_format(
                                $produto['preco'],
                                2,
                                ',',
                                '.'
                            ) ?>
                        </p>

                        <p class="produto-estoque">
                            Estoque:
                            <?= $produto['estoque'] ?>
                        </p>

                        <button
                            class="btn btn-primary add_carrinho"
                            data-id="<?= $produto['id'] ?>"
                        >
                            Adicionar ao Carrinho
                        </button>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>
            <p>
                Nenhum produto encontrado.
            </p>
        <?php } ?>
    </div>
</div>
</body>
</html>

