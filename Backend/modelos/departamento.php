<?php
    class departamento{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT * FROM departamento ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla departamento");

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM departamento WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            $sql = "INSERT INTO departamento(nombre) VALUES('$params->nombre')";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            $sql = "UPDATE departamento SET nombre = '$params->nombre' WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>