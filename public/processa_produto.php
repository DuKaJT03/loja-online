<?php
session_start();//permite acessar dados como quem está logado ($_SESSION)
require_once __DIR__ . '/../vendor/autoload.php'; //inclui o arquivo
use Jhon\Loja\Database\Conexao;

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'lojista'){
    header('Location: login.html');
    exit;
}
//Recebe os dados do formulário
//Pega o valor que veio do campo name="nome" no formulário.
$nome = $_POST['nome'];//Acesso via POST
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];
$estoque = $_POST['estoque'];
$id_lojista = $_SESSION['usuario_id'];
$categoria = $_POST['categoria'];

//Upload da imagem
$nome_imagem =''; //Cria uma variável vazia pra guardar o nome da imagem

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

//Verifica se foi enviado um arquivo e se não teve erro.
if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
//$_FILES['imagem'] → Superglobal que guarda os dados do arquivo enviado no input type="file".

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

    $pasta = 'uploads/'; //Cria uma string com o nome da pasta onde as imagens vão ser salvas.
    if(!is_dir($pasta)){ //verifica se a pasta imagens/ existe.
        mkdir($pasta, 0755);// se não existirm cria com permissão 0755(padrão para leitura e escrita)
    }

    $nome_arquivo = basename($_FILES['imagem']['name']);//besename(): Remove possíveis caminhos do arquivo, pegando só o nome.
    //cria um nome único pro arquivo, juntando $pasta=imagens/
    $caminho_arquivo = $pasta . time() .'_' .$nome_arquivo; //nome do arquivo original(Exemplo:imagens/1717000000_camisa.png)
             //time(): pega o timestamp atual(número único)pra não sobreescrever.|| '_' :Só pra separar

    if(move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_arquivo)){// move_uploaded_file() → Move o arquivo da pasta temporária para a pasta imagens/.
        $nome_imagem = $caminho_arquivo;//Salva o caminho na variável $nome_imagem.
    }else{
        echo"Erro no uplad da imagem.";
        exit();
    }
}
/*
// Insere no banco (INSEGURO)
$sql = "INSERT INTO produtos (id_lojista, nome, descricao, preco, imagem, estoque)
        VALUES ('{$_SESSION['usuario_id']}', '$nome', '$descricao', '$preco', '$nome_imagem', '$estoque')";//O ID do lojista que está cadastrando(pega da sessão).
        if($conexao->query($sql) == TRUE){//Executa a query no banco
        */
//Cadastro Insere no banco (SEGURO)Prepared Statement
$conexao = Conexao::conectar();
$stmt = $conexao->prepare("INSERT INTO produtos (id_lojista, nome, descricao, preco, imagem, estoque, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");//$stmt= Cria uma variével chamada stmt que guarda o statement preparado
//$conexao->prepare() Método que prepara o comando SQL com parâmetros ? que serão preenchidos depois, ? espaços reservados para os dados
$stmt->bind_param("issdsis", $id_lojista, $nome, $descricao, $preco, $nome_imagem, $estoque, $categoria); //$stmt->bind_param("ssdsi", Faz a ligação doso dados nas interrogações do SQL. O "ssdsi" indica os tipos dos dados.(ssdsi: String(nome, descricao, imagem) Double(preco) Inteiro(id_lojista))

if($stmt->execute()){//Executa a query no banco
    header("Location: lista_produtos.php?mensagem=Produto cadastrado com sucesso");
    exit;
}else{
    echo "Erro ao cadastrar produto: " . $stmt->error;
}

$conexao->close();

?>