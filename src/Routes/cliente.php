<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Obtiene todas las ordenes de trabajos y lo devuelve
$app->get('/obtieneOrdenesTrabajo/{idEmpresa}', function (Request $request, Response $response, array $args) {

    $idEmpresa = $args['idEmpresa'];

    $consulta = "SELECT item, fechaAviso, jobIDColina, jobIDLiverpool, motonave, dv, nombreCliente, laboratorio, nombreMina, nombreMaterial, fechaRecepcion, fechaInicio, target, tiempoPendiente, fechaReal, estado, tat, cantMuestras, composito, analitosMuestras , analitosCompositos,certificadoALS, certificadoPesoSeco, wayBill, fechaWayBill, observacion  FROM orden_trabajo WHERE idCliente = $idEmpresa ORDER BY item DESC";
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