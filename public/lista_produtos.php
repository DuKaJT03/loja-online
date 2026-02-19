<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'lojista'){
    header('Location: login.html');
    exit;
}

//Captura o ID do lojista que está logado
$lojista_id = $_SESSION['usuario_id'];

//captura da busca (vem da URL)
$busca = $_GET['busca'] ?? '';

$conexao = Conexao::conectar();
//SQL base (NUNCA concatenar variável direto)
$sql = "
    SELECT id, nome, descricao, preco, imagem 
    FROM produtos 
    WHERE id_lojista = ?
    ";

// Tipos e valores para o bind 
$tipos = "i";
$valores = [$lojista_id];

// Se houver busca, adiciona condição
if ($busca !== ''){
    $sql .= "AND (nome LIKE ? OR descricao LIKE ?)";
    $tipos .= "ss";
    $valores[] = "%$busca%";
    $valores[] = "%$busca%";
}

// Prepara a query
$stmt = $conexao->prepare($sql);

// Bind dinâmico dos parâmetros
$stmt->bind_param($tipos, ...$valores);

//executa
$stmt->execute();

//Resultado
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
if(isset($_GET['mensagem'])){//Verifica se existe na URL um parâmetro chamado mensagem
    echo "<p style='color: green; font-weight:bold;'>".htmlspecialchars($_GET['mensagem'])."</p>"; //font-weight:bold :Deixa em negrito
}
?>

<h2>Meus Produtos</h2>

<a href="cadastro_produto.php">+ Adicionar Novo Produto</a>
<a href="painel_vendedor.php">Voltar para o painel</a><br><br>

<form action="" method="get"><!--Formulário que envia dados pela URL (GET)|| action"" =envia pra própia página-->
<!-- name="busca": Define o nome do dado || value: mantém o texto digitando após buscar -->
    <input type="text" name="busca" placeholder="Buscar produto..." 
    value="<?php echo isset($_GET['busca']) ? $_GET['busca'] : '';?>">
    <input type="submit" value="Buscar">
</form>

<!-- Começo da tabela-->
 <table border="1" cellpadding="10"><!--Cria tabela com borda-->
    <tr><!--Cria uma linha-->
        <th>ID</th><!--Cabeçalho de cada coluna(ID, Nome..)-->
        <th>Nome</th>
        <th>Descrição</th>
        <th>Preço</th>
        <th>Imagem</th>
        <th>Ações</th>
    </tr>

<?php
//Percorre todos os produtos e gera as linhas da tabela
if($resultado->num_rows > 0){//num_rows: quantidade de linhas retornas pela coluna
   //Enquanto houver produtos, ele pega cada um.
    while ($produto = $resultado->fetch_assoc()){//fetch_assoc():Transforma cada linha em um ARRAY ASSOCIATIVO, onde os índices são os nomes do campos do banco
        echo "<tr>";
        echo "<td>".$produto['id']."</td>";//<td> Cria cada célula na linha
        echo "<td>".$produto['nome']. "</td>";//valor da coluna nome daquele produto
        echo "<td>".$produto['descricao']. "</td>";
        echo "<td>R$". number_format($produto['preco'], 2, ',', '.')."</td>";
                        //2 duas casas decimais|,separador decimal|.separador de milhar
        if($produto['imagem'] != ''){
        echo "<td><img src='".$produto['imagem']."' width='100'></td>";//Exibe a imagem do produto na tabela, redimensionadoo para largura 100px
        }else{
            echo "<td>Sem Imagem</td>";
        }
        //Botões de editar e excluir, passando o ID do produto na URL
        echo "<td>
                <a href='editar_produto.php?id=".$produto['id']."'>Editar</a> 
                <a href='excluir_produto.php?id=".$produto['id']."' onclick=\"return confirm('Tem certeza que deseja excluir?');\">Excluir</a>
             </td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='6'>Nenhum produto cadastrado ainda.</td>";
}
?>
</table>

</body>
</html>

<?php
$conexao->close();
?>