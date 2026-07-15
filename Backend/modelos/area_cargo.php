<?php
    class area_cargo{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM y ORDEN BY por id_area_cargo
            $sql = "SELECT * FROM area_cargo ORDER BY id_area_cargo";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla area_cargo");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM area_cargo WHERE id_area_cargo = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            $sql = "INSERT INTO area_cargo(nombre) VALUES('$params->nombre')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: WHERE id_usuario -> id_area_cargo
            $sql = "UPDATE area_cargo SET nombre = '$params->nombre' WHERE id_area_cargo = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>