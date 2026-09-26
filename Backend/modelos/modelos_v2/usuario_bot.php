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

        // Limite de bots del plan activo del usuario (NULL = ilimitado,
        // 0 si no tiene ninguna suscripcion activa hoy). "Activa" es
        // fo_estado=1 (Activo) Y la fecha de hoy dentro de [fecha_inicio,
        // fecha_fin] -- un trial es una fila mas aqui (fo_suscripcion
        // apuntando al plan "Trial"), no un caso especial.
        // Mismo criterio que UsuarioSuscripcion::planActual(): mantener sincronizados.
        private function obtenerLimiteBotsUsuario($fo_usuario){
            $sql = "SELECT s.limite_bots
                    FROM usuario_suscripcion us
                    INNER JOIN suscripcion s ON us.fo_suscripcion = s.id_suscripcion
                    WHERE us.fo_usuario = $fo_usuario
                      AND us.fo_estado = 1
                      AND us.fecha_inicio <= CURDATE()
                      AND us.fecha_fin >= CURDATE()
                    ORDER BY us.fecha_inicio DESC
                    LIMIT 1";
            $res = mysqli_query($this->conexion, $sql) or die("Error al consultar suscripcion activa: " . mysqli_error($this->conexion));

            if(mysqli_num_rows($res) === 0){
                return 0;
            }

            $row = mysqli_fetch_assoc($res);
            return $row['limite_bots'] !== null ? (int) $row['limite_bots'] : null;
        }

        private function contarBotsNoVipActivos($fo_usuario){
            $sql = "SELECT COUNT(*) AS total
                    FROM usuario_bot ub
                    INNER JOIN bot_inversion bi ON ub.fo_bot = bi.id_bot
                    WHERE ub.fo_usuario = $fo_usuario AND ub.activo = 1 AND bi.es_vip = 0";
            $res = mysqli_query($this->conexion, $sql) or die("Error al contar bots activos: " . mysqli_error($this->conexion));
            $row = mysqli_fetch_assoc($res);
            return (int) $row['total'];
        }

        public function insertar($params){
            $fo_usuario = intval($params->fo_usuario);
            $fo_bot = intval($params->fo_bot);

            // Regla de negocio: un bot VIP siempre se permite activar, sin
            // importar el plan del usuario ni cuantos bots tenga activos.
            $sqlVip = "SELECT es_vip FROM bot_inversion WHERE id_bot = $fo_bot";
            $resVip = mysqli_query($this->conexion, $sqlVip) or die("Error al consultar bot_inversion: " . mysqli_error($this->conexion));
            $filaVip = mysqli_fetch_assoc($resVip);
            $esVip = $filaVip !== null ? (bool) $filaVip['es_vip'] : false;

            if(!$esVip){
                $limite = $this->obtenerLimiteBotsUsuario($fo_usuario);

                if($limite !== null){
                    $activos = $this->contarBotsNoVipActivos($fo_usuario);
                    if($activos >= $limite){
                        return ['Resultado' => "Error", 'mensaje' => "Has alcanzado el límite de bots de tu plan"];
                    }
                }
            }

            // Un insertar() siempre representa una activacion NUEVA:
            // fecha_activacion y activo quedan en su DEFAULT de la tabla
            // (CURRENT_TIMESTAMP y 1), no se confia en lo que mande el cliente.
            $api_key = mysqli_real_escape_string($this->conexion, $params->api_key);
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

        // Uso exclusivo de Settings: solo actualiza api_key de una conexion
        // ya existente, y solo si esa conexion pertenece a $fo_usuario.
        // No toca fo_bot/fo_broker/activo/fo_pasarela (eso lo maneja
        // Engines a traves de editar()).
        public function editarApiKey($id, $fo_usuario, $api_key){
            $id = intval($id);
            $fo_usuario = intval($fo_usuario);

            // Chequeo de propiedad ANTES de tocar la BD: evita que un id
            // de otro usuario reciba el UPDATE.
            $sqlOwner = "SELECT fo_usuario FROM usuario_bot WHERE id_conexion = $id";
            $resOwner = mysqli_query($this->conexion, $sqlOwner) or die("Error al verificar la conexion: " . mysqli_error($this->conexion));
            $filaOwner = mysqli_fetch_assoc($resOwner);

            if($filaOwner === null){
                return ['Resultado' => "Error", 'mensaje' => "La conexión no existe"];
            }
            if((int) $filaOwner['fo_usuario'] !== $fo_usuario){
                return ['Resultado' => "Error", 'mensaje' => "No tienes permiso para editar esta conexión"];
            }

            $api_key = mysqli_real_escape_string($this->conexion, $api_key);
            $sql = "UPDATE usuario_bot SET api_key = '$api_key' WHERE id_conexion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar api_key de usuario_bot: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se actualizó la api_key"];
        }
    }
?>