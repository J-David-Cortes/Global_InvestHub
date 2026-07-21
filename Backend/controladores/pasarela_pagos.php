<?php
    // Configura los encabezados HTTP para permitir solicitudes desde cualquier origen (CORS) y evitar bloqueos en el navegador
    header('Access-Control-Allow-Origin: *');
    
    // Define los encabezados permitidos en las peticiones HTTP (como orígenes, tipos de contenido y tokens de aceptación)
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    
    // Especifica que la respuesta del servidor se entregará estrictamente en formato JSON
    header('Content-Type: application/json');

    // Importa el archivo global que establece la conexión activa con la base de datos MySQL
    require_once('../modelos/conexion.php');
    
    // Importa el archivo del modelo específico que contiene toda la lógica de negocio para la pasarela de pagos
    require_once('../modelos/modelos_v2/pasarela_pagos.php'); 

    // Captura mediante el método GET el parámetro 'control' enviado por la URL; si no existe, lo deja vacío
    $control = isset($_GET['control']) ? $_GET['control'] : '';
    
    // Instancia el objeto de la clase del modelo pasándole la conexión activa a la base de datos
    $pasarelaPagos = new PasarelaPagos($conexion);

    // Estructura de control condicional que evalúa la acción solicitada por el cliente
    switch($control){
        
        // Caso para listar o consultar todos los registros almacenados en la tabla
        case 'consulta' :
            $vec = $pasarelaPagos->consulta(); // Ejecuta el método consulta del modelo y guarda el vector resultante
        break;

        // Caso para insertar o registrar un nuevo elemento enviado desde la interfaz
        case 'insertar' :
            $json = file_get_contents('php://input'); // Lee el flujo de datos crudos (JSON) enviado en el cuerpo de la petición HTTP
            $params = json_decode($json); // Convierte la cadena JSON en un objeto de PHP manipulable
            $vec = $pasarelaPagos->insertar($params); // Ejecuta el método de inserción enviándole los parámetros decodificados
        break;

        // Caso para modificar o actualizar un registro existente
        case 'editar' :
            $json = file_get_contents('php://input'); // Lee los nuevos datos enviados en formato JSON desde el cliente
            $id = isset($_GET['id']) ? $_GET['id'] : 0; // Captura el ID del registro a editar desde la URL; por defecto asigna 0 si no viene
            $params = json_decode($json); // Decodifica el JSON de entrada a un objeto PHP
            $vec = $pasarelaPagos->editar($id, $params); // Ejecuta el método de actualización pasándole tanto el ID como los nuevos parámetros
        break;

        // Caso para eliminar un registro específico del sistema
        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0; // Captura el identificador único del registro que se desea borrar desde la URL
            $vec = $pasarelaPagos->eliminar($id); // Ejecuta el método de borrado enviándole el ID correspondiente
        break;

        // Caso por defecto que se activa si la variable 'control' no coincide con ninguna de las opciones anteriores
        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado']; // Devuelve un arreglo asociativo con el reporte del fallo
        break;
    }

    // Codifica el arreglo o vector de resultados final a formato JSON y lo imprime como respuesta HTTP definitiva
    echo json_encode($vec);
?>