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
$conexao = Conexao::conectar();

//prepare
$stmt = $conexao->prepare(
    "SELECT
        pe.id AS pedido_id,
        pe.data,
        pe.status,
        u.nome AS cliente,
        pr.nome AS produto,
        i.quantidade,
        i.preco
    FROM itens_pedido i
    INNER JOIN produtos pr ON pr.id = i.id_produto
    INNER JOIN pedidos pe ON pe.id = i.id_pedido
    INNER JOIN usuarios u ON u.id = pe.id_cliente
    WHERE pr.id_lojista = ?
    ORDER BY pe.data DESC"
);

if(!$stmt){
    die("Erro no prepare: " . $conexao->error);
}

//BIND 
$stmt->bind_param("i", $id_lojista);
$stmt->execute();
$resultado = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendas</title>
</head>
<body>
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
    while($row = $resultado->fetch_assoc()){
        echo "<tr>
                <td>{$row['pedido_id']}</td>
                <td>{$row['data']}</td>
                <td>{$row['cliente']}</td>
                <td>{$row['produto']}</td>
                <td>{$row['quantidade']}</td>
                <td>R$ ".number_format($row['preco'],2,',','.')."</td>
                <td>{$row['status']}</td>
                <td>
                    <button class='aprovar' data-id='{$row['pedido_id']}'>Aprovar</button>
                    <button class='cancelar' data-id='{$row['pedido_id']}'>Cancelar</button>
                    <button class='enviar' data-id='{$row['pedido_id']}'>Enviar</button>
                </td>
            </tr>";
    }
    ?>
</table>
<script>

document.querySelectorAll(".aprovar").forEach(btn => {
    btn.addEventListener("click", function(){

        let id = this.dataset.id;

        fetch("atualizar_status.php", {
            method: "POST",
            headers: {
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body: "id="+id+"&status=aprovado"
        })
        .then(res => res.text())
        .then(r => location.reload());

    });
});

document.querySelectorAll(".cancelar").forEach(btn => {
    btn.addEventListener("click", function(){

        let id = this.dataset.id;

        fetch("atualizar_status.php", {
            method: "POST",
            headers: {
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body: "id="+id+"&status=cancelado"
        })
        .then(res => res.text())
        .then(r => location.reload());

    });
});

document.querySelectorAll(".enviar").forEach(btn => {
    btn.addEventListener("click", function(){

        let id = this.dataset.id;

        fetch("atualizar_status.php", {
            method: "POST",
            headers: {
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body: "id="+id+"&status=enviado"
        })
        .then(res => res.text())
        .then(r => location.reload());

    });
});

</script>
</body>
</html>