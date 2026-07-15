<?php
    class marketplace {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM
            $sql = "SELECT * FROM marketplace ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla marketplace");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM marketplace WHERE id_marketplace = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: eliminado comillas en fo_bot para tratar como INT
            $sql = "INSERT INTO marketplace(nombre, fo_bot) VALUES('$params->nombre', $params->bot)";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: eliminado comillas en fo_bot
            $sql = "UPDATE marketplace SET nombre = '$params->nombre', fo_bot = $params->bot WHERE id_marketplace = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>