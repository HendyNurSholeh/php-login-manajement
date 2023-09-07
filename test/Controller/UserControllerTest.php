<?php 
namespace HendyNurSholeh\App{
    function header($value){
        echo "$value";
    }
}
namespace HendyNurSholeh\Controller{

    use HendyNurSholeh\Config\Database;
    use HendyNurSholeh\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    use function PHPUnit\Framework\assertTrue;

    class UserControllerTest extends TestCase{
    
        private UserController $userController;
        private UserRepository $userRepository;
    
        protected function setUp(): void{
            $this->userController = new UserController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
        }
        public function testpostRegisterSuccess(): void{
            putenv("mode=test");
            $_POST["id"]="123xxx";
            $_POST["username"]="hendy";
            $_POST["password"]="hendy123";
            $this->userController->postRegister();
            self::expectOutputString("Location: /users/login");
        }

        public function testpostRegisterFailed(): void{
            putenv("mode=test");
            $_POST["id"]="12";
            $_POST["username"]="hendy";
            $_POST["password"]="hendy123";
            $this->userController->postRegister();
            self::expectOutputRegex("[User Registration]");
        }


    }
}



?>