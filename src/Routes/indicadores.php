<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Obtiene todas las ordenes de trabajos y lo devuelve
$app->get('/getAnalitoMuestras', function (Request $request, Response $response) {

    $consulta = "SELECT SUM(existe) FROM analito_muestra GROUP BY nombreAnalito";
    try {
        // Instanciar la base de datos
        $db = new db();

        // Conectarse a la base de datos
        $db = $db->connect();

        $ejecutar = $db->query($consulta);
        $result = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array (
            "message" => $e->getMessage()
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
        ;
    }
});

$app->get('/getAnalitosCompositos', function (Request $request, Response $response) {

    $consulta = "SELECT SUM(existe) FROM analito_composito GROUP BY nombreAnalito";
    try {
        // Instanciar la base de datos
        $db = new db();

        // Conectarse a la base de datos
        $db = $db->connect();

        $ejecutar = $db->query($consulta);
        $result = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array (
            "message" => $e->getMessage()
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
        ;
    }
});

$app->get('/getEstadosOrdenesTrabajo', function (Request $request, Response $response) {

    $consulta = "SELECT estado,COUNT(*) FROM orden_trabajo  GROUP BY estado ";
    try {
        // Instanciar la base de datos
        $db = new db();

        // Conectarse a la base de datos
        $db = $db->connect();

        $ejecutar = $db->query($consulta);
        $result = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array (
            "message" => $e->getMessage()
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
        ;
    }
});

