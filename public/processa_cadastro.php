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
        $senha = $_POST['senha']; 
        $tipo = $_POST['tipo'];
        $confirmar_senha = $_POST['confirmar_senha'];

        $pdo = Conexao::conectar();
        
        //Verifica se e-mail já existe 
        $verifica = $pdo->prepare(
            "SELECT id FROM usuarios WHERE email = :email"
            );
        $verifica->bindValue(':email', $email);
        $verifica->execute();

        if($verifica->rowCount() > 0){
            echo "<p class='erro'>E-mail já cadastrado. Use outro. </p>";
            exit;
        } 
        if($senha != $confirmar_senha){
            echo "<p class='erro'>As senhas não coincidem.</p>";
            exit;
        }

        //Criptografa a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        //Insere usuário
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, tipo) 
            VALUES (:nome, :email, :senha, :tipo)"
            );
        
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senhaHash);
        $stmt->bindValue(':tipo', $tipo);

        $stmt->execute();

        echo "<p style='color:green;'>Usuário cadastrado com sucesso!</p>";
        echo "<a href='login.html'>Ir para o Login</a>";
    }
