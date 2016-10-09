<?php

use App\Core\Core;
use League\Container\Container;

error_reporting(E_ERROR | E_WARNING);
define('DEFAULT_SQLITE_DB_PATH', __DIR__.'/../addressbook.db');
define('DEFAULT_VIEW_DIRECTORY', __DIR__.'/../app/views');

require __DIR__.'/../vendor/autoload.php';

$container = new Container();

$core = new Core($container);

$routes = [
    ['GET', '/', '\App\Controllers\HomeController@welcome'],
    ['GET', '/list', '\App\Controllers\ContactController@index'],
    ['GET', '/add', '\App\Controllers\ContactController@create'],
    ['POST', '/add', '\App\Controllers\ContactController@store']
];


$core->registerRoutes($routes);

$core->run();



