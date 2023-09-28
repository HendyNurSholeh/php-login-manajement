<?php 
namespace HendyNurSholeh\Service;

use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\Session;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use PDOException;

class SessionService{
    

    public static string $COOKIE_NAME = "X-HYNS-COOKIE";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository) {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public function create(string $userId):Session{
        $uniqId = uniqid();
        $session = new Session($uniqId, $userId);
        try{
            Database::beginTransaction();
            $this->sessionRepository->save($session);
            Database::commitTransaction();
        }catch(PDOException $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
        setcookie(self::$COOKIE_NAME, $session->getId(), time() + 60*60*24*30, "/");
        return $session;
    }

    public function destroy(): void{
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? "";
        $this->sessionRepository->deleteById($sessionId);
        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User{
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $session = $this->sessionRepository->findById($sessionId);
        if($session){
            return $this->userRepository->findById($session->getUserId());
        }
        return null;
    }
}

?>