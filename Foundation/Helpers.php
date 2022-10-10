<?php

function session() {
    return Container::get('base.http.session');
}

function input() {
    return Container::get('base.http.input');
}

function redirect() {
    return Container::get('base.http.redirect');
}

function assets() {
    return Container::get('base.html.assets');
}

function url() {
    return Container::get('base.html.url');
}

function dt($date) {
    return Container::get('base.html.date')->setDate($date);
}

function getRoute($route, $module) {
    $routes = Container::get($module.'.routes');
    if( ! isset($routes[$route])) {
        throw new \Exception('A rota informada não foi definida.');
    }

    return $routes[$route];
}

function getModule(){
    return \Foundation\Request::getModule();
}

function public_path(){
    return '/public/'.strtolower(getModule());
}

function public_root(){
    return '/public';
}