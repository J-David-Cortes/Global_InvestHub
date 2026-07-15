<?php
    class fecha_inicio {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM y orden por fecha
            $sql = "SELECT * FROM fecha_inicio ORDER BY fecha";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla fecha_inicio");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM fecha_inicio WHERE id_fecha_inicio = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            $sql = "INSERT INTO fecha_inicio(fecha) VALUES('$params->fecha')";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            $sql = "UPDATE fecha_inicio SET fecha = '$params->fecha' WHERE id_fecha_inicio = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>