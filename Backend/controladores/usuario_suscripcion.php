<?php
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Content-Type: application/json');

    // El navegador manda un preflight OPTIONS (sin Authorization) antes de
    // cualquier peticion real que incluya ese header. Si no se corta aqui,
    // el middleware de mas abajo lo rechazaria con 401 y el navegador
    // bloquearia la peticion real como un fallo de CORS.
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(200);
        exit;
    }

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/usuario_suscripcion.php');
    require_once('../modelos/auth_middleware.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $usuarioSuscripcion = new UsuarioSuscripcion($conexion);

    $fo_usuario = exigirUsuarioAutenticado();

    switch($control){
        case 'consulta' :
            $vec = $usuarioSuscripcion->consulta();
        break;

        case 'planActual' :
            $vec = $usuarioSuscripcion->planActual($fo_usuario);
        break;

        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $usuarioSuscripcion->insertar($params);
        break;

        case 'editar' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $usuarioSuscripcion->editar($id, $params);
        break;

        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $usuarioSuscripcion->eliminar($id);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>