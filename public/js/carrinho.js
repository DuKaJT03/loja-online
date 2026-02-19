// Espera o conteúdo do HTML carregar completamente
document.addEventListener("DOMContentLoaded", function(){
//document: refere-se a pagina html
    //pega todos os botões com a classe 'add_carrinho'
    const botoes = document.querySelectorAll(".add_carrinho"); //.add_carrinho : . significa classe. ISso busca todos os elementos que têm class="add_carrinho"
    //const: variavel que não pode ser reatribuida || querySelectorAll: busca todos os elementos que têm o seletor informado 

    //Para cada botão encontrado..
    botoes.forEach(function(botao){//forEach: método que passa por cada botão individualmente
        //Adiciona um "escutador de clique"
        botao.addEventListener("click", function(e){
            e.preventDefault(); //Impede  que o link recarregue a página (impede o comportamento padrão)

            //this :significa o elemento HTML clicado(o botão) ||dataset.id :pega o atributo data_id="123" esse botão
            const id = this.dataset.id; //Pega o id do produto do atributo data_id
            //Cria a riquisição AJAX, xhr:nome da variável para guardar o objeto de requisição ajax
            const xhr = new XMLHttpRequest(); //Objeto responsável por fazer requisições HTTP (como GET e POST)
            
            //xhr.open :Método que prepara a requisição || "POST" (poderia ser "GET", mas POST é mais seguro para dados).
            xhr.open("POST", "adicionar_carrinho_ajax.php", true); // url:nome do arquivo de destino , true:indica que a requisição será assíncrona (não trava a página)
            //xhr.setRequestHeader: define um cabeçalho da requisição || content-type: nome do cabeçalho
            xhr.setRequestHeader(
                "Content-Type",
                "application/x-www-form-urlencoded"
            ); //application/x-www-form-urlencoded :tipo de dados que será enviado (igual formulário HTML comum) 

            //Quando a riquisição terminar...
            //chr.onload :função a ser executada quando a resposta do servidor chegar
            xhr.onload = function(){
                if (xhr.status === 200 && xhr.responseText === "ok"){ //xhr.status :código HTTP da resposta (200 = sucesso) || === :comparação estrita (valor e tipo) || 200 :número que significa OK (sucesso)
                    alert("Produto adicionado ao carrinho!"); //Mostra confirmação
                }else{
                    alert("Erroo ao adicionar produto.");
                }
            };
            
            console.log("ID enviado:", id);

            //Envia os dados
            //xhr.send :Envia a requisição para o servidor
            xhr.send("id=" + encodeURIComponent(id));// "id=" + ...: monta o texto que vai ser enviado no corpo (como se fosse id=123)
            //encodeURIComponent(id): protege o valor de id para não quebrar a URL.
        });
    });
});