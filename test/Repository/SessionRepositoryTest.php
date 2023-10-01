<?php
    namespace HendyNurSholeh\Repository;

use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\Session;
use PDO;
use PHPUnit\Framework\TestCase;
    class SessionRepositoryTest extends TestCase{

        private SessionRepository $sessionRepository;

        protected function setUp():void{
            $this->sessionRepository= new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
        }

        public function testSaveSuccess(): void{
            $session = new Session(uniqid(), "user123");
            $this->sessionRepository->save($session);
            $result = $this->sessionRepository->findById($session->getId());
            self::assertEquals($session, $result);
            self::assertNotNull($result);
        }
        
        public function testFindByIdNotFound(): void{
            $result = $this->sessionRepository->findById("salah");
            self::assertNull($result);
        }
        
        public function testDeleteByIdSuccess(): void{
            $session = new Session(uniqid(), "user123");
            $this->sessionRepository->save($session);
            $this->sessionRepository->deleteById($session->getId());
            $result = $this->sessionRepository->findById($session->getId());
            self::assertNull($result);
        }
    }
?>