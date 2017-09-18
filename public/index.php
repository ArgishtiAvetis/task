<?php

session_start();


// TWIG templating engine
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Autoload classes
spl_autoload_register(function($class) {
	$root = dirname(__DIR__);
	$file = $root . '/' . str_replace('\\', '/', $class) . '.php';
	if (is_readable($file)) {
		require $root . '/' . str_replace('\\', '/', $class) . '.php';
	}
});


// Routing

require '../Core/Router.php';

$router = new Core\Router();

// main public routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('account/users/login', ['controller' => 'Users', 'action' => 'login', 'namespace' => 'User']);
$router->add('account/users/signup', ['controller' => 'Users', 'action' => 'signup', 'namespace' => 'User']);

// main web app routes 
// example routes: 'users/dashboard', 'users/account', 'users/delete'  
$router->add('{controller}/{action}', ['namespace' => 'Admin']);
$router->add('account/{controller}/{action}', ['controller' => 'Users', 'action' => 'account', 'namespace' => 'User']);
$router->add('logout', ['controller' => 'Users', 'action' => 'logout', 'namespace' => 'User']);
// not yet :)
//$router->add('{controller}/{id:\d+}/{action}', ['namespace' => 'User']);

$router->dispatch($_SERVER['QUERY_STRING']);


