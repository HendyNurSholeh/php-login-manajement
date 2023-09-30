<?php
require_once __DIR__ . '/../vendor/autoload.php';

use HendyNurSholeh\App\Router;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Controller\HomeController;
use HendyNurSholeh\Controller\UserController;
use HendyNurSholeh\Middleware\MustLoginMiddleware;
use HendyNurSholeh\Middleware\MustNotLoginMiddleware;

Database::getConnection("prod");

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);

Router::run();