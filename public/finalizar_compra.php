<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

// Verifica se o cliente está logado (PROTEÇÃO DE ROTA)
if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'cliente'){
    header('Location: login.html');
    exit;
}

// Verifica se o carrinho existe e não está vazio
if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) === 0){
    echo "<p style='color:red;'>Seu carrinho está vazio!</p>";
    echo "<a href='ver_produtos.php'>Voltar</a>";
    exit;
}

$conexao = Conexao::conectar();
$conexao->begin_transaction();

try {

    $id_cliente = $_SESSION['usuario_id'];
    $total = 0;

    // Calcula o total REAL (preço vem do banco)
    foreach($_SESSION['carrinho'] as $item){

        $stmt = $conexao->prepare(
            "SELECT preco, estoque FROM produtos WHERE id = ?"
        );
        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if($resultado->num_rows !== 1){
            throw new Exception("Produto inválido.");
        }

        $produto = $resultado->fetch_assoc();
        
        if ($produto['estoque'] < $item['quantidade']) {
            throw new Exception("Estoque insuficiente para o produto ID {$item['id']}.");
        }

        $subtotal = $produto['preco'] * $item['quantidade']; // as $item → Cada item individual, representado por essa variável $item.
        $total += $subtotal;
    }

    // Insere na tabela PEDIDOS - Cria o pedido
    $stmt_pedido = $conexao->prepare(
        "INSERT INTO pedidos (id_cliente, total, status) 
        VALUES (?, ?, 'pendente')"
    );
    $stmt_pedido->bind_param("id", $id_cliente, $total);
    $stmt_pedido->execute();

    //Pega o número do pedido que acabou de ser salvo
    $id_pedido = $stmt_pedido->insert_id; //Pega o último ID que foi criado no banco, esse é o número do pedido

// Insere cada item na tabela ITENS_PEDIDO e atualiza estoque
    foreach($_SESSION['carrinho'] as $item){//foreach :Percorre cada item do carrinho 

        $stmt_produto = $conexao->prepare(
            "SELECT preco, estoque FROM produtos WHERE id = ?"
        );
        $stmt_produto->bind_param("i", $item['id']);
        $stmt_produto->execute();
        $produto = $stmt_produto->get_result()->fetch_assoc();

        $id_produto = $item['id'];
        $quantidade = $item['quantidade'];
        $preco = $produto['preco'];

        $stmt_item = $conexao->prepare(
            "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco)
            VALUES (?, ?, ?, ?)"
        );
        $stmt_item->bind_param(
            "iiid",
            $id_pedido,
            $id_produto,
            $quantidade,
            $preco
        );
        $stmt_item->execute();

        //Atualiza estoque
        $stmt_update = $conexao->prepare(
            "UPDATE produtos SET estoque = estoque - ? WHERE id = ?"
        );
        $stmt_update->bind_param("ii", $quantidadem, $id_produto);
        $stmt_update->execute();
    }

    //Finaliza tudo
    $conexao->commit();
    unset($_SESSION['carrinho']);

    echo "<p style='color:green;'>Compra finaliza com sucesso! Pedido nº $id_pedido</p>";
    echo "<a href='ver_produtos.php'>Continuar comprando</a> |
        <a href='painel_cliente.php'>Voltar ao painel</a>";

}catch (Exception $e){
    
    $conexao->rollback();
    echo "<p style='color:red;'>Erro ao finalizar compra: {$e->getMessage()}</p>";
}

$conexao->close();

?>
