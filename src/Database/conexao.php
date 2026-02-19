<?php

namespace Jhon\Loja\Database;

use mysqli;
use Exception;

class Conexao {
    
    public static function conectar() {

        require_once __DIR__ . '/../bootstrap.php';

        $servidor = $_ENV['DB_HOST'];
        $usuario = $_ENV['DB_USER'];
        $senha = $_ENV['DB_PASS'];
        $banco = $_ENV['DB_NAME'];

        $conexao = new mysqli($servidor, $usuario, $senha, $banco);

        if ($conexao->connect_error) {
            throw new Exception("Falha na conexão: " . $conexao->connect_error);
        }

        $conexao->set_charset("utf8mb4");

        return $conexao;
    }
}

?>