<?php

function getDatabaseConfig(): array{
    return [
        "database" => [
            "prod" =>[
                "url" => "mysql:host=localhost:3306;dbname=php_login_manajement",
                "username" => "root",
                "password" => ""
            ],
            "test" =>[
                "url" => "mysql:host=localhost:3306;dbname=php_login_manajement_test",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];
}