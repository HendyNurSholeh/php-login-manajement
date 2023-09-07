<?php
namespace HendyNurSholeh\App;
use PHPUnit\Framework\TestCase;
class ViewTest extends TestCase{
    public function testRender(): void{
        View::render("Home/index", ["title"=>"Hendy Ganteng"]);
        self::expectOutputRegex("[Hendy Ganteng]");
        self::expectOutputRegex("[Hendy Nur Sholeh]");
        self::expectOutputRegex("[Login]");
        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[html]");
        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[Login]");
    }
}