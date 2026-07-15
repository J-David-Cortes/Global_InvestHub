<?php
    class bot{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT b.*, u.nombre AS Usuario, a.codigo AS Codigo, br.nombre AS Broker
                FROM bot b
                INNER JOIN usuario u ON b.fo_usuario = u.id_usuario
                INNER JOIN api a ON b.fo_api = a.id_api
                INNER JOIN broker br ON a.fo_broker = br.id_broker
                ORDER BY b.nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No pudo consultar la tabla bot");

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM bot WHERE id_bot = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            $sql = "INSERT INTO bot(nombre, codigo, fo_usuario, fo_api) 
                    VALUES('$params->nombre', '$params->codigo', $params->fo_usuario, $params->fo_api)";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: WHERE id_bot = $id
            $sql = "UPDATE bot SET nombre = '$params->nombre', codigo = '$params->codigo', 
                    fo_usuario = $params->fo_usuario, fo_api = $params->fo_api 
                    WHERE id_bot = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>