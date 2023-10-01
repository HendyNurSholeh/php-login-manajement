<?php

namespace HendyNurSholeh\Controller;

use HendyNurSholeh\App\View;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use HendyNurSholeh\Service\SessionService;
use UserController;

class HomeController
{

    private SessionService $sessionService;

    public function __construct() {
        $conn = Database::getConnection();
        $sessionRepository = new SessionRepository($conn);
        $userRepository = new UserRepository($conn);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index(): void{
        $user = $this->sessionService->current();
        if($user == null){
            View::render("Home/index", [
                "title" => "PHP Login Manajement"
            ]);
        } else{
            $data = [
                "title" => "PHP Login Manajement | Dashboard",
                "username" => $user->getUsername()
            ];
            View::render("Home/dashboard", $data);
        }
    }
}