<?php
    class educacion{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM y orden por id_educacion
            $sql = "SELECT * FROM educacion ORDER BY id_educacion";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla educacion");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM educacion WHERE id_educacion = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: eliminadas comillas en llaves foráneas para tratar como INT
            $sql = "INSERT INTO educacion(fo_usuario, fo_titulo) VALUES($params->usuario, $params->titulo)";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: WHERE id_usuario -> id_educacion y eliminado comillas en llaves foráneas
            $sql = "UPDATE educacion SET fo_usuario = $params->usuario, fo_titulo = $params->titulo WHERE id_educacion = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>