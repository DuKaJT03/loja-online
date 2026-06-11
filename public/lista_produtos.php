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

$pdo = Conexao::conectar();
//SQL base (NUNCA concatenar variável direto)
$sql = "
    SELECT id, nome, descricao, preco, imagem 
    FROM produtos 
    WHERE id_lojista = :lojista
    ";
$params = [
    ':lojista' =>$lojista_id
];

// Se houver busca, adiciona condição
if ($busca !== ''){
    $sql .= "AND (nome ILIKE :busca OR descricao ILIKE :busca)";
    $params[':busca'] = "%$busca%";
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Produtos</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/lista_produtos.css">
</head>
<body>
<?php
if(isset($_GET['mensagem'])){//Verifica se existe na URL um parâmetro chamado mensagem
    echo "<p style='color: green; font-weight:bold;'>".htmlspecialchars($_GET['mensagem'])."</p>"; //font-weight:bold :Deixa em negrito
}
?>

<div class="lista-produtos-page">

    <div class="page-header">
        <h1>Meus Produtos</h1>

        <div class="header-actions">
            <a href="cadastro_produto.php" class="btn btn-primary">
                + Novo Produto
            </a>

            <a href="painel_vendedor.php" class="btn btn-secondary">
                Voltar
            </a>
        </div>
    </div>

    <form class="busca-form" method="get">

        <input
            type="text"
            name="busca"
            placeholder="Buscar produto..."
            value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
        >

        <button
            type="submit"
            class="btn btn-primary"
        >
            Buscar
        </button>

    </form>

    <div class="table-container">

        <table class="produtos-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php if(count($produtos) > 0){ ?>

                    <?php foreach($produtos as $produto){ ?>

                        <tr>

                            <td>
                                <?= $produto['id'] ?>
                            </td>

                            <td>

                                <?php if(!empty($produto['imagem'])){ ?>

                                    <img
                                        src="<?= htmlspecialchars($produto['imagem']) ?>"
                                        class="produto-thumb"
                                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                                    >

                                <?php }else{ ?>

                                    <span class="sem-imagem">
                                        Sem imagem
                                    </span>

                                <?php } ?>

                            </td>

                            <td>
                                <?= htmlspecialchars($produto['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($produto['descricao']) ?>
                            </td>

                            <td class="preco">

                                R$

                                <?= number_format(
                                    $produto['preco'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </td>

                            <td>

                                <div class="acoes">

                                    <a
                                        href="editar_produto.php?id=<?= $produto['id'] ?>"
                                        class="btn btn-primary"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="excluir_produto.php?id=<?= $produto['id'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir?');"
                                    >
                                        Excluir
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php } ?>

                <?php }else{ ?>

                    <tr>
                        <td colspan="6">

                            Nenhum produto cadastrado.

                        </td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>
</body>
</html>
