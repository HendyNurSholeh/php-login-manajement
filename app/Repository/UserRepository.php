<?php
namespace HendyNurSholeh\Repository;

use HendyNurSholeh\Domain\User;
use PDO;

class UserRepository{

    public function __construct(
        private PDO $connection
    ){}

    public function save(User $user): User{
        $sql = <<<SQL
            INSERT INTO users(id, username, password)
            VALUES(:id, :username, :password)
        SQL;
        $statement = $this->connection->prepare($sql);
        $id = $user->getId();
        $username = $user->getUsername();
        $password = $user->getPassword(); 
        $statement->bindParam("id", $id);
        $statement->bindParam("username", $username);
        $statement->bindParam("password", $password);
        $statement->execute();
        return $user;
    }

    public function findById(string $id): ?User{
        $sql = <<<SQL
            SELECT id, username, password 
            FROM users
            WHERE id=?
        SQL; 
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        $user = null;
        try{
            if($array = $statement->fetch()){
                $user = new User(
                    $array["id"],
                    $array["username"],
                    $array["password"]
                );
            }
        } finally{
            $statement->closeCursor();
        }
        return $user;
    }

    public function deleteAll(): void{
        $sql = <<<SQL
            DELETE FROM users
        SQL;
        $this->connection->exec($sql);
    }
}