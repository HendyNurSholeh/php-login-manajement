<?php
namespace HendyNurSholeh\Repository;

use HendyNurSholeh\Domain\Session;
use PDO;

class SessionRepository{

    public function __construct(
        private PDO $connection
    ){}

    public function save(Session $session): Session{
        $sql = <<<SQL
            INSERT INTO sessions(id, user_id)
            VALUES(?, ?)
        SQL;
        $statement = $this->connection->prepare($sql);
        $statement->execute([$session->getId(), $session->getUserId()]);
        return $session;
    }

    public function deleteById(String $id): void{
        $sql = <<<SQL
            DELETE FROM sessions
            WHERE id=?
        SQL;
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
    }

    public function findById(String $id): ?Session{
        $sql = <<<SQL
            SELECT id, user_id
            FROM sessions
            WHERE id=?
        SQL;
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        try{
            if($result = $statement->fetch()){
                return new Session($result["id"], $result["user_id"]);
            }
            return null;
        }finally{
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void{
        $sql = <<<SQL
            DELETE FROM sessions
        SQL;
        $this->connection->exec($sql);
    }
}
?>