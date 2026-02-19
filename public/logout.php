<?php
session_start();//Inicia a sessão (função obrigatória para acessar qualquer variável de sessão)

session_destroy(); //Destrói todos os dados da sessão (Remove todos os dados que estavam na $_SESSION)

header('Location: login.html');
exit;
?>