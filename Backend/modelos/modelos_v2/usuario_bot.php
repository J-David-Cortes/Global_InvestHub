<?php
    class UsuarioBot {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_conexion, api_key, fo_usuario, fo_bot, fo_broker FROM usuario_bot ORDER BY id_conexion";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta usuario_bot: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM usuario_bot WHERE id_conexion = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la conexión del bot"];
        }

        public function insertar($params){
            $api_key = mysqli_real_escape_string($this->conexion, $params->api_key);
            $fo_usuario = intval($params->fo_usuario);
            $fo_bot = intval($params->fo_bot);
            $fo_broker = intval($params->fo_broker);
            
            $sql = "INSERT INTO usuario_bot(api_key, fo_usuario, fo_bot, fo_broker) VALUES('$api_key', $fo_usuario, $fo_bot, $fo_broker)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la conexión del bot"];
        }

        public function editar($id, $params){
            $api_key = mysqli_real_escape_string($this->conexion, $params->api_key);
            $fo_usuario = intval($params->fo_usuario);
            $fo_bot = intval($params->fo_bot);
            $fo_broker = intval($params->fo_broker);
            $id = intval($id);

            $sql = "UPDATE usuario_bot SET api_key = '$api_key', fo_usuario = $fo_usuario, fo_bot = $fo_bot, fo_broker = $fo_broker WHERE id_conexion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la conexión del bot"];
        }
    }
?>