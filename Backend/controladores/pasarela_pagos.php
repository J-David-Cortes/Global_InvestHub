<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/pasarela_pagos.php');

    $control = $_GET['control'];
    $pasarela_pagos = new pasarela_pagos($conexion);

    switch($control){
        case 'consulta' :
            $vec = $pasarela_pagos->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $pasarela_pagos->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $pasarela_pagos->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $pasarela_pagos->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>