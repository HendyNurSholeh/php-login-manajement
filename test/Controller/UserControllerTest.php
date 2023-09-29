<?php 
namespace HendyNurSholeh\App{
    function header($value){
        echo "$value";
}
}
namespace HendyNurSholeh\Service{
    function setCookie(string $cookieName,  string $value, $optional = []){
        $_COOKIE["X-HYNS-COOKIE"] = $value;
    }
}
namespace HendyNurSholeh\Controller{
    
    use HendyNurSholeh\Model\UserLoginResponse;
    use HendyNurSholeh\Config\Database;
    use HendyNurSholeh\Repository\UserRepository;
    use HendyNurSholeh\Service\UserService;
    use HendyNurSholeh\Model\UserRegisterRequest;
    use PHPUnit\Framework\TestCase;

    use function PHPUnit\Framework\assertTrue;

    class UserControllerTest extends TestCase{
        
        private UserController $userController;
        private UserRepository $userRepository;

        private $userService;
    
        protected function setUp(): void{
            $this->userController = new UserController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userService = $this->createMock(UserService::class);
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

        public function testLogin(): void{
            $this->userController->login();
            self::expectOutputRegex("[User Login]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Sign On]");
        }
        
        public function testPostLoginSuccess(): void{
            putenv("mode=test");
            $this->userService = new UserService($this->userRepository);
            $id = "123";
            $password = "hendy123";
            $_POST["id"] = $id;
            $_POST["password"] = $password;
            $requestRegist = new UserRegisterRequest();
            $requestRegist->id = $id;
            $requestRegist->username = "hendy";
            $requestRegist->password = $password;
            $this->userService->register($requestRegist);
            $this->userController->postLogin();
            self::expectOutputString("Location: /");
        }
        
        public function testUsernameNotFound(): void{
            $this->userService = new UserService($this->userRepository);
            $_POST["id"] = "salah";
            $_POST["password"] = "hendy123";
            $this->userController->postLogin();
            self::expectOutputRegex("[User Login]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Sign On]");
        }
        
        public function testPostLoginUsernameWrong(): void{
            $this->userService = new UserService($this->userRepository);
            $id = "123";
            $password = "hendy123";
            $_POST["id"] = "salah";
            $_POST["password"] = $password;
            $requestRegist = new UserRegisterRequest();
            $requestRegist->id = $id;
            $requestRegist->username = "hendy";
            $requestRegist->password = password_hash($password, PASSWORD_BCRYPT);
            $this->userService->register($requestRegist);
            $this->userController->postLogin();
            self::expectOutputRegex("[User Login]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Sign On]");
        }

        public function testPostLoginPasswordWrong(): void{
            $this->userService = new UserService($this->userRepository);
            $id = "123";
            $password = "hendy123";
            $_POST["id"] = $id;
            $_POST["password"] = "salah";
            $requestRegist = new UserRegisterRequest();
            $requestRegist->id = $id;
            $requestRegist->username = "hendy";
            $requestRegist->password = password_hash($password, PASSWORD_BCRYPT);
            $this->userService->register($requestRegist);
            $this->userController->postLogin();
            self::expectOutputRegex("[User Login]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Sign On]");
        }
        
        public function testPostLoginValidationError(): void{
            $this->userService = new UserService($this->userRepository);
            $_POST["id"] = "";
            $_POST["password"] = "hendy123";
            $this->userController->postLogin();
            self::expectOutputRegex("[User Login]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Sign On]");
        }

        public function testLogout(): void{
            putenv("mode=test");
            $_COOKIE["X-HYNS-COOKIE"] = "123cookieebciu";
            self::assertNotEmpty($_COOKIE["X-HYNS-COOKIE"], "cookie is empty");
            $this->userController->logout();
            var_dump($_COOKIE["X-HYNS-COOKIE"]);
            self::assertEmpty($_COOKIE["X-HYNS-COOKIE"], "cookie is not empty");
        }
    }
}



?>