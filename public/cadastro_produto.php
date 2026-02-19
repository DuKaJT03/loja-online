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
    <title>Cadastro de Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Cadastro de Produto</h2>

<form action="processa_produto.php" method="POST" enctype="multipart/form-data"> <!-- Obirgatório quando você envia arquivos, como imagens. -->
    <label for="nome">Nome do Produto</label> <!-- for="":Ligado ao campo com id="nome". -- Label=Rotuloo descritivo-->
    <input type="text" id="nome" name="nome" required><br><br> <!--ID:Identificador interno(ajuda em CSS/JS e label)-->
                        <!-- name="":Nome do dado que vai ser enviado pro PHP ($_POST['nome']).-->
    <label for="descricao">Descrição</label><br>
    <textarea name="descricao" id="descricao" rows="4" cols="50"></textarea><br><br><!--TEXTEREA:área de texto grande-->
                                        <!--rows: 4 linhas de altura // cols: 50 colunas de largura-->
    <label for="preco">Preço (Ex: 99.90):</label>
    <input type="number" id="preco" name="preco" step="0.01" required><br><br><!--step: permite casas decimais (centavos)-->

    <label for="estoque">Estoque:</label>
    <input type="number" id="estoque" name="estoque" required><br><br>

    <label for="imagem">Imagem de Produto:</label>
    <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>
            <!-- type="file": Permite selecionar um arquivo(imagem) // accept="image/*" :Só permite escolher arquivos de imagem(png, jpg,jpeg, gif...)-->

    <label>Categoria:</label>
    <select name="categoria" required>
        <option value="camiseta">Camiseta</option>
        <option value="calca">Calça</option>
        <option value="tenis">Tênis</option>
    </select><br><br>
    
    <input type="submit" value="Cadastrar Produto">
</form>
    
</body>
</html>