<?php
namespace HendyNurSholeh\Controller;

use HendyNurSholeh\App\View;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Exception\ValidationException;
use HendyNurSholeh\Model\UserLoginRequest;
use HendyNurSholeh\Model\UserRegisterRequest;
use HendyNurSholeh\Repository\UserRepository;
use HendyNurSholeh\Service\UserService;

class UserController
{

    private UserService $userService;

    public function __construct(){
        $connection = Database::getConnection();
        $repository = new UserRepository($connection);
        $this->userService = new UserService($repository);
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
            $request->id = trim($_POST["id"]);
            $request->password = trim($_POST["password"]);
            $this->userService->login($request);
            View::redirect("/");
        }catch(ValidationException $ex){
            View::render("User/login", [
                "title" => "User Login",
                "error" => $ex->getMessage()
            ]);
        }
    }
}