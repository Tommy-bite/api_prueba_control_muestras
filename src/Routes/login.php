<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/login', function (Request $request, Response $response) {
    $correo = $request->getParam('correo');
    $password = $request->getParam('password');

    try {

        //Instanciar la base de datos
        $db = new db();

        //Conectarse
        $conn = $db->connect();

        //Se obtiene el hash de la password del usuario
        $consulta = "SELECT password FROM usuario WHERE correo = ? AND idEstado = '1' ";
        $stmt = $conn->prepare($consulta);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                // Si la contraseña coincide, obtén el resto de la información del usuario, excluyendo la contraseña
                $consultaInfo = "SELECT u.idUsuario, u.nombre, u.apellido, u.correo, u.telefono, u.idCargo, u.idEmpresa, c.nombreCargo FROM usuario AS u INNER JOIN cargo AS c ON u.idCargo = c.idCargo WHERE u.correo = ?";
                $stmtInfo = $conn->prepare($consultaInfo);
                $stmtInfo->execute([$correo]);
                $userInfo = $stmtInfo->fetch(PDO::FETCH_OBJ);

                $response->getBody()->write(json_encode($userInfo));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                // Contraseña incorrecta
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Credenciales incorrectas']));
            }
        } else {
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Usuario no encontrado']));
        }

    } catch (\Throwable $th) {
        $error = array ("message" => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

});