<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Content-Type: application/json');

    // El navegador manda un preflight OPTIONS (sin Authorization) antes de
    // cualquier peticion real que incluya ese header. Si no se corta aqui,
    // el navegador bloquearia la peticion real como un fallo de CORS.
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(200);
        exit;
    }

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/broker.php'); 

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $broker = new Broker($conexion);

    switch($control){
        case 'consulta' :
            $vec = $broker->consulta();
        break;

        // TODO SEGURIDAD: insertar y editar no tienen ninguna restriccion de
        // acceso: cualquiera puede llamarlos desde fuera sin login. Antes de
        // cualquier uso real (mas alla de esta demo academica), deben exigir
        // un usuario autenticado con permiso de administrador.
        // (eliminar no lo necesita: la FK de usuario_bot ya lo frena.)
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $broker->insertar($params);
        break;

        case 'editar' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $broker->editar($id, $params);
        break;

        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $broker->eliminar($id);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>