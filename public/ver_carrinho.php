<?php
session_start();

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">

    <title>Seu Carrinho</title>

    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/carrinho.css">

</head>

<body>

<div class="container carrinho-page">
    <div class="page-header">
        <h1>Seu Carrinho</h1>
        <div class="header-actions">
            <a href="ver_produtos.php" class="btn btn-primary">
                Continuar Comprando
            </a>
            <a href="painel_cliente.php" class="btn btn-secondary">
                Voltar ao Painel
            </a>
        </div>
    </div>

<?php

if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) === 0){

?>

    <div class="carrinho-vazio">
        <h2>
            Seu carrinho está vazio
        </h2>
        <p>
            Adicione alguns produtos para continuar.
        </p>
    </div>

<?php

}else{

    $total = 0;

    foreach($_SESSION['carrinho'] as $item){

        if(
            !isset(
                $item['id'],
                $item['nome'],
                $item['preco'],
                $item['quantidade']
            )
        ){
            continue;
        }

        $subtotal =
            $item['preco']
            *
            $item['quantidade'];

        $total += $subtotal;

?>

    <div class="carrinho-item">
        <div class="carrinho-info">
            <h3>
                <?=
                htmlspecialchars($item['nome'])
                ?>
            </h3>

            <p class="carrinho-preco">
                R$
                <?=
                number_format($item['preco'], 2, ',', '.')
                ?>
            </p>

            <p>
                Quantidade:
                <strong>
                    <?=
                    (int)$item['quantidade']
                    ?>
                </strong>
            </p>
            <p>
                Subtotal:
                <strong>
                    R$
                    <?= 
                        number_format($subtotal,2,',','.')
                    ?>
                </strong>
            </p>

        </div>

        <div class="carrinho-acoes">

            <a
                href="remover_carrinho.php?id=<?=(int)$item['id']?>"
                class="btn btn-danger"
                onclick="
                    return confirm(
                        'Remover este item do carrinho?'
                    );
                "
            >
                Remover
            </a>

        </div>

    </div>

<?php

    }

?>

    <div class="carrinho-resumo">

        <h2>
            Total da Compra
        </h2>

        <p class="carrinho-total">

            R$
            <?=
            number_format(
                $total,
                2,
                ',',
                '.'
            )
            ?>

        </p>

        <a
            href="finalizar_compra.php"
            class="btn btn-success"
        >
            Finalizar Compra
        </a>

    </div>

<?php

}

?>

</div>

</body>
</html>