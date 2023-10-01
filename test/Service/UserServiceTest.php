<?php
namespace HendyNurSholeh\Service;

use Error;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Domain\User;
use HendyNurSholeh\Exception\ValidationException;
use HendyNurSholeh\Model\UserLoginRequest;
use HendyNurSholeh\Model\UserPasswordChangeRequest;
use HendyNurSholeh\Model\UserProfileUpdateRequest;
use HendyNurSholeh\Model\UserRegisterRequest;
use HendyNurSholeh\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase{

    private UserService $userService;
    private $userRepository;

    protected function setUp(): void{
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function testValidationRegisterIdLessThan3(): void{
        self::expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id='12';
        $request->username='hendy';
        $request->password=password_hash('hendy123', PASSWORD_BCRYPT);
        $this->userService->validationRegister($request);
    }

    public function testValidationRegisterUsernameLessThan3(): void{
        self::expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id='123';
        $request->username='he';
        $request->password=password_hash('hendy123', PASSWORD_BCRYPT);
        $this->userService->validationRegister($request);
    }

    public function testValidationRegisterPasswordLessThan8(): void{
        self::expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id='123';
        $request->username='hendy';
        $request->password='hendy12';
        $this->userService->validationRegister($request);
    }

    public function testRegisterRequestNotValid(): void{
        self::expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "12";
        $request->username = null;
        $this->userRepository->expects(self::never())->method("findById");
        $this->userRepository->expects(self::never())->method("save");
        $this->userService->register($request);
    }
    
    // public function testRegisterUserAlreadyExists(): void{
    //     Database::getConnection();
    //     self::expectException(ValidationException::class);
    //     $request = new UserRegisterRequest();
    //     $request->id = "123";
    //     $request->username = "hendy";
    //     $request->password = "hendy123";
    //     $this->userRepository->expects(self::once())->method("findById")->willReturn(new User("123", "hendy", "hendy123"));
    //     $this->userRepository->expects(self::never())->method("save");
    //     $this->userService->register($request);
    // }

    public function testRegisterSuccess(): void{
        Database::getConnection();
        $request = new UserRegisterRequest();
        $request->id = "123";
        $request->username = "hendy";
        $request->password = "hendy123";
        $this->userRepository->expects(self::once())->method("findById")->willReturn(null);
        $this->userRepository->expects(self::once())->method("save");
        $respons = $this->userService->register($request);
        self::assertEquals($request->id, $respons->user->getId());
        self::assertEquals($request->username, $respons->user->getUsername());
        self::assertNotEquals($request->password, $respons->user->getPassword());
        self::assertTrue(password_verify($request->password, $respons->user->getPassword()));
    }

    // public function testValidationUserLoginRequestSuccess(): void{
    //     $request = new UserLoginRequest;
    //     $request->id = "123";
    //     $request->password = "hendy133";
    //     $this->userService->validateUserLoginRequest($request);
    //     self::assertTrue(true);
    // }

    // public function testValidationUserLoginRequestIdBlank(): void{
    //     self::expectException(ValidationException::class);
    //     $request = new UserLoginRequest;
    //     $request->id = "   ";
    //     $request->password = "hendy133";
    //     $this->userService->validateUserLoginRequest($request);
    // }
    
    // public function testValidationUserLoginRequestPasswordBlank(): void{
    //     self::expectException(ValidationException::class);
    //     $request = new UserLoginRequest;
    //     $request->id = "123";
    //     $request->password = "";
    //     $this->userService->validateUserLoginRequest($request);
    // }

    // public function testValidationUserLoginRequestIdNull(): void{
    //     self::expectException(ValidationException::class);
    //     $request = new UserLoginRequest;
    //     $request->id = null;
    //     $request->password = "hendy133";
    //     $this->userService->validateUserLoginRequest($request);
    // }

    // public function testValidationUserLoginRequestPasswordNull(): void{
    //     self::expectException(ValidationException::class);
    //     $request = new UserLoginRequest;
    //     $request->id = "123";
    //     $request->password = null;
    //     $this->userService->validateUserLoginRequest($request);
    // }

    // public function testLoginSuccess(): void{
    //     $user = new User("123", "hendy", "hendy123");
    //     $this->userRepository->expects(self::once())->method("findById")->willReturn($user);
    //     $request = new UserLoginRequest();
    //     $request->id=$user->getId();
    //     $request->password=$user->getPassword();
    //     $response = $this->userService->login($request);
    //     self::assertEquals($user, $response->user);
    // }

    public function testLoginUsernameOrPasswordWrong(): void{
        self::expectException(ValidationException::class);
        $this->userRepository->expects(self::once())->method("findById")->willReturn(null);
        $request = new UserLoginRequest();
        $request->id="123";
        $request->password="hendy123";
        $this->userService->login($request);
    }

    public function testLoginRequestNotValid(): void{
        self::expectException(ValidationException::class);
        $this->userRepository->expects(self::never())->method("findById")->willReturn(null);
        $request = new UserLoginRequest();
        $request->id="";
        $request->password=null;
        $this->userService->login($request);
    }

    public function testUpdateProfileSuccess(): void{
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
        $this->userService = new UserService($this->userRepository);
        
        $user = new User("123", "hendy", password_hash("hendy123", PASSWORD_BCRYPT));
        $this->userRepository->save($user);
        $beforeUpdate = $this->userRepository->findById($user->getId());
        self::assertEquals($user, $beforeUpdate);
        $user->setUsername("joko");
        $request = new UserProfileUpdateRequest();
        $request->id = $user->getId();
        $request->username = $user->getUsername();
        $response = $this->userService->updateProfile($request);
        $afterUpdate = $this->userRepository->findById($user->getId());
        self::assertNotEquals($beforeUpdate, $afterUpdate);
        self::assertEquals($user, $response->user);
        self::assertEquals($afterUpdate, $response->user);
    }
    
    public function testUpdateProfileValidationError(): void{
        self::expectException(ValidationException::class);
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
        $this->userService = new UserService($this->userRepository);
        $request = new UserProfileUpdateRequest();
        $request->id = "12345";
        $request->username = "";
        $response = $this->userService->updateProfile($request);
    }
    public function testUpdateProfileNotFound(): void{
        self::expectException(ValidationException::class);
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
        $this->userService = new UserService($this->userRepository);
        $request = new UserProfileUpdateRequest();
        $request->id = "not found";
        $request->username = "hendy";
        $response = $this->userService->updateProfile($request);
    }
    
    // public function testChangePasswordSuccess(): void{
    //     $this->userRepository = new UserRepository(Database::getConnection());
    //     $this->userService = new UserService($this->userRepository);
    //     $this->userRepository->deleteAll();

    //     $user = new User("123", "hendy", password_hash("hendy123", PASSWORD_BCRYPT));
    //     $this->userRepository->save($user);

    //     $request = new UserPasswordChangeRequest();
    //     $request->id="123";
    //     $request->newPassword="hendynew123";
    //     $request->oldPassword="hendy123";
        
    //     $userBeforeChange = $this->userRepository->findById("123");
    //     self::assertTrue(password_verify("hendy123", $userBeforeChange->getPassword()));
    //     $response = $this->userService->changePassword($request);
    //     $userAfterChange = $this->userRepository->findById("123");
    //     self::assertTrue(password_verify("hendynew123", $userAfterChange->getPassword()));
    //     self::assertNotEquals($user, $response->user); 
    // }   

    public function testChangePasswordValidationError(): void{
        self::expectException(ValidationException::class);
        $request = new UserPasswordChangeRequest();
        $request->id="";
        $request->newPassword="";
        $request->oldPassword="";
        $this->userService->changePassword($request);
    }

    public function testChangePasswordValidationErrorPasswordToShort(): void{
        self::expectException(ValidationException::class);
        $request = new UserPasswordChangeRequest();
        $request->id="123";
        $request->newPassword="short";
        $request->oldPassword="hendy123";
        $this->userService->changePassword($request);
    }

    public function testChangePasswordUserNotFound(): void{
        self::expectException(ValidationException::class);
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userService = new UserService($this->userRepository);
        $this->userRepository->deleteAll();

        $user = new User("123", "hendy", password_hash("hendy123", PASSWORD_BCRYPT));

        $request = new UserPasswordChangeRequest();
        $request->id="123";
        $request->newPassword="hendynew123";
        $request->oldPassword="hendy123";

        $this->userService->changePassword($request);
    } 

    public function testChangePasswordOldPasswordWrong(): void{
        self::expectException(ValidationException::class);
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userService = new UserService($this->userRepository);
        $this->userRepository->deleteAll();

        $user = new User("123", "hendy", password_hash("hendy123", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $request = new UserPasswordChangeRequest();
        $request->id="123";
        $request->newPassword="hendynew123";
        $request->oldPassword="salah";

        $this->userService->changePassword($request);
    } 





}