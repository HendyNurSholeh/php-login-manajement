<?php
require_once __DIR__ . '/../vendor/autoload.php';

use HendyNurSholeh\App\Router;
use HendyNurSholeh\Config\Database;
use HendyNurSholeh\Controller\HomeController;
use HendyNurSholeh\Controller\UserController;

Database::getConnection("prod");

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/users/register', UserController::class, 'register');
Router::add('POST', '/users/register', UserController::class, 'postRegister');
Router::add('GET', '/users/login', UserController::class, 'login');
Router::add('POST', '/users/login', UserController::class, 'postLogin');
Router::add('GET', '/users/logout', UserController::class, 'logout');

Router::run();