<?php

namespace Jhon\Loja\Database;

use PDO;
use PDOException;

class Conexao {
    
    public static function conectar() {

        $host = "dpg-d6blg7h5pdvs73b98vmg-a";
        $db   = "loja_db_s1j9";
        $user = "loja_db_s1j9_user";
        $pass = "FoTKMJ4dZW9336AKMGzA5ZfKiZfVIHzg";
        $port = "5432";

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        try {

            $pdo = new PDO($dsn, $user, $pass);

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $pdo;

        }  catch(PDOExecption $e){
            die("Erro conexão: ".$e->getMessage());
        }
    }
}

?>