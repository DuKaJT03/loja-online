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

$pdo = Conexao::conectar();

try {

    //Inicia Transação
    $pdo->beginTransaction();

    $id_cliente = $_SESSION['usuario_id'];
    $total = 0;

    //Array para guardar dados já validados
    $produtosCarrinho = [];

    // Valida Produtos e Calcula o total REAL (preço vem do banco)
    foreach($_SESSION['carrinho'] as $item){

        $stmt = $pdo->prepare(
            "SELECT preco, estoque FROM produtos WHERE id = :id"
        );
        $stmt->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$produto){
            throw new Exception("Produto inválido (ID {$item['id']}).");
        }
        
        if ($produto['estoque'] < $item['quantidade']) {
            throw new Exception("Estoque insuficiente para o produto ID {$item['id']}.");
        }

        $subtotal = $produto['preco'] * $item['quantidade']; // as $item → Cada item individual, representado por essa variável $item.
        $total += $subtotal;

    //guarda dados já conferidos
    $produtosCarrinho[] = [
        'id' => $item['id'],
        'quantidade' => $item['quantidade'],
        'preco' => $produto['preco']
    ];
}
    // Insere na tabela PEDIDOS - Cria o pedido
    $stmt_pedido = $pdo->prepare(
        "INSERT INTO pedidos (id_cliente, total, status) 
        VALUES (:cliente, :total, 'pendente')"
    );
    $stmt_pedido->bindValue(':cliente', $id_cliente, PDO::PARAM_INT);
    $stmt_pedido->bindValue(':total', $total);
    $stmt_pedido->execute();

    $id_pedido = $pdo->lastInsertId(); //Pega o último ID que foi criado no banco, esse é o número do pedido
    

// Insere cada item na tabela ITENS_PEDIDO e atualiza estoque
    foreach($produtosCarrinho as $item){//foreach :Percorre cada item do carrinho 

        $stmt_item = $pdo->prepare(
            "SELECT itens_pedido 
            (id_pedido, id_produto, quantidade, preco)
            VALUES (:pedido, :produto, :qtd, :preco)"
        );
        $stmt_item->bindValue(':pedido', $id_pedido, PDO::PARAM_INT);
        $stmt_item->bindValue(':produto', $item['id'], PDO::PARAM_INT);
        $stmt_item->bindValue(':qtd', $item['quantidade'], PDO::PARAM_INT);
        $stmt_item->bindValue(':preco', $item['preco']);
        $stmt_item->execute();

        //Atualizar estoque
        $stmt_update = $pdo->prepare(
            "UPDATE produtos
            SET estoque = estoque - :qtd
            WHERE id = :produto"
        );

        $id_produto = $item['id'];
        $quantidade = $item['quantidade'];
        $preco = $produto['preco'];

        $stmt_item = $pdo->prepare(
            "INSERT INTO itens_pedido 
            (id_pedido, id_produto, quantidade, preco)
            VALUES (:pedido, :produto, :qtd, :preco)"
        );
        $stmt_item->bindValue(':pedido', $id_pedido, PDO::PARAM_INT);
        $stmt_item->bindValue(':produto', $id_produto, PDO::PARAM_INT);
        $stmt_item->bindValue(':qtd', $quantidade, PDO::PARAM_INT);
        $stmt_item->bindValue(':preco', $preco);

        $stmt_item->execute();

        //Atualiza estoque
        $stmt_update = $pdo->prepare(
            "UPDATE produtos 
            SET estoque = estoque - :qtd 
            WHERE id = :produto"
        );

        $stmt_update->bindValue(':qtd', $item['quantidade'], PDO::PARAM_INT);
        $stmt_update->bindValue(':produto', $item['id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    //Finaliza tudo
    $pdo->commit();

    //Limpa carrinho
    unset($_SESSION['carrinho']);

    echo "<p style='color:green;'>Compra finaliza com sucesso! Pedido nº {$id_pedido}</p>";
    echo "<a href='ver_produtos.php'>Continuar comprando</a> |
        <a href='painel_cliente.php'>Voltar ao painel</a>";

}catch (Exception $e){
    
    // Desfaz tudo se algo der errado
    $pdo->rollback();
    echo "<p style='color:red;'>Erro ao finalizar compra: {$e->getMessage()}</p>";
}

?>
