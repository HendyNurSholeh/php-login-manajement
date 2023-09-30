<?php  
namespace HendyNurSholeh\Middleware;

use HendyNurSholeh\App\View;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use HendyNurSholeh\Service\SessionService;

class MustLoginMiddleware implements Middleware{

    private SessionService $sessionService;

    public function __construct() {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function before(): void{
        $user = $this->sessionService->current();
        if($user == null){
            View::redirect("/users/login");
        }
    }
}
?>