<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Jhon\Loja\Database\Conexao;

// Verifica se está logado e se é cliente
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}

// Verifica se recebeu o ID do pedido
if(!isset($_GET['id'])){
    echo "Pedido não especificado.";
    exit;
}

$id_pedido = (int) $_GET['id'];
$id_cliente = $_SESSION['usuario_id'];

$pdo = Conexao::conectar();

// Verifica se o pedido pertence ao cliente
$stmt_pedido = $pdo->prepare(
    "SELECT
        id,
        data,
        total
    FROM pedidos
    WHERE id = :pedido
    AND id_cliente = :cliente"
);

$stmt_pedido->bindValue(
    ':pedido',
    $id_pedido,
    PDO::PARAM_INT
);

$stmt_pedido->bindValue(
    ':cliente',
    $id_cliente,
    PDO::PARAM_INT
);

$stmt_pedido->execute();

$pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

if(!$pedido){
    echo "Pedido não encontrado ou acesso negado.";
    exit;
}

// Busca os itens do pedido
$stmt_itens = $pdo->prepare(
    "SELECT
        i.quantidade,
        i.preco,
        p.nome
    FROM itens_pedido i
    INNER JOIN produtos p
        ON p.id = i.id_produto
    WHERE i.id_pedido = :pedido"
);

$stmt_itens->bindValue(
    ':pedido',
    $id_pedido,
    PDO::PARAM_INT
);

$stmt_itens->execute();

$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">

    <title>Itens do Pedido</title>

    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/itens_pedido.css">

</head>

<body>

<div class="container pedido-page">

    <div class="page-header">

        <div>

            <h1>

                Pedido #<?= $pedido['id'] ?>

            </h1>

            <p>

                Data:
                <?= (new DateTime(
                    $pedido['data']
                ))->format('d/m/Y H:i') ?>

            </p>

        </div>

        <a
            href="ver_pedidos.php"
            class="btn btn-secondary"
        >
            Voltar
        </a>

    </div>

    <div class="table-container">

        <table class="pedido-table">

            <thead>

                <tr>

                    <th>Produto</th>

                    <th>
                        Preço Unitário
                    </th>

                    <th>
                        Quantidade
                    </th>

                    <th>
                        Subtotal
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php

                $total = 0;

                if(count($itens) > 0){

                    foreach($itens as $item){

                        $subtotal =
                            $item['preco']
                            *
                            $item['quantidade'];

                        $total += $subtotal;

                ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $item['nome']
                            ) ?>

                        </td>

                        <td>

                            R$

                            <?= number_format(
                                $item['preco'],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </td>

                        <td>

                            <?= $item['quantidade'] ?>

                        </td>

                        <td>

                            R$

                            <?= number_format(
                                $subtotal,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </td>

                    </tr>

                <?php

                    }

                ?>

                    <tr class="total-row">

                        <td colspan="3">

                            Total do Pedido

                        </td>

                        <td class="total-value">

                            R$

                            <?= number_format(
                                $total,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </td>

                    </tr>

                <?php

                }else{

                ?>

                    <tr>

                        <td colspan="4">

                            Nenhum item neste pedido.

                        </td>

                    </tr>

                <?php

                }

                ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>