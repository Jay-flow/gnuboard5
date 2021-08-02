<?php

require './vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', 'Users/index');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    $r->addRoute('GET', '/', 'Index');
    $r->addRoute('GET', '/install', 'Install/index');
    $r->addRoute('GET', '/kim', 'Kim/index');
    $r->addRoute('GET', '/php8', 'Php8/test');

});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        // list($class, $method) = explode("/", $handler, 2);
        // call_user_func_array(array(new $class, $method), $vars);
        $handler = trim($handler);
        if (strpos($handler, "/") === false) {
            $func = $handler; // 함수
            call_user_func_array($func, $vars);
        } else {
            // $method 는 $class 에 연관된 함수
            list($class, $method) = explode("/", $handler, 2);
            call_user_func_array(array(new $class, $method), $vars);
        }
        break;
}

class Users
{
    function index() {
        echo "Users/index";
    }
}

// class Index
// {
//     function __construct()
//     {
//         echo "Index __construct()";
//     }
// }

function Index() {
    echo "Index";
}

function get_user_handler($id) {
    echo "get_user_handler($id)";
}

class Install
{
    function index() {
        echo "1";
        include("./setup/main.php");
        echo "2";
    }
}

class Kim
{
	function index() {
		echo "kim/index";
	}
}

class Php8
{
	function test() {
		include "./test.php";
	}
}
