<?php
    class estado_persona{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT * FROM estado_persona ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla estado_persona");

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM estado_persona WHERE id_estado_persona = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            $sql = "INSERT INTO estado_persona(nombre) VALUES('$params->nombre')";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: WHERE id_usuario -> id_estado_persona
            $sql = "UPDATE estado_persona SET nombre = '$params->nombre' WHERE id_estado_persona = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>