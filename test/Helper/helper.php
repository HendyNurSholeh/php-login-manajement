<?php 
namespace HendyNurSholeh\App{
    function header($value){
        echo "$value";
}
}
namespace HendyNurSholeh\Service{
    function setCookie(string $name, string $value, $optional = []){
        $_COOKIE[$name] = $value;
    }
}
?>