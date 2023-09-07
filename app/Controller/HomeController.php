<?php

namespace HendyNurSholeh\Controller;

use HendyNurSholeh\App\View;
use UserController;

class HomeController
{
    public function index(): void{
        View::render("Home/index", [
            "title" => "PHP Login Manajement"
        ]);
    }
}