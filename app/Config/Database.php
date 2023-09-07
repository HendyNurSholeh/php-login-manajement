<?php 
namespace HendyNurSholeh\Config;

use PDO;

class Database{
    private static ?PDO $connection = null;
    public static function getConnection(string $env = "test"): PDO{
        if(is_null(self::$connection)){
            require_once __DIR__ . "/../../config/database.php";
            $config = getDatabaseConfig();
            self::$connection = new PDO(
                $config["database"][$env]["url"], 
                $config["database"][$env]["username"], 
                $config["database"][$env]["password"]
            );
        }
        return self::$connection;
    }

    public static function beginTransaction(): void{
        self::$connection->beginTransaction();
    }

    public static function commitTransaction(): void{
        self::$connection->commit();       
    }

    public static function rollbackTransaction(): void{
        self::$connection->rollBack();
    }
}