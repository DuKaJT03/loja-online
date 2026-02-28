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

    $pdo = Conexao::conectar();

    //Verifica se o produto existe e pertence a esse lojista
    $sql = "SELECT * FROM produtos WHERE id = :id AND id_lojista = :lojista";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':lojista', $lojista_id, PDO::PARAM_INT);
    $stmt->execute();

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if($produto){ //Verifica se encontrou exatamente um produto com esse ID e que pertence ao lojista
  
        //Se tiver imagem, apaga a imagem do servidor
        if($produto['imagem'] != '' && file_exists($produto['imagem'])){
            unlink($produto['imagem']); //Apaga fisicamente o arquivo da imagem da pasta
        }

        //Apaga o produto do banco
        $sql_delete = 
            "DELETE FROM produtos 
            WHERE id = :id 
            AND id_lojista = :lojista";

        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt_delete->bindValue(':lojista', $lojista_id, PDO::PARAM_INT);

        if($stmt_delete->execute()){
            echo "Produto excluido com sucesso!";
            echo "<br><a href='lista_produtos.php'>Voltar para lista de produtos</a>";
        }else{
            echo "Erro ao Excluir: ";
        }
    }else{
        echo "Produto não encontrado ou você não tem permissão para excluir.";
    }
}else{
    echo "ID do produto não especificado.";
}

?>