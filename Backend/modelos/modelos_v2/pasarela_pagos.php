<?php
    // Definición de la clase PasarelaPagos bajo el paradigma de Programación Orientada a Objetos (POO)
    class PasarelaPagos {
        
        // Variable privada para almacenar la conexión activa a la base de datos
        private $conexion;

        // Constructor que se ejecuta automáticamente al instanciar la clase, recibiendo la conexión
        public function __construct($conexion){
            $this->conexion = $conexion; // Asigna la conexión recibida a la variable local de la clase
        }

        // Método público para consultar y listar todos los registros de la tabla ordenados alfabéticamente
        public function consulta(){
            // Define la sentencia SQL para seleccionar el ID y el nombre
            $sql = "SELECT id_pasarela, nombre FROM pasarela_pagos ORDER BY nombre";
            
            // Ejecuta la consulta en MySQL; si ocurre un fallo, detiene la ejecución y muestra el error detallado
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta pasarela_pagos: " . mysqli_error($this->conexion));

            // Inicializa un arreglo vacío que almacenará los resultados obtenidos
            $vec = [];
            
            // Recorre cada fila devuelta por la base de datos transformándola en un arreglo asociativo
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row; // Añade cada registro al vector principal               
            }
            
            // Retorna el arreglo completo con todos los datos listos para ser consumidos
            return $vec;
        }

        // Método público para eliminar un registro específico filtrado por su ID único
        public function eliminar($id){
            // Construye la instrucción DELETE asegurando que el ID sea estrictamente un número entero
            $sql = "DELETE FROM pasarela_pagos WHERE id_pasarela = " . intval($id);
            
            // Ejecuta la orden de borrado en la base de datos o detiene la ejecución si hay error
            mysqli_query($this->conexion, $sql) or die("Error al eliminar pasarela de pagos: " . mysqli_error($this->conexion));
            
            // Retorna un arreglo asociativo indicando el éxito de la operación y un mensaje de confirmación
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la pasarela de pagos"];
        }

        // Método público para insertar un nuevo registro utilizando los parámetros enviados desde el cliente
        public function insertar($params){
            // Limpia y escapa los caracteres especiales del parámetro para prevenir ataques de Inyección SQL
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            // Arma la sentencia SQL de inserción con el valor ya saneado
            $sql = "INSERT INTO pasarela_pagos(nombre) VALUES('$nombre')";
            
            // Ejecuta la consulta de inserción en la base de datos o muestra el error correspondiente
            mysqli_query($this->conexion, $sql) or die("Error al insertar pasarela de pagos: " . mysqli_error($this->conexion));
            
            // Retorna una respuesta estructurada con estado OK y mensaje de confirmación
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la pasarela de pagos"];
        }

        // Método público para actualizar o editar un registro existente según su ID y nuevos parámetros
        public function editar($id, $params){
            // Limpia los datos nuevos de entrada para mantener la seguridad ante inyecciones
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            // Convierte y asegura que el ID recibido sea un número entero válido
            $id = intval($id);

            // Construye la sentencia UPDATE para modificar el nombre correspondiente al ID indicado
            $sql = "UPDATE pasarela_pagos SET nombre = '$nombre' WHERE id_pasarela = $id";
            
            // Ejecuta la actualización en la base de datos o detiene el proceso si falla
            mysqli_query($this->conexion, $sql) or die("Error al editar pasarela de pagos: " . mysqli_error($this->conexion));
            
            // Retorna un arreglo con el resultado exitoso y su respectivo mensaje
            return ['Resultado' => "OK", 'mensaje' => "Se editó la pasarela de pagos"];
        }
    }
?>