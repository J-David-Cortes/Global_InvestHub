<?php
    class ciudad{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT * FROM ciudad ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla ciudad");

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function consulta2($id_departamento){
            $sql = "SELECT * FROM ciudad WHERE fo_departamento = $id_departamento ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla ciudad");

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM ciudad WHERE id_ciudad = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: apuntas a la tabla ciudad, no bot
            $sql = "INSERT INTO ciudad(nombre, fo_departamento) VALUES('$params->nombre', $params->departamento)";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            $sql = "UPDATE ciudad SET nombre = '$params->nombre', fo_departamento = $params->departamento WHERE id_ciudad = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>