<?php
    class nivel_permiso {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM
            $sql = "SELECT * FROM nivel_permiso ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla nivel_permiso");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM nivel_permiso WHERE id_nivel_permiso = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Ajustado para manejar 'nivel' como número sin comillas
            $sql = "INSERT INTO nivel_permiso(nombre, nivel) VALUES('$params->nombre', $params->nivel)";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            $sql = "UPDATE nivel_permiso SET nombre = '$params->nombre', nivel = $params->nivel WHERE id_nivel_permiso = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>