<?php
    class PreferenciaNotificacion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        // Valores para un usuario que todavia no tiene fila. Deben coincidir
        // con los DEFAULT de las columnas en
        // Backend/migraciones/2026-09-20_tabla_preferencia_notificacion.sql
        private function defaults(){
            return [
                'emailTrades' => true,
                'emailAlerts' => true,
                'emailReports' => false,
                'pushTrades' => true,
                'pushAlerts' => true,
                'pushNews' => false,
            ];
        }

        public function obtenerPreferencias($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        email_trades AS emailTrades,
                        email_alerts AS emailAlerts,
                        email_reports AS emailReports,
                        push_trades AS pushTrades,
                        push_alerts AS pushAlerts,
                        push_news AS pushNews
                    FROM preferencia_notificacion
                    WHERE fo_usuario = $fo_usuario";

            $res = mysqli_query($this->conexion, $sql) or die("Error en obtenerPreferencias: " . mysqli_error($this->conexion));
            $row = mysqli_fetch_assoc($res);

            if(!$row){
                return $this->defaults();
            }

            return [
                'emailTrades' => (bool) $row['emailTrades'],
                'emailAlerts' => (bool) $row['emailAlerts'],
                'emailReports' => (bool) $row['emailReports'],
                'pushTrades' => (bool) $row['pushTrades'],
                'pushAlerts' => (bool) $row['pushAlerts'],
                'pushNews' => (bool) $row['pushNews'],
            ];
        }

        public function guardarPreferencias($fo_usuario, $params){
            $fo_usuario = intval($fo_usuario);

            // Se exigen las 6 preferencias, cada una como booleano real
            // (true/false en el JSON). Asi el INSERT inicial nunca tiene que
            // decidir que hacer con una preferencia que falta.
            $valores = [];
            foreach(array_keys($this->defaults()) as $clave){
                if(!isset($params->$clave) || !is_bool($params->$clave)){
                    return ['Resultado' => "Error", 'mensaje' => "Datos inválidos"];
                }
                $valores[$clave] = $params->$clave ? 1 : 0;
            }

            // Crear o actualizar en una sola sentencia: la PK es fo_usuario,
            // asi que si ya existe fila, el INSERT choca y se ejecuta el UPDATE.
            $sql = "INSERT INTO preferencia_notificacion
                        (fo_usuario, email_trades, email_alerts, email_reports, push_trades, push_alerts, push_news)
                    VALUES
                        ($fo_usuario, {$valores['emailTrades']}, {$valores['emailAlerts']}, {$valores['emailReports']},
                         {$valores['pushTrades']}, {$valores['pushAlerts']}, {$valores['pushNews']})
                    ON DUPLICATE KEY UPDATE
                        email_trades = VALUES(email_trades),
                        email_alerts = VALUES(email_alerts),
                        email_reports = VALUES(email_reports),
                        push_trades = VALUES(push_trades),
                        push_alerts = VALUES(push_alerts),
                        push_news = VALUES(push_news)";

            mysqli_query($this->conexion, $sql) or die("Error al guardar preferencias: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se guardaron las preferencias"];
        }
    }
?>
