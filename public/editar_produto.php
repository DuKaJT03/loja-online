<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id'])||$_SESSION['usuario_tipo'] != 'lojista'){
    header('Location: login.html');
    exit;
}

//Verifica se recebeu o ID via URL
if(!isset($_GET['id'])){//$_GET['id']:captura o valor do ID que veio pela URL(editar_produto.php?id=5)
    echo "Produto não especificado";
    exit;
}

$id_produto = intval($_GET['id']); //intval():Transforma o que veio na URL em número inteiro. (PROTEGE CONTRA SQL INJECTION).

$pdo = Conexao::conectar();
//Query para buscar o produto
$sql = "
    SELECT * 
    FROM produtos 
    WHERE id = :id 
    AND id_lojista = :lojista
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id_produto, PDO::PARAM_INT);
$stmt->bindValue(':lojista', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$produto){
    echo "Produto não encontrado ou você não tem permissão.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produto</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/editar_produto.css">
</head>
<body>

<div class="editar-produto-page">

    <div class="page-header">

        <h1>Editar Produto</h1>

        <a
            href="lista_produtos.php"
            class="btn btn-secondary"
        >
            Voltar
        </a>

    </div>

    <div class="form-card">

        <form
            action="processa_edita_produto.php"
            method="POST"
            enctype="multipart/form-data"
            class="produto-form"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $produto['id']; ?>"
            >

            <div class="form-group">

                <label for="nome">
                    Nome
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($produto['nome']); ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="descricao">
                    Descrição
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    rows="5"
                    required
                ><?= htmlspecialchars($produto['descricao']); ?></textarea>

            </div>

            <div class="form-group">

                <label for="preco">
                    Preço
                </label>

                <input
                    type="number"
                    id="preco"
                    name="preco"
                    step="0.01"
                    value="<?= $produto['preco']; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Imagem Atual
                </label>

                <?php if(!empty($produto['imagem'])){ ?>

                    <img
                        src="<?= htmlspecialchars($produto['imagem']); ?>"
                        alt="Produto"
                        class="preview-image"
                    >

                <?php }else{ ?>

                    <div class="sem-imagem">
                        Sem imagem cadastrada
                    </div>

                <?php } ?>

            </div>

            <div class="form-group">

                <label for="imagem">
                    Nova Imagem (opcional)
                </label>

                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                >

            </div>

            <div class="form-group">

                <label for="estoque">
                    Estoque
                </label>

                <input
                    type="number"
                    id="estoque"
                    name="estoque"
                    value="<?= $produto['estoque']; ?>"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Salvar Alterações
            </button>

        </form>

    </div>

</div>

</body>
</html>