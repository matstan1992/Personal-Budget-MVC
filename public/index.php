<?php

/**
 * Front controller
 *
 * PHP version 7.3
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Home', 'action' => 'index']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('mainmenu', ['controller' => 'MainMenu', 'action' => 'index']);
$router->add('income', ['controller' => 'Income', 'action' => 'index']);
$router->add('expense', ['controller' => 'Expense', 'action' => 'index']);
$router->add('balance', ['controller' => 'Balance', 'action' => 'index']);
$router->add('settings', ['controller' => 'Settings', 'action' => 'index']);
$router->add('poprzedni-miesiac', ['controller' => 'Balance', 'action' => 'previousMonth']);
$router->add('biezacy-rok', ['controller' => 'Balance', 'action' => 'currentYear']);
$router->add('niestandardowy', ['controller' => 'Balance', 'action' => 'customPeriod']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);

$router->add('expense/getCategoryLimit/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getCategoryLimit']);
$router->add('expense/getExpensesDate/{id:[\d]+}/{date:(19|20|21)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])}', ['controller' => 'Expense', 'action' => 'getExpensesDate']);

$router->add('{controller}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);
