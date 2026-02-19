<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id'])|| $_SESSION['usuario_tipo'] != 'lojista'){
    header('Location: login.html');
    exit;
}

if(isset($_GET['id'])){//Verifica se foi passado o parâmetro id pela URL, exemplo: excluir_produto.php?id=5
    $id = intval($_GET['id']);//pega e garante que é um número inteiro(proteção básica).
    $lojista_id = $_SESSION['usuario_id'];

    //Verifica se o produto existe e pertence a esse lojista
    $sql = "SELECT * FROM produtos WHERE id = $id AND id_lojista = $lojista_id";
    $conexao = Conexao::conectar();
    $resultado = $conexao->query($sql);//executa a query no banco

    if($resultado->num_rows == 1){ //Verifica se encontrou exatamente um produto com esse ID e que pertence ao lojista
        $produto = $resultado->fetch_assoc();//pega os dados do produto (em formato array associativo)
    
        //Se tiver imagem, apaga a imagem do servidor
        if($produto['imagem'] != '' && file_exists($produto['imagem'])){
            unlink($produto['imagem']); //Apaga fisicamente o arquivo da imagem da pasta
        }

        //Apaga o produto do banco
        $sql_delete = "DELETE FROM produtos WHERE id = $id AND id_lojista = $lojista_id";

        if($conexao->query($sql_delete)==TRUE){
            echo "Produto excluido com sucesso!";
            echo "<br><a href='lista_produtos.php'>Voltar para lista de produtos</a>";
        }else{
            echo "Erro ao Excluir: ".$conexao->error;
        }
    }else{
        echo "Produto não encontrado ou você não tem permissão para excluir.";
    }
}else{
    echo "ID do produto não especificado.";
}

$conexao->close();
?>