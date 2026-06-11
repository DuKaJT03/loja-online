<?php
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'lojista'){// se não estiver logado OU Não for lojista, então..
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">

    <title>
        Cadastro de Produto
    </title>

    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/cadastro_produto.css">
</head>

<body>

<div class="cadastro-produto-page">
    <div class="produto-form-container">
        <div class="page-header">
            <h1>
                Cadastro de Produto
            </h1>

            <a
                href="painel_vendedor.php"
                class="btn btn-secondary"
            >
                Voltar
            </a>

        </div>

        <form
            action="processa_produto.php"
            method="POST"
            enctype="multipart/form-data"
            class="produto-form"
        >

            <div class="form-group">
                <label for="nome">
                    Nome do Produto
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
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
                ></textarea>

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
                    required
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
                    required
                >

            </div>

            <div class="form-group">

                <label for="imagem">
                    Imagem do Produto
                </label>

                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                    accept="image/*"
                >

            </div>

            <div class="form-group">

                <label for="categoria">
                    Categoria
                </label>

                <select
                    name="categoria"
                    id="categoria"
                    required
                >
                    <option value="">
                        Selecione
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

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Cadastrar Produto
            </button>

        </form>

    </div>

</div>

</body>
</html>