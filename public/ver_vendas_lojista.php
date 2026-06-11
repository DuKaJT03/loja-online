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
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/pages/ver_vendas_lojista.css">
</head>
<body>
<div class="vendas-page">

    <div class="page-header">

        <h1>Pedidos Recebidos</h1>

        <a
            href="painel_vendedor.php"
            class="btn btn-secondary"
        >
            Voltar
        </a>

    </div>

    <div id="toast"></div>

    <div class="table-container">
        <table class="vendas-table">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach($vendas as $row){ ?>

                    <tr id="linha-<?= $row['id_item'] ?>">

                        <td>
                            #<?= $row['pedido_id'] ?>
                        </td>

                        <td>
                            <?= $row['data'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['cliente']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['produto']) ?>
                        </td>

                        <td>
                            <?= $row['quantidade'] ?>
                        </td>

                        <td>
                            R$
                            <?= number_format($row['preco'],2,',','.') ?>
                        </td>

                        <td>
                            <span
                                id="status-<?= $row['id_item'] ?>"
                                class="status-badge status-<?= $row['status_item'] ?>"
                            >
                                <?= $row['status_item'] ?>
                            </span>
                        </td>

                        <td>
                            <div class="acoes">
                                <button
                                    class="btn btn-success aprovar"
                                    data-id="<?= $row['id_item'] ?>"
                                >
                                    Aprovar
                                </button>

                                <button
                                    class="btn btn-danger cancelar"
                                    data-id="<?= $row['id_item'] ?>"
                                >
                                    Cancelar
                                </button>

                                <button
                                    class="btn btn-primary enviar"
                                    data-id="<?= $row['id_item'] ?>"
                                >
                                    Enviar
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
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
            el.classList.remove("status-pendente", "status-aprovado", "status-cancelado", "status-enviado");
            //adiciona nova classe
            el.classList.add("status-" + novoStatus);

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