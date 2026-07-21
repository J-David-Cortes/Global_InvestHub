<?php
    class BotInversion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_bot, nombre, estrategia, algoritmo FROM bot_inversion ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta bot_inversion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM bot_inversion WHERE id_bot = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar bot de inversión: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el bot de inversión"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $estrategia = mysqli_real_escape_string($this->conexion, $params->estrategia);
            $algoritmo = mysqli_real_escape_string($this->conexion, $params->algoritmo);
            
            $sql = "INSERT INTO bot_inversion(nombre, estrategia, algoritmo) VALUES('$nombre', '$estrategia', '$algoritmo')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar bot de inversión: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el bot de inversión"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $estrategia = mysqli_real_escape_string($this->conexion, $params->estrategia);
            $algoritmo = mysqli_real_escape_string($this->conexion, $params->algoritmo);
            $id = intval($id);

            $sql = "UPDATE bot_inversion SET nombre = '$nombre', estrategia = '$estrategia', algoritmo = '$algoritmo' WHERE id_bot = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar bot de inversión: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el bot de inversión"];
        }
    }
?>