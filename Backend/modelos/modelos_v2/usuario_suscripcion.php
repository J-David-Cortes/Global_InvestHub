<?php
    class UsuarioSuscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        // TODO PRIVACIDAD: consulta() devuelve las suscripciones de TODOS los
        // usuarios, sin ningun filtro. Antes de cualquier uso real debe
        // restringirse (filtrar por el usuario autenticado o reservarlo a
        // administradores). Para la pestaña Billing usar planActual().
        public function consulta(){
            $sql = "SELECT id_registro, fecha_inicio, fecha_fin, fo_usuario, fo_suscripcion, fo_pasarela, fo_estado FROM usuario_suscripcion ORDER BY id_registro";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta usuario_suscripcion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        // Plan activo del usuario, con el mismo criterio de "activa" que
        // UsuarioBot::obtenerLimiteBotsUsuario(): fo_estado = 1 (Activo) Y la
        // fecha de hoy dentro de [fecha_inicio, fecha_fin]. Mantener ambos
        // sitios sincronizados.
        // Sin suscripcion activa se devuelve el plan sin bots (limite_bots = 0,
        // hoy Starter) con suscripcionActiva = false, igual que la validacion de
        // limite de bots. Si el catalogo no tuviera ningun plan con limite 0,
        // devuelve null.
        public function planActual($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        s.id_suscripcion AS id,
                        s.nombre,
                        s.precio,
                        s.limite_bots AS limiteBots,
                        us.fecha_inicio AS fechaInicio,
                        us.fecha_fin AS fechaFin
                    FROM usuario_suscripcion us
                    INNER JOIN suscripcion s ON us.fo_suscripcion = s.id_suscripcion
                    WHERE us.fo_usuario = $fo_usuario
                      AND us.fo_estado = 1
                      AND us.fecha_inicio <= CURDATE()
                      AND us.fecha_fin >= CURDATE()
                    ORDER BY us.fecha_inicio DESC
                    LIMIT 1";

            $res = mysqli_query($this->conexion, $sql) or die("Error en planActual: " . mysqli_error($this->conexion));
            $row = mysqli_fetch_assoc($res);
            $suscripcionActiva = true;

            if(!$row){
                $sqlSinBots = "SELECT
                                   id_suscripcion AS id,
                                   nombre,
                                   precio,
                                   limite_bots AS limiteBots,
                                   NULL AS fechaInicio,
                                   NULL AS fechaFin
                               FROM suscripcion
                               WHERE limite_bots = 0
                               ORDER BY precio, id_suscripcion
                               LIMIT 1";
                $resSinBots = mysqli_query($this->conexion, $sqlSinBots) or die("Error en planActual (plan sin bots): " . mysqli_error($this->conexion));
                $row = mysqli_fetch_assoc($resSinBots);
                $suscripcionActiva = false;

                if(!$row){
                    return null;
                }
            }

            return [
                'id' => (int) $row['id'],
                'nombre' => $row['nombre'],
                'precio' => (float) $row['precio'],
                // NULL = ilimitado (Enterprise), 0 = sin bots: no convertir NULL a 0.
                'limiteBots' => $row['limiteBots'] !== null ? (int) $row['limiteBots'] : null,
                'fechaInicio' => $row['fechaInicio'],
                'fechaFin' => $row['fechaFin'],
                'suscripcionActiva' => $suscripcionActiva,
            ];
        }

        public function eliminar($id){
            $sql = "DELETE FROM usuario_suscripcion WHERE id_registro = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la suscripción del usuario"];
        }

        // TODO SEGURIDAD: insertar() y editar() aceptan plan, estado y fechas
        // directamente del cliente, asi que cualquiera podria concederse el plan
        // Enterprise sin pagar. Es aceptable en esta demo academica (todavia no
        // existe login ni pasarela de pagos real), pero ANTES de cualquier uso
        // real el plan y el estado deben fijarse en el servidor a partir de un
        // pago confirmado, nunca venir del cliente.
        public function insertar($params){
            $fecha_inicio = mysqli_real_escape_string($this->conexion, $params->fecha_inicio);
            $fecha_fin = mysqli_real_escape_string($this->conexion, $params->fecha_fin);
            $fo_usuario = intval($params->fo_usuario);
            $fo_suscripcion = intval($params->fo_suscripcion);
            $fo_pasarela = intval($params->fo_pasarela);
            $fo_estado = intval($params->fo_estado);
            
            $sql = "INSERT INTO usuario_suscripcion(fecha_inicio, fecha_fin, fo_usuario, fo_suscripcion, fo_pasarela, fo_estado) VALUES('$fecha_inicio', '$fecha_fin', $fo_usuario, $fo_suscripcion, $fo_pasarela, $fo_estado)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la suscripción del usuario"];
        }

        // TODO SEGURIDAD: mismo problema que insertar() (ver comentario arriba).
        public function editar($id, $params){
            $fecha_inicio = mysqli_real_escape_string($this->conexion, $params->fecha_inicio);
            $fecha_fin = mysqli_real_escape_string($this->conexion, $params->fecha_fin);
            $fo_usuario = intval($params->fo_usuario);
            $fo_suscripcion = intval($params->fo_suscripcion);
            $fo_pasarela = intval($params->fo_pasarela);
            $fo_estado = intval($params->fo_estado);
            $id = intval($id);

            $sql = "UPDATE usuario_suscripcion SET fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin', fo_usuario = $fo_usuario, fo_suscripcion = $fo_suscripcion, fo_pasarela = $fo_pasarela, fo_estado = $fo_estado WHERE id_registro = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la suscripción del usuario"];
        }
    }
?>