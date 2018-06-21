<?php
// import de la classe Routing ( pour l'utiliser)
use BWB\Framework\mvc\Routing;
use BWB\Framework\mvc\EntitiesBuilder;

define("ROOT" , "../");

// pour beneficier de l'autoload de composer
include "../vendor/autoload.php";

// A chaque requete emise nous lançons le mecanisme de routage
(new Routing())->execute();

//new EntitiesBuilder();

function vardump($pWhat){
    echo "<pre>";
    var_dump($pWhat);
    echo "</pre>";
}

function printr($pWhat){
    echo "<pre>";
    print_r($pWhat);
    echo "</pre>";
}