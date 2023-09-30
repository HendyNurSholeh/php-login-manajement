<?php
namespace HendyNurSholeh\Controller;

use Exception;
use HendyNurSholeh\App\View;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Exception\ValidationException;
use HendyNurSholeh\Model\UserLoginRequest;
use HendyNurSholeh\Model\UserProfileUpdateRequest;
use HendyNurSholeh\Model\UserRegisterRequest;
use HendyNurSholeh\Repository\SessionRepository;
use HendyNurSholeh\Repository\UserRepository;
use HendyNurSholeh\Service\SessionService;
use HendyNurSholeh\Service\UserService;

class UserController
{

    private UserService $userService;
    private SessionService $sessionService;

    public function __construct(){
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);
        $this->userService = new UserService($userRepository);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register(): void{
        View::render("User/register", ["title" => "User Registration"]);
    }
    
    public function postRegister(): void{
        try{
            $request = new UserRegisterRequest();
            $request->id = $_POST["id"];
            $request->username = $_POST["username"];
            $request->password = $_POST["password"];
            $response = $this->userService->register($request);
            View::redirect("/users/login");
        }catch(ValidationException $ex){
            View::render("User/register", [
                "title" => "User Registration",
                "error" => $ex->getMessage()
            ]);
        }
    }
    
    public function login(): void{
        View::render("User/login", ["title" => "User Login"]);
    }
    
    public function postLogin(): void{
        try{
            $request = new UserLoginRequest();
            $request->id = $_POST["id"];
            $request->password = $_POST["password"];
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->getId());
            View::redirect("/");
        }catch(ValidationException $ex){
            View::render("User/login", [
                "title" => "User Login",
                "error" => $ex->getMessage()
            ]);
        }
    }

    public function logout(): void{
        $this->sessionService->destroy();
        View::redirect("/");
    }

    public function updateProfile(): void{
        $user = $this->sessionService->current();
        $model = [
            "title" => "update user profile",
            "form" => [
                "id"=>$user->getId(),
                "username"=>$user->getUsername(),
            ]
        ];
        View::render("User/profile", $model);
    }
    
    public function postUpdateProfile(): void{
        $user = $this->sessionService->current();
        try{
            $request = new UserProfileUpdateRequest();
            $request->id = $user->getId();
            $request->username = $_POST["username"];
            $this->userService->updateProfile($request);
            View::redirect("/");
        }catch(Exception $exception){
            $model = [
                "title" => "update user profile",
                "error" => $exception->getMessage(),
                "form" => [
                    "id"=>$user->getId(),
                    "username"=>$user->getUsername(),
                ]
            ];
            View::render("/", $model);
        }
    }
}