<?php 

namespace HendyNurSholeh\App{
    function header($value){
        echo "$value";
    }
}
namespace HendyNurSholeh\Middleware{

    use HendyNurSholeh\Config\Database;
    use HendyNurSholeh\Domain\Session;
    use HendyNurSholeh\Domain\User;
    use HendyNurSholeh\Repository\SessionRepository;
    use HendyNurSholeh\Repository\UserRepository;
    use HendyNurSholeh\Service\SessionService;
    use HendyNurSholeh\Service\SessionServiceTest;
    use PHPUnit\Framework\TestCase;
    class MustNotLoginMiddlewareTest extends TestCase{
        private MustNotLoginMiddleware $mustNotLoginMiddleware;
        private SessionService $sessionService;
        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;
    
        protected function setUp(): void{
            $connection = Database::getConnection();
            $this->mustNotLoginMiddleware = new MustNotLoginMiddleware();
            $this->sessionRepository = new SessionRepository($connection);
            $this->userRepository = new UserRepository($connection);
            $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
            $this->userRepository->deleteAll();
            $this->sessionRepository->deleteAll();
        }
    
        public function testMustNotLoginNotRidirect(): void{
            self::expectOutputString("");
            $this->mustNotLoginMiddleware->before();
        }
        
        public function testMustNotLoginRidirect(): void{
            putenv("mode=test");
            $user = new User("123", "hendy", "hendy123");
            $session = new Session("session123", $user->getId());
            $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
            $this->userRepository->save($user);
            $this->sessionRepository->save($session);
            $this->sessionService->create($user->getId());
            self::expectOutputString("Location: /");
            $this->mustNotLoginMiddleware->before();
        }
        
    }
}

?>