<?php
    namespace HendyNurSholeh\Repository;

use HendyNurSholeh\Config\Database;
use PDO;
use PHPUnit\Framework\TestCase;
    class SessionRepositoryTest extends TestCase{

        private SessionRepository $sessionRepository;

        protected function setUp():void{
            $this->sessionRepository= new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
        }

        public function testSave(): void{
                      
        }
    }
?>