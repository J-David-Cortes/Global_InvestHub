<?php
    class suscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM
            $sql = "SELECT * FROM suscripcion ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla suscripcion");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM suscripcion WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: eliminadas comillas en llaves foráneas (INT)
            $sql = "INSERT INTO suscripcion(nombre, fo_usuario, fo_estado_persona, fo_fecha_inicio, fo_fecha_fin) 
                    VALUES('$params->nombre', $params->usuario, $params->estado_persona, '$params->fecha_inicio', '$params->fecha_fin')";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: eliminadas comillas en llaves foráneas (INT)
            $sql = "UPDATE suscripcion SET nombre = '$params->nombre', fo_usuario = $params->usuario, fo_estado_persona = $params->estado_persona, fo_fecha_inicio = '$params->fecha_inicio', fo_fecha_fin = '$params->fecha_fin' WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>