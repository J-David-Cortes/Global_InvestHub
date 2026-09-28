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
    require_once('../modelos/modelos_v2/operacion_trading.php');
    require_once('../modelos/auth_middleware.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $operacionTrading = new OperacionTrading($conexion);

    $fo_usuario = exigirUsuarioAutenticado();

    switch($control){
        case 'resumen' :
            $vec = $operacionTrading->resumenPortfolio($fo_usuario);
        break;

        case 'abiertas' :
            $vec = $operacionTrading->posicionesAbiertas($fo_usuario);
        break;

        case 'cerradas' :
            $vec = $operacionTrading->posicionesCerradas($fo_usuario);
        break;

        case 'sharpe' :
            $vec = $operacionTrading->sharpeRatio($fo_usuario);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>
