<?php 
namespace HendyNurSholeh\Service{
    function setCookie(string $cookieName,  string $value, $optional = []){
        $_COOKIE["X-HYNS-COOKIE"] = $value;
    }
}
namespace HendyNurSholeh\Controller{
    require_once __DIR__ . "/../Helper/helper.php";
    use HendyNurSholeh\Model\UserLoginResponse;
    use HendyNurSholeh\Config\Database;
    use HendyNurSholeh\Domain\Session;
    use HendyNurSholeh\Domain\User;
    use HendyNurSholeh\Repository\UserRepository;
    use HendyNurSholeh\Service\UserService;
    use HendyNurSholeh\Model\UserRegisterRequest;
    use HendyNurSholeh\Repository\SessionRepository;
    use HendyNurSholeh\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    use function PHPUnit\Framework\assertTrue;

    class UserControllerTest extends TestCase{
        
        private UserController $userController;
        private UserRepository $userRepository;

        private SessionRepository $sessionRepository;

        private SessionService $sessionService;

        private $userService;
    
        protected function setUp(): void{
            $this->userController = new UserController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
            $this->userService = $this->createMock(UserService::class);
            $this->userRepository->deleteAll();
            $this->sessionRepository->deleteAll();
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
            self::assertEmpty($_COOKIE["X-HYNS-COOKIE"], "cookie is not empty");
        }

        public function testUpdateProfile(): void{
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $this->userController->updateProfile();
            self::expectOutputRegex("[Profile]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Name]");
            self::expectOutputRegex("[123]");
            self::expectOutputRegex("[hendy123]");
            self::expectOutputRegex("[Update Profile]");
        }

        public function testPostUpdateProfileSuccess(): void{
            putenv("mode=test");
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $_POST["username"] = "joko";
            $this->userController->postUpdateProfile();
            self::expectOutputString("Location: /");
        }

        public function testPostUpdateProfileValidationError(): void{
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $_POST["username"] = "";
            $this->userController->postUpdateProfile();
            self::expectOutputRegex("[Profile]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Name]");
            self::expectOutputRegex("[Update Profile]");
            self::expectOutputRegex("[alert]");
            self::expectOutputRegex("[123]");
            self::expectOutputRegex("[hendy123]");
            self::expectOutputRegex("[Id, username can't not blank]");
        }

        public function testChangePassword(): void{
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $this->userController->changePassword();
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Old Password]");
            self::expectOutputRegex("[New Password]");
            self::expectOutputRegex("[Change Password]");
        }

        public function testPostChangePasswordSuccess(): void{
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $_POST["oldPassword"] = "hendy123";
            $_POST["newPassword"] = "hendy123haha";
            $this->userController->postChangePassword();
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Old Password]");
            self::expectOutputRegex("[New Password]");
            self::expectOutputRegex("[Change Password]");
            self::expectOutputRegex("[password is successfull change]");
        }

        public function testPostChangePasswordValidationError(): void{
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $_POST["oldPassword"] = "";
            $_POST["newPassword"] = "";
            $this->userController->postChangePassword();
            self::expectOutputRegex("[Password]");
            self::expectOutputRegex("[Id]");
            self::expectOutputRegex("[Old Password]");
            self::expectOutputRegex("[New Password]");
            self::expectOutputRegex("[Change Password]");
            self::expectOutputRegex("[Id or new password or old password can't not blank]");
        }
        
        public function testPostChangePasswordWrongOldPassword(): void{
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Old Password]");
            $this->expectOutputRegex("[New Password]");
            $this->expectOutputRegex("[Change Password]");
            $this->expectOutputRegex("[Old Password is wrong]");
            $user = new User("123", "hendy123", password_hash("hendy123", PASSWORD_BCRYPT));
            $session = new Session("session123", $user->getId());
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $_POST["oldPassword"] = "salah";
            $_POST["newPassword"] = "hendyrahasia123";
            $this->userController->postChangePassword();
        }





    }
}



?>