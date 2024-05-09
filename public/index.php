<?php
header('Access-Control-Allow-Origin:*'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Request-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

use Slim\Factory\AppFactory;


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Config/db.php';


$app = AppFactory::create();


$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

//Routes
require __DIR__ . '/src/Routes/login.php';
require __DIR__ . '/src/Routes/home.php';
require __DIR__ . '/src/Routes/cliente.php';
require __DIR__ . '/src/Routes/indicadores.php';

// Run app
$app->run();