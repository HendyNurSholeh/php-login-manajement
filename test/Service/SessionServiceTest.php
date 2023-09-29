<?php 
namespace HendyNurSholeh\Service;

use HendyNurSholeh\Domain\Session;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

function setCookie(string $name, string $value){
    $_COOKIE[$name] = $value;
}

class SessionServiceTest extends TestCase{
    private SessionService $sessionService;
    private $userRepository;
    private $sessionRepository;

    protected function setUp(): void{
        $this->sessionRepository = $this->createMock(SessionRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
    }

    public function testCreateSuccess(): void{
        $this->sessionRepository->expects(self::once())->method("save");
        $userId = "123";
        $session = $this->sessionService->create($userId);
        self::assertNotNull($session, "SESSION IS NULL");
        self::assertInstanceOf(Session::class, $session);
        self::assertNotNull($_COOKIE["X-HYNS-COOKIE"], "cookie is null");
        self::assertEquals($_COOKIE["X-HYNS-COOKIE"], $session->getId());
    }

    public function testDestroySuccess(): void{
        $this->sessionService->create("HENDY123");
        self::assertNotEmpty($_COOKIE["X-HYNS-COOKIE"]);
        $this->sessionService->destroy();
        self::assertEmpty($_COOKIE["X-HYNS-COOKIE"]);
    }

    public function testCurrentSuccess(): void{
        $_COOKIE["X-HYNS-COOKIE"] = '123';
        $session = new Session("123", "user123");
        $user = new User("user123", "hendy", "rahasia");
        $this->sessionRepository->expects(self::once())->method("findById")->with("123")->willReturn($session);
        $this->userRepository->expects(self::once())->method("findById")->with("user123")->willReturn($user);
        $result = $this->sessionService->current();
        self::assertSame($user, $result);
    }

    public function testCurrentNull(): void{
        $result = $this->sessionService->current();
        self::assertNull($result);
    }

}

?>