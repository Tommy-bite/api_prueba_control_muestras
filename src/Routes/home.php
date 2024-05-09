<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* Funciones */
// SEPARA LOS STRING DE LOS NUMEROS
function obtenerNumeros($cadena)
{
    preg_match_all('!\d+!', $cadena, $coincidencias);
    return $coincidencias[0];
}


/* RUTAS */
//Obtiene el JobIDColina y lo devuelve
$app->get('/obtieneJobId', function (Request $request, Response $response) {

    $consulta = "SELECT item 
    FROM orden_trabajo 
    ORDER BY item DESC 
    LIMIT 1
    ";
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

//Guardar un Orden de trabajo
$app->post('/guardarOT', function (Request $request, Response $response) {
    $fechaAviso = $request->getParam('fechaAviso');
    $jobIDColina = $request->getParam('jobIDColina');
    $jobIDLiverpool = $request->getParam('jobIDLiverpool');
    $idCliente = $request->getParam('idCliente');
    $motoNave = $request->getParam('motoNave');
    $dv = $request->getParam('dv');
    $idMina = $request->getParam('idMina');
    $idMaterial = $request->getParam('idMaterial');
    $laboratorio = $request->getParam('laboratorio');
    $cantMuestras = $request->getParam('cantMuestras');
    $codigoWayBill = $request->getParam('codigoWayBill');
    $fechaWayBill = $request->getParam('fechaWayBill');
    $estado = 'NO INICIADO';

    /**
     * OBTIENE TIPOS DE ANALITOS
     */
    $cobreMaterial = $request->getParam('cobreMaterial');
    $arsenicoMaterial = $request->getParam('arsenicoMaterial');
    $plataMaterial = $request->getParam('plataMaterial');
    $oroMaterial = $request->getParam('oroMaterial');
    $moligdenoMaterial = $request->getParam('moligdenoMaterial');

    $cobreComposito = $request->getParam('cobreComposito');
    $arsenicoComposito = $request->getParam('arsenicoComposito');
    $plataComposito = $request->getParam('plataComposito');
    $oroComposito = $request->getParam('oroComposito');
    $moligdenoComposito = $request->getParam('moligdenoComposito');


    if ($cobreMaterial) {
        $cobreMaterial = 1;
        $nomCobreMaterial = 'Cobre';
    } else {
        $cobreMaterial = 0;
        $nomCobreMaterial = '';
    }
    if ($arsenicoMaterial) {
        $arsenicoMaterial = 1;
        $nomArsenicoMaterial = 'Arsenico';

    } else {
        $arsenicoMaterial = 0;
        $nomArsenicoMaterial = '';
    }
    if ($plataMaterial) {
        $plataMaterial = 1;
        $nomPlataMaterial = 'Plata';
    } else {
        $plataMaterial = 0;
        $nomPlataMaterial = '';
    }
    if ($oroMaterial) {
        $oroMaterial = 1;
        $nomOroMaterial = 'Oro';
    } else {
        $oroMaterial = 0;
        $nomOroMaterial = '';
    }
    if ($moligdenoMaterial) {
        $moligdenoMaterial = 1;
        $nomMoligdenoMaterial = 'Molibdeno';
    } else {
        $moligdenoMaterial = 0;
        $nomMoligdenoMaterial = '';
    }

    // Datos a insertar
    $datosAnalitosMuestra = array (
        'Cobre' => $cobreMaterial,
        'Arsenico' => $arsenicoMaterial,
        'Plata' => $plataMaterial,
        'Oro' => $oroMaterial,
        'Molibdeno' => $moligdenoMaterial
    );

    if ($cobreComposito) {
        $cobreComposito = 1;
        $nomCobreComposito = 'Cobre';
    } else {
        $cobreComposito = 0;
        $nomCobreComposito = '';
    }
    if ($arsenicoComposito) {
        $arsenicoComposito = 1;
        $nomArsenicoComposito = 'Arsenico';
    } else {
        $arsenicoComposito = 0;
        $nomArsenicoComposito = '';
    }
    if ($plataComposito) {
        $plataComposito = 1;
        $nomPlataComposito = 'Plata';
    } else {
        $plataComposito = 0;
        $nomPlataComposito = '';
    }
    if ($oroComposito) {
        $oroComposito = 1;
        $nomCobreComposito = 'Oro';
    } else {
        $oroComposito = 0;
        $nomCobreComposito = '';
    }
    if ($moligdenoComposito) {
        $moligdenoComposito = 1;
        $nomMoligdenoComposito = 'Molibdeno';
    } else {
        $moligdenoComposito = 0;
        $nomMoligdenoComposito = '';
    }

    // Datos a insertar
    $datosAnalitosComposito = array (
        'Cobre' => $cobreComposito,
        'Arsenico' => $arsenicoComposito,
        'Plata' => $plataComposito,
        'Oro' => $oroComposito,
        'Molibdeno' => $moligdenoComposito
    );

    $analitosMuestras = $nomCobreMaterial . " " . $nomArsenicoMaterial . " " . $nomPlataMaterial . " " . $nomOroMaterial . " " . $nomMoligdenoMaterial;
    $analitosCompositos = $nomCobreComposito . " " . $nomArsenicoComposito . " " . $nomPlataComposito . " " . $nomCobreComposito . " " . $nomMoligdenoComposito;

    if ($fechaWayBill === '') {
        // Si está vacío, establecerlo como NULL en lugar de '000-000-0'
        $fechaWayBill = null;
    }

    // SE BAJA 1 MUESTRA Y SE AGREGA 1 COMPOSITO
    $composito = 1;
    $cantMuestras = $cantMuestras - 1;

    // Codigo para optener el valor del campo item
    $itemArray = obtenerNumeros($jobIDColina);
    $itemString = implode('', $itemArray); // Convierte el array en una cadena sin separadores
    $itemNumero = intval($itemString); // Convierte la cadena en un entero

    try {
        // Instanciar la base de datos
        $db = new db();
        // Conexión
        $db = $db->connect();

        // Iterar sobre los datos y realizar la inserción
        foreach ($datosAnalitosMuestra as $nombreAnalito => $exite) {
            // Insertar en la tabla
            $consulta = "INSERT INTO analito_muestra (nombreAnalito, existe, idOrdenTrabajo) VALUES (?, ?, ?)";
            $ejecutar = $db->prepare($consulta);
            $ejecutar->bindParam(1, $nombreAnalito, PDO::PARAM_STR);
            $ejecutar->bindParam(2, $exite, PDO::PARAM_INT);
            $ejecutar->bindParam(3, $itemNumero, PDO::PARAM_INT);
            $ejecutar->execute();
        }

        // Iterar sobre los datos y realizar la inserción
        foreach ($datosAnalitosComposito as $nombreAnalito => $exite) {
            // Insertar en la tabla
            $consulta = "INSERT INTO analito_composito (nombreAnalito, existe, idOrdenTrabajo) VALUES (?, ?, ?)";
            $ejecutar = $db->prepare($consulta);
            $ejecutar->bindParam(1, $nombreAnalito, PDO::PARAM_STR);
            $ejecutar->bindParam(2, $exite, PDO::PARAM_INT);
            $ejecutar->bindParam(3, $itemNumero, PDO::PARAM_INT);
            $ejecutar->execute();
        }

        // SE TRAE EL NOMBRE DE LA EMPRESA
        $consulta = "SELECT nombreEmpresa FROM empresa WHERE idEmpresa = ?";
        $ejecutar = $db->prepare($consulta);
        $ejecutar->execute([$idCliente]); // Pasar directamente el valor de $idCliente
        $cliente = $ejecutar->fetch(PDO::FETCH_ASSOC);
        $nombreCliente = $cliente['nombreEmpresa'];

        // SE TRAE EL NOMBRE DE LA MINA
        $consulta = "SELECT nombreMina FROM mina WHERE idMina = ?";
        $ejecutar = $db->prepare($consulta);
        $ejecutar->execute([$idMina]); // Pasar directamente el valor de $idMina
        $mina = $ejecutar->fetch(PDO::FETCH_ASSOC);
        $nombreMina = $mina['nombreMina'];

        // SE TRAE EL NOMBRE DEL MATERIAL
        $consulta = "SELECT nombreMaterial FROM material WHERE idMaterial = ?";
        $ejecutar = $db->prepare($consulta);
        $ejecutar->execute([$idMaterial]); // Pasar directamente el valor de $idMaterial
        $material = $ejecutar->fetch(PDO::FETCH_ASSOC);
        $nombreMaterial = $material['nombreMaterial'];

        $idCliente = intval($idCliente);
        $idMina = intval($idMina);
        $idMaterial = intval($idMaterial);

        $adjuntaCertificadoALS = 'No';
        $adjuntaCertificadoPesoSeco = 'No';

        if ($jobIDColina !== '' && $fechaAviso !== '' && $itemNumero !== '') {
            $consulta = "INSERT INTO orden_trabajo (item, fechaAviso, jobIDColina, jobIDLiverpool, motoNave, dv, idCliente, nombreCliente, laboratorio, idMina, nombreMina, idMaterial, nombreMaterial, estado, cantMuestras, composito, analitosMuestras, analitosCompositos ,wayBill, fechaWayBill,  certificadoALS ,certificadoPesoSeco) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $ejecutar = $db->prepare($consulta);
            $ejecutar->bindParam(1, $itemNumero, PDO::PARAM_INT);
            $ejecutar->bindParam(2, $fechaAviso, PDO::PARAM_STR);
            $ejecutar->bindParam(3, $jobIDColina, PDO::PARAM_STR);
            $ejecutar->bindParam(4, $jobIDLiverpool, PDO::PARAM_STR);
            $ejecutar->bindParam(5, $motoNave, PDO::PARAM_STR);
            $ejecutar->bindParam(6, $dv, PDO::PARAM_STR);
            $ejecutar->bindParam(7, $idCliente, PDO::PARAM_INT);
            $ejecutar->bindParam(8, $nombreCliente, PDO::PARAM_STR);
            $ejecutar->bindParam(9, $laboratorio, PDO::PARAM_STR);
            $ejecutar->bindParam(10, $idMina, PDO::PARAM_INT);
            $ejecutar->bindParam(11, $nombreMina, PDO::PARAM_STR);
            $ejecutar->bindParam(12, $idMaterial, PDO::PARAM_INT);
            $ejecutar->bindParam(13, $nombreMaterial, PDO::PARAM_STR);
            $ejecutar->bindParam(14, $estado, PDO::PARAM_STR);
            $ejecutar->bindParam(15, $cantMuestras, PDO::PARAM_INT);
            $ejecutar->bindParam(16, $composito, PDO::PARAM_INT);
            $ejecutar->bindParam(17, $analitosMuestras, PDO::PARAM_STR);
            $ejecutar->bindParam(18, $analitosCompositos, PDO::PARAM_STR);
            $ejecutar->bindParam(19, $codigoWayBill, PDO::PARAM_STR);
            $ejecutar->bindParam(20, $fechaWayBill, PDO::PARAM_STR);
            $ejecutar->bindParam(21, $adjuntaCertificadoALS, PDO::PARAM_STR);
            $ejecutar->bindParam(22, $adjuntaCertificadoPesoSeco, PDO::PARAM_STR);
            $ejecutar->execute();
        }


        // Realizar la segunda consulta
        $consulta2 = "SELECT * FROM orden_trabajo ";
        $ejecutar2 = $db->query($consulta2);
        $ordenesTrabajos = $ejecutar2->fetchAll(PDO::FETCH_OBJ);

        $db = null;

        // Devolver la lista de usuarios
        $response->getBody()->write(json_encode($ordenesTrabajos));
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

//Obtiene los datos de los clientes
$app->get('/obtieneClientes', function (Request $request, Response $response) {

    $consulta = "SELECT idEmpresa, nombreEmpresa
    FROM empresa; 
    ";
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

//Obtiene los datos de las minas
$app->get('/obtieneMinas', function (Request $request, Response $response) {

    $consulta = "SELECT *
    FROM mina
    ";
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

//Obtiene los datos de los materiales
$app->get('/obtieneMateriales', function (Request $request, Response $response) {

    $consulta = "SELECT *
    FROM material
    ";
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


//Obtiene todas las ordenes de trabajos y lo devuelve
$app->get('/obtieneOrdenesTrabajo', function (Request $request, Response $response) {

    $consulta = "SELECT item, fechaAviso, jobIDColina, jobIDLiverpool, motonave, dv, nombreCliente, laboratorio, nombreMina, nombreMaterial, fechaRecepcion, fechaInicio, target, tiempoPendiente, fechaReal, estado, tat, cantMuestras, composito, analitosMuestras , analitosCompositos,certificadoALS, certificadoPesoSeco, wayBill, fechaWayBill, observacion  FROM orden_trabajo ORDER BY item DESC";
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

$app->get('/obtieneOrdenTrabajo/{item}', function (Request $request, Response $response) {
    $item = $request->getAttribute('item');

    $consulta = "SELECT * FROM orden_trabajo WHERE item = $item";
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
    }
});


$app->put('/editarOT', function (Request $request, Response $response) {
    $item = $request->getParam('item');
    $fechaRecepcion = $request->getParam('fechaRecepcion');
    $fechaInicio = $request->getParam('fechaInicio');

    if ($fechaRecepcion || $fechaInicio) {
        $estado = 'EN EJECUCION';
    }

    try {
        $db = new db();
        $db = $db->connect();

        $consulta = " UPDATE orden_trabajo SET fechaRecepcion = :fechaRecepcion, fechaInicio = :fechaInicio ,estado = :estado  WHERE item = :item";

        if ($item) {
            $parametros = ['fechaRecepcion' => $fechaRecepcion, 'fechaInicio' => $fechaInicio, 'estado' => $estado, 'item' => $item];
            $ejecutar = $db->prepare($consulta);
            $result = $ejecutar->execute($parametros);
        }

        // Realizar la segunda consulta
        $consulta2 = "SELECT * FROM orden_trabajo WHERE item = $item ";
        $ejecutar2 = $db->query($consulta2);
        $orden_trabajo = $ejecutar2->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        // Devolver la lista de usuarios
        $response->getBody()->write(json_encode($orden_trabajo));
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
    }
});

// Función para generar un nombre de archivo único
function getUniqueFileName($directory, $filename)
{
    $now = new DateTime();
    $uniqueId = uniqid();
    return $now->format('YmdHis') . '_' . $uniqueId . '_' . $filename;
}

$app->post('/addArchivos', function (Request $request, Response $response) {
    $db = new db();
    $db = $db->connect();

    $parsedBody = $request->getParsedBody();
    $item = $parsedBody['item'];
    $target = $parsedBody['target'];
    $target = $target - 1;
    $uploadedFiles = $request->getUploadedFiles();
    $certificadoALS = $uploadedFiles['certificadoALS'] ?? null;
    $certificadoPesoSeco = $uploadedFiles['certificadoPesoSeco'] ?? null;

    // Directorio para subir archivos
    $uploadsDirectory = './src/File/uploads/';
    if (!is_dir($uploadsDirectory)) {
        mkdir($uploadsDirectory, 0755, true);
    }

    // Inicializar nombres de archivo
    $filename = $filenameDos = null; // Modificado para incluir $filenameTres

    try {

        //Trae fecha de recepcion de la OT
        $consulta = "SELECT * FROM orden_trabajo WHERE item = ?";
        $ejecutar = $db->prepare($consulta);
        $ejecutar->execute([$item]); // Pasar directamente el valor de $idCliente
        $ordenTrabajo = $ejecutar->fetch(PDO::FETCH_ASSOC);
        $fechaRecepcion = $ordenTrabajo['fechaRecepcion'];
        date_default_timezone_set('America/Santiago');
        $fechaReal = new DateTime('now');

        $fechaReal = new DateTime('now');
        $fechaRecepcion = DateTime::createFromFormat('Y-m-d', $fechaRecepcion);
        // $target = $fechaReal->diff($fechaRecepcion);
        $fechaReal = $fechaReal->format('Y-m-d');
        // $target = strval($target->days);


        if ($certificadoALS && $certificadoALS->getError() === UPLOAD_ERR_OK) {
            $estado = 'FINALIZADO';
            $tipoArchivo = 'Certificado ALS';
            $filename = getUniqueFileName($uploadsDirectory, $certificadoALS->getClientFilename());
            $certificadoALS->moveTo($uploadsDirectory . $filename);
            $insertStatement = $db->prepare("INSERT INTO adjunto (nombreArchivo, itemOrdenTrabajo, tipoArchivo) VALUES (?,  ?, ?)");
            $insertStatement->execute([$filename, $item, $tipoArchivo]);

            //Actualiza estado tabla ordenes de trabajo
            $consulta = " UPDATE orden_trabajo SET estado = :estado, certificadoALS = 'Si' WHERE item = :item";
            $parametros = ['estado' => $estado, 'item' => $item];
            $ejecutar = $db->prepare($consulta);
            $result = $ejecutar->execute($parametros);
        }

        if ($certificadoPesoSeco && $certificadoPesoSeco->getError() === UPLOAD_ERR_OK) {
            $tipoArchivo = 'Certificado Peso Seco';
            $filename = getUniqueFileName($uploadsDirectory, $certificadoPesoSeco->getClientFilename());
            $certificadoPesoSeco->moveTo($uploadsDirectory . $filename);
            $insertStatement = $db->prepare("INSERT INTO adjunto (nombreArchivo, itemOrdenTrabajo, tipoArchivo) VALUES (?,  ?, ?)");
            $insertStatement->execute([$filename, $item, $tipoArchivo]);

            //Actualiza estado tabla ordenes de trabajo
            $consulta = " UPDATE orden_trabajo SET  certificadoPesoSeco = 'Si' WHERE item = :item";
            $parametros = ['item' => $item];
            $ejecutar = $db->prepare($consulta);
            $result = $ejecutar->execute($parametros);
        }

        //Actualiza tabla ordenes de trabajo
        $consulta = " UPDATE orden_trabajo SET fechaReal = :fechaReal , target = :target  WHERE item = :item";
        $parametros = ['fechaReal' => $fechaReal, 'target' => $target, 'item' => $item];
        $ejecutar = $db->prepare($consulta);
        $result = $ejecutar->execute($parametros);

        $db = null;

        $response->getBody()->write(json_encode($ordenTrabajo));
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
    }
});


$app->get('/getPdf/{item}/{tipo}', function (Request $request, Response $response, array $args) {
    $db = new db();
    $db = $db->connect();

    $item = $args['item'];
    $tipo = $args['tipo'];

    if ($tipo == 'als') {
        $tipo = 'Certificado ALS';
    } else {
        $tipo = 'Certificado Peso Seco';
    }

    try {
        $stmt = $db->prepare("SELECT nombreArchivo FROM adjunto WHERE itemOrdenTrabajo = :item AND tipoArchivo = :tipo");
        $stmt->bindParam(':item', $item, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();

        $adjunto = $stmt->fetch(PDO::FETCH_OBJ);

        if ($adjunto) {
            $rutaArchivo = __DIR__ . "/../File/uploads/{$adjunto->nombreArchivo}";
            if (file_exists($rutaArchivo)) {
                $response = $response->withHeader('Content-Type', 'application/pdf');
                $response->getBody()->write(file_get_contents($rutaArchivo));
                return $response->withStatus(200);
            } else {
                // Si el archivo no existe, devuelve un mensaje de error
                $response->getBody()->write('No se encuentra el archivo');
                return $response->withStatus(404);
            }
        } else {
            // Si no se encuentra ningún archivo, devuelve un mensaje de error
            $response->getBody()->write('No se encuentra el archivo');
            return $response->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = ["success" => false, "message" => $e->getMessage()];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    } finally {
        $db = null; // Cerrar la conexión a la base de datos
    }
});

$app->put('/addObservaciones', function (Request $request, Response $response) {

    $item = $request->getParam('item');
    $observaciones = $request->getParam('observaciones');

    $db = new db();
    $db = $db->connect();
    try {

        $update = "UPDATE orden_trabajo SET observacion = :observaciones WHERE item = :item";

        if ($item) {
            $parametros = ['observaciones' => $observaciones, 'item' => $item,];
            $ejecutar = $db->prepare($update);
            $result = $ejecutar->execute($parametros);
        }

        // Realizar la segunda consulta
        $consulta2 = "SELECT * FROM orden_trabajo WHERE item = $item ";
        $ejecutar2 = $db->query($consulta2);
        $orden_trabajo = $ejecutar2->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        // Devolver la lista de usuarios
        $response->getBody()->write(json_encode($orden_trabajo));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {
        $error = ["success" => false, "message" => $e->getMessage()];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    } finally {
        $db = null; // Cerrar la conexión a la base de datos
    }

});

$app->post('/anularOT', function (Request $request, Response $response) {

    $parsedBody = $request->getParsedBody();
    $item = $parsedBody['item'];
    $justificacion = $parsedBody['justificacion'];

    $uploadedFiles = $request->getUploadedFiles();
    $evidencia1 = $uploadedFiles['evidencia1'] ?? null;
    $evidencia2 = $uploadedFiles['evidencia2'] ?? null;

    $estado = 'ANULADA';

    $db = new db();
    $db = $db->connect();
    try {

        $update = "UPDATE orden_trabajo SET observacion = :justificacion, estado = :estado WHERE item = :item";

        $uploadsDirectory = './src/File/images/';
        if (!is_dir($uploadsDirectory)) {
            mkdir($uploadsDirectory, 0755, true);
        }

        if ($item) {
            $parametros = ['justificacion' => $justificacion, 'estado' => $estado, 'item' => $item];
            $ejecutar = $db->prepare($update);
            $result = $ejecutar->execute($parametros);
        }

        if ($evidencia1->getError() === UPLOAD_ERR_OK) {
            $tipoArchivo = 'Evidencia1';
            $filename = getUniqueFileName($uploadsDirectory, $evidencia1->getClientFilename());
            $evidencia1->moveTo($uploadsDirectory . $filename);
            $insertStatement = $db->prepare("INSERT INTO adjunto (nombreArchivo, itemOrdenTrabajo, tipoArchivo) VALUES (?,  ?, ?)");
            $insertStatement->execute([$filename, $item, $tipoArchivo]);
        }

        if ($evidencia2->getError() === UPLOAD_ERR_OK) {
            $tipoArchivo = 'Evidencia2';
            $filename = getUniqueFileName($uploadsDirectory, $evidencia2->getClientFilename());
            $evidencia2->moveTo($uploadsDirectory . $filename);
            $insertStatement = $db->prepare("INSERT INTO adjunto (nombreArchivo, itemOrdenTrabajo, tipoArchivo) VALUES (?,  ?, ?)");
            $insertStatement->execute([$filename, $item, $tipoArchivo]);
        }

        // Realizar la segunda consulta
        $consulta2 = "SELECT * FROM orden_trabajo WHERE item = $item ";
        $ejecutar2 = $db->query($consulta2);
        $orden_trabajo = $ejecutar2->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        // Devolver la lista de usuarios
        $response->getBody()->write(json_encode($orden_trabajo));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {
        $error = ["success" => false, "message" => $e->getMessage()];
        $response->getBody()->write(json_encode($uploadedFiles));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    } finally {
        $db = null; // Cerrar la conexión a la base de datos
    }


});


$app->get('/getEvidencias/{item}/{tipo}', function (Request $request, Response $response, array $args) {
    $db = new db();
    $db = $db->connect();

    $item = $args['item'];
    $tipo = $args['tipo'];

    try {
        $stmt = $db->prepare("SELECT nombreArchivo FROM adjunto WHERE itemOrdenTrabajo = :item AND tipoArchivo = :tipo");
        $stmt->bindParam(':item', $item, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();

        $adjunto = $stmt->fetch(PDO::FETCH_OBJ);

        if ($adjunto) {
            $rutaArchivo = __DIR__ . "/../File/images/{$adjunto->nombreArchivo}";
            if (file_exists($rutaArchivo)) {
                $tipoContenido = mime_content_type($rutaArchivo);
                $response = $response->withHeader('Content-Type', $tipoContenido);
                $response->getBody()->write(file_get_contents($rutaArchivo));
                return $response->withStatus(200);
            } else {
                // Si el archivo no existe, devuelve un mensaje de error
                $response->getBody()->write('No se encuentra el archivo');
                return $response->withStatus(404);
            }
        } else {
            // Si no se encuentra ningún archivo, devuelve un mensaje de error
            $response->getBody()->write('No se encuentra el archivo');
            return $response->withStatus(202);
        }
    } catch (PDOException $e) {
        $error = ["success" => false, "message" => $e->getMessage()];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    } finally {
        $db = null; // Cerrar la conexión a la base de datos
    }
});

//Obtiene los analitos muestras
$app->get('/getAnalitosMuestras/{item}', function (Request $request, Response $response, array $args) {

    $item = $args['item'];

    $consulta = "SELECT *
    FROM analito_muestra WHERE idOrdenTrabajo = $item
    ";
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

//Obtiene los analitos compositos
$app->get('/getAnalitosCompositos/{item}', function (Request $request, Response $response, array $args) {

    $item = $args['item'];

    $consulta = "SELECT *
    FROM analito_composito WHERE idOrdenTrabajo = $item
    ";
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