<?php
    class departamento{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT * FORM departamento ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla Departamento");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function consulta2($id_departamento){
            $sql = "SELECT * FORM ciudad WHERE fo_departamento = $id_departamento ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla CIUDAD");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM departamento WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO departamento(nombre) VALUES('$params->nombre')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE departamento SET nombre = '$params->nombre' WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>