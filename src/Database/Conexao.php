<?php

namespace Jhon\Loja\Database;

use PDO;
use PDOException;

class Conexao {
    
    public static function conectar() {

        $host = "dpg-d7ogjqipmmbs73fbiojg-a.ohio-postgres.render.com";
        $db   = "loja_db_r2m8";
        $user = "loja_db_r2m8_user";
        $pass = "aSL8vddJqloUuuyYXcPoZKgCORvX2pdc";
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