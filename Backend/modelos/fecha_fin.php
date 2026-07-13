<?php

    class fecha_fin{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT * FORM fecha_fin ORDER BY fecha_fin";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla fecha_fin");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM fecha_fin WHERE id_fecha_fin = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO fecha_fin(fecha) VALUES('$params->fecha')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE fecha_fin SET fecha = '$params->fecha' WHERE id_fecha_fin = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>