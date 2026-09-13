<?php
    class BotInversion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_bot AS id, nombre, estrategia, es_vip AS esVip, precio_individual AS precioIndividual,
                           subtitulo, descripcion, categoria, plataforma, verificado,
                           rendimiento_anual AS rendimientoAnual, sharpe_ratio AS sharpeRatio, max_drawdown AS maxDrawdown,
                           win_rate AS winRate, desarrollador, rating, numero_reviews AS numeroReviews,
                           tags, historial_rendimiento AS historialRendimiento
                    FROM bot_inversion ORDER BY nombre";

            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta bot_inversion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $row['tags'] = $row['tags'] !== null ? json_decode($row['tags']) : null;
                $row['historialRendimiento'] = $row['historialRendimiento'] !== null ? json_decode($row['historialRendimiento']) : null;
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
            $codigo_python = mysqli_real_escape_string($this->conexion, $params->codigo_python);

            $sql = "INSERT INTO bot_inversion(nombre, estrategia, codigo_python) VALUES('$nombre', '$estrategia', '$codigo_python')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar bot de inversión: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el bot de inversión"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $estrategia = mysqli_real_escape_string($this->conexion, $params->estrategia);
            $codigo_python = mysqli_real_escape_string($this->conexion, $params->codigo_python);
            $id = intval($id);

            $sql = "UPDATE bot_inversion SET nombre = '$nombre', estrategia = '$estrategia', codigo_python = '$codigo_python' WHERE id_bot = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar bot de inversión: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el bot de inversión"];
        }
    }
?>