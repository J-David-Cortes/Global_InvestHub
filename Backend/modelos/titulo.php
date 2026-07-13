<?php

    class titulo{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT e.*, u.nombre AS Usuario, t.id_titulo AS Titulo
                FROM educacion e
                INNER JOIN usuario u ON e.fo_usuario = u.id_usuario
                INNER JOIN titulo t ON e.fo_titulo = t.id_titulo;";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla TITULO");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM titulo WHERE id_titulo = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO titulo(fo_nivel_educacion, fo_leccion_educacion, fo_avance_educacion) VALUES('$params->fo_nivel_educacion', '$params->fo_leccion_educacion', '$params->fo_avance_educacion')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE titulo SET fo_nivel_educacion = '$params->fo_nivel_educacion', fo_leccion_educacion = '$params->fo_leccion_educacion', fo_avance_educacion = '$params->fo_avance_educacion' WHERE id_titulo = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>