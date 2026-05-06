<?php
//SEGURANÇA
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'lojista'){
    header('Location: login.html');
    exit;
}

$id_lojista = $_SESSION['usuario_id'];
$pdo = Conexao::conectar();

//prepare
$stmt = $pdo->prepare(
    "SELECT
        i.id AS id_item,
        pe.id AS pedido_id,
        pe.data,
        pe.status AS status_pedido,
        u.nome AS cliente,
        pr.nome AS produto,
        i.quantidade,
        i.preco,
        i.status AS status_item
    FROM itens_pedido i
    INNER JOIN produtos pr ON pr.id = i.id_produto
    INNER JOIN pedidos pe ON pe.id = i.id_pedido
    INNER JOIN usuarios u ON u.id = pe.id_cliente
    WHERE pr.id_lojista = :lojista
    ORDER BY pe.data DESC"
);

//BIND 
$stmt->bindValue(':lojista', $id_lojista, PDO::PARAM_INT);
$stmt->execute();

$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendas</title>
</head>
<body>
    <div id="toast"></div>
<!-- TABELA -->
<table border="1">
    <tr>
        <th>Pedido</th>
        <th>Data</th>
        <th>Cliente</th>
        <th>Produto</th>
        <th>Qtd</th>
        <th>Preço</th>
        <th>Status</th>
        <th>Ação</th>
    </tr>

    <?php
    foreach($vendas as $row){
        echo "
            <tr id='linha-{$row['id_item']}'>
                <td>{$row['pedido_id']}</td>
                <td>{$row['data']}</td>
                <td>{$row['cliente']}</td>
                <td>{$row['produto']}</td>
                <td>{$row['quantidade']}</td>
                <td>R$ ".number_format($row['preco'],2,',','.')."</td>
                <td id='status-{$row['id_item']}' class='status {$row['status_item']}'>
                    {$row['status_item']}
                </td>
                <td>
                    <button class='aprovar' data-id='{$row['id_item']}'>Aprovar</button>
                    <button class='cancelar' data-id='{$row['id_item']}'>Cancelar</button>
                    <button class='enviar' data-id='{$row['id_item']}'>Enviar</button>
                </td>
            </tr>
        ";
    }
    ?>
</table>
<script>

function atualizarStatus(botao, novoStatus){

    let id = botao.dataset.id;

    fetch("atualizar_status.php", {
        method: "POST",
        headers: {
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body: "id="+id+"&status="+novoStatus
    })
    .then(res => res.text())
    .then(resposta => {
    
        if(resposta.trim() === "ok"){
        
            //Atualiza o texto do status na tela
            let el = document.getElementById("status-"+id);

            el.innerText = novoStatus;
            
            //remove classes antigas
            el.classList.remove("aprovado", "cancelado", "enviado");
            //adiciona nova classe
            el.classList.add(novoStatus);

            mostrarToast("Status atualizado!");

        }else{
            alert("Erro ao atualizar");
        }
            
    });
}

//Eventos
document.querySelectorAll(".aprovar").forEach(btn => {
    btn.addEventListener("click", function(){
        atualizarStatus(this, "aprovado");
    });
});

document.querySelectorAll(".cancelar").forEach(btn => {
    btn.addEventListener("click", function(){
        atualizarStatus(this, "cancelado");
    });
});

document.querySelectorAll(".enviar").forEach(btn => {
    btn.addEventListener("click", function(){
        atualizarStatus(this, "enviado");
    });
});

function mostrarToast(msg){
    let toast = document.getElementById("toast");
    toast.innerText = msg;
    toast.classList.add("show");

    setTimeout(() =>{
        toast.classList.remove("show");
    }, 2000);
}

</script>
</body>
</html>