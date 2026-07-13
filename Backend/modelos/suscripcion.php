<?php

    class suscripcion{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT * FORM suscripcion ORDER BY nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla SUSCRIPCION");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM suscripcion WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO suscripcion(nombre, fo_usuario, fo_estado_persona, fo_fecha_inicio, fo_fecha_fin) VALUES('$params->nombre', '$params->usuario', '$params->estado_persona', '$params->fecha_inicio', '$params->fecha_fin')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE suscripcion SET nombre = '$params->nombre', fo_usuario = '$params->usuario', fo_estado_persona = '$params->estado_persona', fo_fecha_inicio = '$params->fecha_inicio', fo_fecha_fin = '$params->fecha_fin' WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>