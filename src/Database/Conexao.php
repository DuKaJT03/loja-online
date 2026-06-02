<?php

namespace Jhon\Loja\Database;

use PDO;
use PDOException;

class Conexao {
    
    public static function conectar() {

        $host = "dpg-d8fihlc2m8qs73eb7ts0-a.ohio-postgres.render.com";
        $db   = "loja_db_u3ha";
        $user = "loja_db_u3ha_user";
        $pass = "JpWJmCN7fIY6SUH3Ftv8fAmjn5sgcYmE";
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