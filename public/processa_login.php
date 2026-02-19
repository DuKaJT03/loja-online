<?php
session_start(); //Inicia a sessão(permite guardar dados entre páginas, como login).

require_once __DIR__ . '/../vendor/autoload.php';//Inclui a conexão com o banco

use Jhon\Loja\Database\Conexao;

$conexao = Conexao::conectar();

if(isset($_POST['email'], $_POST['senha'])){ //verifica se os campos email e senha chegaram
    $email = $_POST['email'];//captura os valores nas variáveis
    $senha = $_POST['senha'];
    /*
    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = '$email'";//monta o sql para buscar o usuário pelo email
    $resultado = $conexao->query($sql);//executa o SQL
    */
    $stmt = $conexao->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows == 1){//verifica se retornou 1 resultado
        $usuario = $resultado->fetch_assoc(); //recebe o resultado convertido em array associativo (chave => valor)

        if(password_verify($senha, $usuario['senha'])){ //$senha conrresponde com o hash armazenado no banco ($usuario['senha'])
            //Criando ovariáveis de sessão $_SESSION é uma super global do PHP
            //Tudo que colocarmos dentro dela fica disponível enquanto o usuário estiver logado
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            //Agora redireciona pro painel certo
            if($_SESSION['usuario_tipo'] == 'cliente'){
                header('Location: painel_cliente.php');
                exit;
            }elseif($_SESSION['usuario_tipo'] == 'lojista'){
                header('Location: painel_vendedor.php');
                exit;
            }else{
                echo "Tipo de usuário desconhecido.";
            }
        }else{
            header ('Location: login.html?erro=senha');
            exit;
        }
    }else{
        echo"Email não encontrado.";
    }

    $stmt->close();
}else{
    echo"Preencha todos os campos.";
}

$conexao->close();
?>