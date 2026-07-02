<?php

namespace Jhon\Loja\Database;

use PDO;
use PDOException;

class Conexao {
    
    public static function conectar() {

        $host = "dpg-d935n0vaqgkc739405og-a.ohio-postgres.render.com";
        $db   = "loja_db_m0sr";
        $user = "loja_db_m0sr_user";
        $pass = "aq4NDLFITn0UqqXyR7zspjiG0BS10gEC";
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