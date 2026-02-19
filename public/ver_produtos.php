<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}

$conexao = Conexao::conectar();

//Captura da categoria (URL)
$categoria = $_GET['categoria'] ?? '';

//SQL base
$sql = "
    SELECT id, nome, descricao, preco, estoque, imagem
    FROM produtos
    WHERE estoque > 0
";

// Tipos e valores 
$tipos = '';
$valores =[];

// Filtro por categoria (opcional)
if($categoria !== ''){
    $sql .= "AND categoria = ?";
    $tipos .= "s";
    $valores[] = $categoria;
}

//prepara
$stmt = $conexao->prepare($sql);

//Bind dinâmico (se houver parâmetros)
if(!empty($valores)){
    $stmt->bind_param($tipos, ...$valores);
}

//Executa
$stmt->execute();

//$resultado
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja - Ver Produtos</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/carrinho.js?v=2"></script>
</head>
<body>

<h2>Produtos Disponíveis</h2>

<a href="painel_cliente.php">Voltar para o painel</a><br><br>

<form method="get">
    <select name="categoria">
        <option value="">Todas as categorias</option>
        <option value="camiseta">Camiseta</option>
        <option value="calca">Calça</option>
        <option value="tenis">Tênis</option>
    </select>
    <input type="submit" value="Filtrar">
</form>

<table border="1" cellpadding="10"> <!--cellpadding="10" : espaçamento interno nas células -->
    <tr> <!--Linnha da tabela-->
        <th>Imagem</th>
        <th>Nome</th><!--Cabeçalho da tebela-->
        <th>Descrição</th>
        <th>Preço</th>
        <th>Estoque</th>
        <th>Ações</th>
    </tr>

<?php //Listagem
if($resultado->num_rows > 0){ //Conta quantas linhas vieram do banco
    while($produto = $resultado->fetch_assoc()){
        echo "<tr>"; //Começa uma linha da tabela

        //Coluna da imagem
        if($produto['imagem'] != ''){
            echo "<td><img src='".$produto['imagem']."' width='100'></td>";
        } else {
            echo "<td>Sem imagem</td>";
        }
        //outras colunas 
        echo "<td>".$produto['nome']."</td>";
        echo "<td>".$produto['descricao']."</td>";
        echo "<td>R$ ".number_format($produto['preco'], 2, ',', '.')."</td>";
        echo "<td>".$produto['estoque']."</td>";

        //Botão de adicionar no carrinho 
        echo "<td>
                <button class='add_carrinho' data-id='".$produto['id']."'>Adicionar ao Carrinho</button>
             </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>";
}
?>

</table>

</body>
</html>

<?php
$conexao->close();
?>
