<?php
session_start();

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente'){
    header('Location: login.html');
    exit;
}

//Verifica se o carrinho existe e é um array
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])){
    header('Location: ver_carrinho.php');
    exit;
}

//Verifica se o ID foi enviado
if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    //Remove apenas se o item existir no carrinho
    if(isset($_SESSION['carrinho'][$id])){
        unset($_SESSION['carrinho'][$id]);
        //unset() :função que remove um item do array
    }
}

header('Location: ver_carrinho.php');
exit;
?>
