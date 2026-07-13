<?php
    class pais{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT * FORM pais ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla PAIS");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function consulta2($id_pais){
            $sql = "SELECT * FORM pais WHERE fo_ciudad = $id_ciudad ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla PAIS");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM pais WHERE id_pais = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO pais(nombre) VALUES('$params->nombre', '$params->ciudad')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE pais SET nombre = '$params->nombre', fo_ciudad = '$params->ciudad' WHERE id_pais = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>