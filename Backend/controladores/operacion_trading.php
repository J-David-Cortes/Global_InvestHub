<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Content-Type: application/json');

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/operacion_trading.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $operacionTrading = new OperacionTrading($conexion);

    // TEMPORAL: usuario fijo hasta que exista login real con sesion.
    // Reemplazar por el id del usuario autenticado cuando se implemente login.
    $fo_usuario = 2;

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
