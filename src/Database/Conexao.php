<?php

namespace Jhon\Loja\Database;

use PDO;
use PDOException;

class Conexao {
    
    public static function conectar() {

        $host = "dpg-d9v3rfnavr4c73dib5u0-a.ohio-postgres.render.com";
        $db   = "loja_db_blky";
        $user = "loja_db_blky_user";
        $pass = "M1xhe93zic4LCHr0cRzWbo5nAjknLEIU";
        $port = "5432";

        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

        try {

            $pdo = new PDO($dsn, $user, $pass);

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $pdo;

        }  catch(PDOException $e){
            die("Erro conexão: ".$e->getMessage());
        }
    }
}

?>