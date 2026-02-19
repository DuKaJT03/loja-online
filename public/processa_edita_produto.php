<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) ||$_SESSION['usuario_tipo'] != 'lojista'){
    header('Location: login.html');
    exit;
}

//Verifica se os campos foram enviados 
if(//verifica se todos os campos vierom no formulário
    isset($_POST['id'])&&//$_POST['ID']: O ID do produto que será atualizado
    isset($_POST['nome'])&&
    isset($_POST['descricao'])&&
    isset($_POST['preco'])&&
    isset($_POST['estoque'])
){
    //Captura de dados
    $id = intval($_POST['id']);//intval(): Transforma o ID em inteiro (protege contra injeção)
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = floatval($_POST['preco']);//FLOATVAL: Converte pra número decimal
    $estoque = intval($_POST['estoque']);

    if(empty($nome) || empty($descricao) || empty($preco) || empty($estoque)){
    echo "<p style='color:red;'>Todos os campos são obrigatórios!</p>";
    echo "<a href='cadastro_produto.php'>Voltar</a>";
    exit;
    }
    if(!is_numeric($preco) || $preco <= 0){
        echo "<p style='color:red;'>Preço inválido.</p>";
        echo "<a href='cadastro_produto.php'>Voltar</a>";
        exit;
    }

    //Busca imagem atual
    $sql="SELECT imagem FROM produtos WHERE id = $id AND id_lojista= ".$_SESSION['usuario_id'];
    $conexao = Conexao::conectar();
    $resultado = $conexao->query($sql);

    if($resultado->num_rows != 1){
        echo "Produto não encontrado ou sem permissão.";
        exit;
    }
    //Pega os dados atuais do produto
     $produto = $resultado->fetch_assoc();//Transforma em array associativo
     $imagem= $produto['imagem'];//Guarda o caminho atual da imagem

     //Verifica se foi enviado uma nova imagem
     if (isset($_FILES['imagem']) && $_FILES['imagem']['error']==0){ //$_FILES['imagem'] : arquivo enviado , guardad informações dos arquivos enviados via form com (ecntype="multipart/form-data")| error ==0 :Nenhum erro no upload
        
        //Validação do tipo de arquivo 
        $tipos_permitidos = ['image/png', 'image/jpeg', 'image/jpg'];
        if(!in_array($_FILES['imagem']['type'], $tipos_permitidos)){
            echo "Tipo de arquivo não permitido. Apenas PNG, JPEG e JPG.";
            exit;
        }
        //validação do tamanho (2MB)
        if($_FILES['imagem']['size']>2*1024*1024){
            echo "Arquivo muito grande. Máximo permitido é 2MB.";
            exit;
        }

     $pasta ='uploads/';//pasta onde vai salvar
     $nome_arquivo = basename($_FILES['imagem']['name']); //$_FILES['imagem']['name'] → Pega o nome original do arquivo que o usuário selecionou.|| basename(): Pega apenas o nome do arquivo, função PHP que remove qualquer caminho de diretórios
     /*Exemplo:
        Se o usuário tentou enviar algo como:
        C:\Users\usuario\Pictures\foto.png
        O basename() pega só:
        foto.png
    */
        //Monta o nome final que o arquivo terá dentro da pasta imagens/
     $caminho_arquivo = $pasta. time().'_'.$nome_arquivo;//Cria um nome único com a data/hora atual. time() ex. imagens/1717023456_foto.png

     if(move_uploaded_file($_FILES['imagem']['tmp_name'],$caminho_arquivo)){//move_uploaded_file():Função do PHP que move o arquivo do local temporário para o destino final
        //$_FILES['imagem']['tmp_name'] → Arquivo temporário no servidor.|| $caminho_arquivo → Destino final com a pasta e nome gerado.
        
        //Remove imagem antiga se existir
        if(file_exists($imagem)&& $imagem!=''){//file_Exist($imagem): Verifica se o arquivo da imagem anterior realmente existe no servidor
                //$imagem !='' :Confrima que há um caminho registrado na variável $imagem
            unlink($imagem);//unlink($imagem): Deleta o arquivo do servidor(Evita acúmulo de arquivos antigos)
        }
        $imagem = $caminho_arquivo;//Atualiza o valor da varialvel $imagem com o camiho do novo arquivo enviado
        }else{
            echo "<p style='color:red;'>Erro: Erro ao fazer upload da imagem.</p>";
            exit;
        }
    }

/*
    //Atualização dos dados no banco (Forma insegura)
    $sql = "UPDATE produtos SET
                nome = '$nome',--Atualiza a coluna nome com valor da variavel $nome
                descricao = '$descricao',
                preco = $preco,--sem aspas porque é numérico
                imagem = '$imagem'--Atualiza ou mantem a antiga
            WHERE id =$id AND id_lojista = ".$_SESSION['usuario_id']; /*Garante que o lojista só pode alterar produtos que são dele.*/
            /*WHERE id=$id :Só atualiza o produto cujo id é igual ao fornecido*/ 
 /*       if($conexao->query($sql)===TRUE){/*Sea atualização for bem sucedida*/
            //echo "Produto atualizado com sucesso!";
            //echo "<br><a href='lista_produto.php'>Voltar</a>";
  /*      }else{
            echo "Erro ao atualizar: ".$conexao->error;
        }*/

    $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, imagem = ?, estoque = ? 
                           WHERE id = ? AND id_lojista = ?");

    $stmt->bind_param("ssdssii", $nome, $descricao, $preco, $imagem, $estoque, $id, $_SESSION['usuario_id']);

    if($stmt->execute()){
        echo "<p style='color:green;'>Produto atualizado com sucesso!</p>";
        echo "<a href='lista_produtos.php'>Voltar</a>";
    } else {
        echo "<p style='color:red;'>Erro ao atualizar: " . $stmt->error . "</p>";
        echo "<a href='lista_produtos.php'>Voltar</a>";
    }

    $stmt->close();

}else{
    echo "Todos os campos são obrigatórios.";
}

$conexao->close();
?>