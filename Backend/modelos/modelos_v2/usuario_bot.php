<?php
    class UsuarioBot {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        ub.id_conexion AS id,
                        ub.fo_bot AS botId,
                        bi.nombre AS botNombre,
                        bi.plataforma AS botPlataforma,
                        bi.categoria AS botCategoria,
                        ub.fo_broker AS brokerId,
                        br.nombre AS brokerNombre,
                        ub.activo AS activo,
                        ub.fecha_activacion AS fechaActivacion,
                        ub.fecha_desactivacion AS fechaDesactivacion,
                        ub.fo_pasarela AS pasarelaId
                    FROM usuario_bot ub
                    INNER JOIN bot_inversion bi ON ub.fo_bot = bi.id_bot
                    INNER JOIN broker br ON ub.fo_broker = br.id_broker
                    WHERE ub.fo_usuario = $fo_usuario AND ub.activo = 1
                    ORDER BY ub.fecha_activacion DESC";

            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta usuario_bot: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $row['id'] = (int) $row['id'];
                $row['botId'] = (int) $row['botId'];
                $row['brokerId'] = (int) $row['brokerId'];
                $row['activo'] = (bool) $row['activo'];
                $row['pasarelaId'] = $row['pasarelaId'] !== null ? (int) $row['pasarelaId'] : null;
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
            // Un insertar() siempre representa una activacion NUEVA:
            // fecha_activacion y activo quedan en su DEFAULT de la tabla
            // (CURRENT_TIMESTAMP y 1), no se confia en lo que mande el cliente.
            $api_key = mysqli_real_escape_string($this->conexion, $params->api_key);
            $fo_usuario = intval($params->fo_usuario);
            $fo_bot = intval($params->fo_bot);
            $fo_broker = intval($params->fo_broker);
            $fo_pasarela = isset($params->fo_pasarela) && $params->fo_pasarela !== null ? intval($params->fo_pasarela) : null;
            $fo_pasarela_sql = $fo_pasarela !== null ? $fo_pasarela : 'NULL';

            $sql = "INSERT INTO usuario_bot(api_key, fo_usuario, fo_bot, fo_broker, fo_pasarela) VALUES('$api_key', $fo_usuario, $fo_bot, $fo_broker, $fo_pasarela_sql)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la conexión del bot"];
        }

        public function editar($id, $params){
            // fecha_activacion NO se toca aqui: es la fecha original de la
            // fila, nunca deberia cambiar despues de creada.
            $api_key = mysqli_real_escape_string($this->conexion, $params->api_key);
            $fo_usuario = intval($params->fo_usuario);
            $fo_bot = intval($params->fo_bot);
            $fo_broker = intval($params->fo_broker);
            $activo = intval($params->activo);
            $fo_pasarela = isset($params->fo_pasarela) && $params->fo_pasarela !== null ? intval($params->fo_pasarela) : null;
            $fo_pasarela_sql = $fo_pasarela !== null ? $fo_pasarela : 'NULL';
            // fecha_desactivacion la calcula el servidor segun "activo",
            // no se confia en una fecha que mande el cliente.
            $fecha_desactivacion_sql = $activo === 0 ? 'NOW()' : 'NULL';
            $id = intval($id);

            $sql = "UPDATE usuario_bot SET api_key = '$api_key', fo_usuario = $fo_usuario, fo_bot = $fo_bot, fo_broker = $fo_broker,
                           activo = $activo, fecha_desactivacion = $fecha_desactivacion_sql, fo_pasarela = $fo_pasarela_sql
                    WHERE id_conexion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la conexión del bot"];
        }
    }
?>