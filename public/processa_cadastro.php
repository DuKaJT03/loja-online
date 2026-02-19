<?php
    //Ele não mostra um formulário, ele só processa.
    //Ele pega os dados que vêm do formulário HTML, trata esses dados, valida, insere no banco de dados e dá uma resposta.

require_once __DIR__ . '/../vendor/autoload.php';// chama o arquivo de conexão
use Jhon\Loja\Database\Conexao;

    //Verifica se todos os campos foram enviados corretamente
    if(isset($_POST['nome'],$_POST['email'],$_POST['senha'],$_POST['tipo'], $_POST['confirmar_senha'])){
        //capturando os valores do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = ($_POST['senha']); 
        $tipo = $_POST['tipo'];
        $confirmar_senha = $_POST['confirmar_senha'];

        $conexao = Conexao::conectar();
        
        //Verifica se e-mail já existe 
        $verifica = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $verifica->bind_param("s", $email);
        $verifica->execute();
        $verifica->store_result();

        if($verifica->num_rows > 0){
            echo "<p class='erro'>E-mail já cadastrado. Use outro. </p>";
            exit;
        }else if($senha != $confirmar_senha){
            echo "<p class='erro'>As senhas não coincidem.</p>";
            exit;
        }
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //Criptografando

        //Inserindo no banco de dados (INSEGURO)
        //$sql = "INSERT INTO usuarios (nome, email, senha, tipo)VALUES ('$nome', '$email', '$senha', '$tipo')";
        //if($conexao->query($sql)===TRUE){

        $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha, $tipo);

        if($stmt->execute()){
            echo "<p style='color:green;'>Usuário cadastrado com sucesso!</p>";
            echo "<a href='login.html'>Ir para o Login</a>";
        }else{
            echo "Erro: " . $stmt->error;
        }
        $stmt->close();
        $conexao->close();
}else{
    header('Location: index.html');
    exit;
}
/** composer.json
 * bootstrap
 * .env
 * conexao
 * gitignore
 * longin.html
 * processalogin
 * logout
 * painelcliente
 * painelvendedor
 * index.php
 * processacadastro
 * 
 * FALTA
 * adicionarcarrinhoajax
 * adicionarcarrinho
 * cadastroproduto
 * editaproduto
 * excluirproduto
 * finalizarcompra
 * listaproduto
 * menu
 * processaeditaproduto
 * processaproduto
 * removercarrinho
 * vercarrinho
 * veritenspedido
 * verpedidos
 * verprodutos
 * autoload
 */
?>
