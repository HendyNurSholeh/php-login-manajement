<?php
namespace HendyNurSholeh\Repository;

use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\User;
use PDO;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase{
    private UserRepository $userRepository;
    
    protected function setUp(): void{
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSave(): void{
        $id = "123";
        $user = new User($id, "usernameTest", "passwordTest");
        $this->userRepository->save($user);
        $userDatabase = $this->userRepository->findById($id);
        self::assertEquals($user, $userDatabase); 
    }
    
    public function testFindById(): void{
        $user = new User(
            "22013010",
            "hendy",
            "hendy123"
        );
        $this->userRepository->save($user);
        $result = $this->userRepository->findById($user->getId());
        self::assertEquals($user, $result);
    }

    public function testFindByIdNotFound(): void{
        $id = "salah";
        $result = $this->userRepository->findById($id);
        self::assertNull($result);
    }

}