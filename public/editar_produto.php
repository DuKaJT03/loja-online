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

//Query para buscar o produto
$sql = "SELECT * FROM produtos WHERE id = $id_produto AND id_lojista = ".$_SESSION['usuario_id'];//Busca o produto com aquele id e que pertença ao lojista logado
$conexao = Conexao::conectar();
$resultado = $conexao->query($sql); //Executa a query.
//Verifica se encontrou 1 produto
if($resultado->num_rows != 1){
    echo "Produto não encontrado oou você não tem permissão.";
    exit;
}
//pega os dados do produto
$produto = $resultado ->fetch_assoc();//Transforma em array associativo $produto['nome'],$produto['descricao'],etc.
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Editar Produto</h2>
                                    <!--METHOD:dados vão ocultos no corpo da requisição-->
<form action="processa_edita_produto.php" method="post" enctype="multipart/form-data"><!--ENCTYPE: permite envio de arquivos(imagem)-->
    <input type="hidden" name="id" value="<?php echo $produto['id']; ?>"><!--Esse ID é essencial pro processador saber qual produto atualizar-->

    <label>Nome:</label>
    <input type="text" name="nome" value="<?php  echo htmlspecialchars($produto['nome']); ?>" required><br><br><!--HTMLSPECIALCHARS(): Protege contra XSS(inserção de códigos maliciosos)-->

    <label>Descrição:</label>
    <textarea name="descricao" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea><br><br><!--Carrega a descrição atual dentro da <textarea>-->

    <label>Preço</label><!--step:permite valores com 2 casas decimais 9.99-->
    <input type="number" step="0.01" name="preco" value="<?php echo $produto['preco']; ?>" required><br><br>

    <label>Imagem Atual</label><br><!--Se não enviar, mantém a imagem antiga-->
    <img src="<?php echo $produto['imagem']; ?>" width="150"><br><br>

    <label>Trocar imagem (opcional):</label>
    <input type="file" name="imagem"><br><br>

    <label>Estoque: </label>
    <input type="number" name="estoque" value="<?php  echo $produto ['estoque']; ?>" required><br><br>

    <input type="submit" value="Salvar Alterações">
</form>

<br>
<a href="lista_produtos.php">Voltar</a>
    
</body>
</html>