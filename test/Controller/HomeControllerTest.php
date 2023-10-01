<?php 
namespace HendyNurSholeh\Controller;

use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\Session;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use HendyNurSholeh\Service\SessionService;
use HendyNurSholeh\Service\UserService;
use PHPUnit\Framework\TestCase;
class HomeControllerTest extends TestCase{

    private HomeController $homeController;

    private SessionRepository $sessionRepository; 
    private UserRepository $userRepository; 

    protected function setUp(): void{
        $this->homeController = new HomeController();
        $conection = Database::getConnection();
        $this->sessionRepository = new SessionRepository($conection);
        $this->userRepository = new UserRepository($conection);
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testUserLogin(): void{
        self::expectOutputRegex("[Login Manajement]");
        self::expectOutputRegex("[by]");
        self::expectOutputRegex("[Hendy Nur Sholeh]");
        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[Login]");
        $this->homeController->index();
    }

    public function testGuest(): void{
        $user = new User("user123", "hendy", "rahasia123");
        $session = new Session("session123", $user->getId());
        $this->userRepository->save($user);
        $this->sessionRepository->save($session);
        $_COOKIE["X-HYNS-COOKIE"] = $session->getId();
        self::expectOutputRegex("[Hai]");
        self::expectOutputRegex("[by]");
        self::expectOutputRegex("[Hendy Nur Sholeh]");
        self::expectOutputRegex("[Profile]");
        self::expectOutputRegex("[Password]");
        self::expectOutputRegex("[Logout]");
        $this->homeController->index();
    }
}
?>