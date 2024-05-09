<?php

header('Access-Control-Allow-Origin:*'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Request-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');


use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Config/db.php'; // Incluye la configuración de la base de datos si es necesario

$app = AppFactory::create();


// Define las rutas de tu aplicación
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

// Incluye las rutas definidas en otros archivos
require __DIR__ . '/src/Routes/home.php';
require __DIR__ . '/src/Routes/login.php';
require __DIR__ . '/src/Routes/cliente.php';
require __DIR__ . '/src/Routes/indicadores.php';

// Ejecuta la aplicación
$app->run();
