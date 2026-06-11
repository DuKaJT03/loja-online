<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

// Verifica se o cliente está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'cliente'){
    header('Location: login.html');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

$pdo = Conexao::conectar();

// Busca todos os pedidos do cliente
$sql =
    "SELECT id, data, total, status
    FROM pedidos
    WHERE id_cliente = :id_cliente
    ORDER BY data DESC
    ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt->execute();

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/pedidos.css">
</head>
<body>

<div class="container pedidos-page">
    <div class="page-hader">
        <h1>Meus Pedidos</h1>
        <a href="painel_cliente.php" class="btn btn-secondary">
            Voltar ao Inicio
        </a>
    </div>
</div>

<?php
    if(count($pedidos)>0){
?>
<div class="table-container">
    <table class="pedidos-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($pedidos as $pedido){ ?>
                <tr>
                    <td>
                        <?= $pedido['id'] ?>
                    </td>

                    <td>
                        <?= (new DateTime($pedido['data']))->format('d/m/y') ?>
                    </td>

                    <td>
                        R$
                        <?=number_format($pedido['total'],2,',','.')?>
                    </td>

                    <td>
                        <?php $status = strtolower($pedido['status']); ?>
                        <span class="status-badge status-<?= $status ?>">
                            <?= htmlspecialchars($pedido['status']) ?>
                        </span>
                    </td>

                    <td>
                        <a href="ver_itens_pedido.php?id=<?= $pedido['id'] ?>"
                            class="btn btn-primary">
                            Ver Itens
                        </a>
                    </td>
                </tr>
            <?php } ?>    
        </tbody>
            <?php
                if(isset($_SESSION['sucesso'])){
            ?>

                <div class="alert-success">
                    <?= htmlspecialchars($_SESSION['sucesso']) ?>
                </div>
            <?php
                unset($_SESSION['sucesso']);
            }
            ?>
    </table>
</div>
<?php
    }else{
?>
<p>
    Você ainda não fez nenhum pedido.
</p>
<?php
    }
?>

</body>
</html>
