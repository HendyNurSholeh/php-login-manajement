<?php
namespace HendyNurSholeh\Service;

use Exception;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Exception\ValidationException;
use HendyNurSholeh\Model\UserLoginRequest;
use HendyNurSholeh\Model\UserLoginResponse;
use HendyNurSholeh\Model\UserRegisterRequest;
use HendyNurSholeh\Model\UserRegisterResponse;
use HendyNurSholeh\Repository\UserRepository;
use PDOException;

class UserService{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse{
        $this->validationRegister($request);
        try{
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if(!is_null($user)){
                throw new ValidationException("User id already exists");
            }
            $newUser = new User(
                $request->id,
                $request->username,
                password_hash($request->password, PASSWORD_BCRYPT)
            );
            $this->userRepository->save($newUser);
            $response = new UserRegisterResponse();
            $response->user = $newUser;
            Database::commitTransaction();
        } catch(PDOException $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
        return $response;
    }

    public function validationRegister(UserRegisterRequest $request){
        if(is_null($request->id) || is_null($request->username) || is_null($request->password) ||
           trim($request->id) == "" || trim($request->username) == "" || trim($request->password) == ""){
            throw new ValidationException("Id, name, password can't not blank");
        }else if(strlen(trim($request->id)) < 3){
            throw new ValidationException("id length can't be less than 3");
        }else if(strlen(trim($request->username)) < 3){
            throw new ValidationException("username length can't be less than 3");
        }else if(strlen(trim($request->password)) < 8){
            throw new ValidationException("password length can't be less than 8");
        }
        $request->id = trim($request->id);
        $request->username = trim($request->username);
        $request->password = trim($request->password);
    }

    public function login(UserLoginRequest $request): UserLoginResponse{
        $this->validateUserLoginRequest($request);
        $user = $this->userRepository->findById($request->id);
        $response = new UserLoginResponse();
        if($user == null || !password_verify($request->password, $user->getPassword())){
            throw new ValidationException("id or password is wrong");
        }
        $response->user = $user;
        return $response;
    }

    public function validateUserLoginRequest(UserLoginRequest $request){
        if(is_null($request->id) || is_null($request->password) ||
           trim($request->id) == "" || trim($request->password) == ""){
            throw new ValidationException("Id, password can't not blank");
        }
        $request->id = strtolower($request->id);
        $request->password = strtolower($request->password);
    }
}