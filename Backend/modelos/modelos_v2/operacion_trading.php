<?php
    class OperacionTrading {
        private $conexion;

        // Tasa libre de riesgo asumida = 0, simplificacion academica
        // (ver discusion: con datos sinteticos/vacios hoy, un valor
        // distinto de 0 seria precision falsa). Cambiar solo aqui si
        // en el futuro se decide usar otro valor.
        const TASA_LIBRE_RIESGO = 0.0;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function resumenPortfolio($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        COALESCE(SUM(monto_invertido), 0) AS totalEquity,
                        COALESCE(SUM(CASE WHEN fecha_cierre IS NOT NULL THEN resultado_pnl ELSE 0 END), 0) AS totalPnl,
                        COALESCE(SUM(CASE WHEN DATE(fecha_cierre) = CURDATE() THEN resultado_pnl ELSE 0 END), 0) AS pnlHoy,
                        COALESCE(SUM(CASE WHEN fecha_cierre >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN resultado_pnl ELSE 0 END), 0) AS pnlMes,
                        COALESCE(SUM(CASE WHEN YEAR(fecha_cierre) = YEAR(CURDATE()) THEN resultado_pnl ELSE 0 END), 0) AS pnlAnio
                    FROM operacion_trading
                    WHERE fo_usuario = $fo_usuario";

            $res = mysqli_query($this->conexion, $sql) or die("Error en resumenPortfolio: " . mysqli_error($this->conexion));
            $row = mysqli_fetch_assoc($res);

            return [
                'totalEquity' => (float) $row['totalEquity'],
                'totalPnl' => (float) $row['totalPnl'],
                'pnlHoy' => (float) $row['pnlHoy'],
                'pnlMes' => (float) $row['pnlMes'],
                'pnlAnio' => (float) $row['pnlAnio'],
            ];
        }

        // Sharpe = (retorno promedio - tasa libre de riesgo) / desviacion
        // estandar de los retornos. El retorno de cada operacion se
        // normaliza como resultado_pnl / monto_invertido (no el pnl crudo,
        // para no mezclar operaciones de montos distintos).
        // Con 0 o 1 operacion cerrada, STDDEV_SAMP (muestral, n-1) da NULL
        // en MariaDB -- no truena, se detecta antes de dividir y se
        // devuelve sharpeRatio=null (no 0) para no simular una metrica
        // real donde solo falta informacion.
        public function sharpeRatio($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        COUNT(*) AS operacionesCerradas,
                        AVG(resultado_pnl / monto_invertido) AS retornoPromedio,
                        STDDEV_SAMP(resultado_pnl / monto_invertido) AS desviacionRetornos
                    FROM operacion_trading
                    WHERE fo_usuario = $fo_usuario
                      AND fecha_cierre IS NOT NULL
                      AND resultado_pnl IS NOT NULL
                      AND monto_invertido <> 0";

            $res = mysqli_query($this->conexion, $sql) or die("Error en sharpeRatio: " . mysqli_error($this->conexion));
            $row = mysqli_fetch_assoc($res);

            $operacionesCerradas = (int) $row['operacionesCerradas'];
            $desviacion = $row['desviacionRetornos'] !== null ? (float) $row['desviacionRetornos'] : null;

            if($operacionesCerradas < 2 || $desviacion === null || $desviacion == 0.0){
                return [
                    'sharpeRatio' => null,
                    'operacionesCerradas' => $operacionesCerradas,
                ];
            }

            $retornoPromedio = (float) $row['retornoPromedio'];
            $sharpe = ($retornoPromedio - self::TASA_LIBRE_RIESGO) / $desviacion;

            return [
                'sharpeRatio' => round($sharpe, 4),
                'operacionesCerradas' => $operacionesCerradas,
            ];
        }

        public function posicionesAbiertas($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        ot.id_operacion AS id,
                        ot.fo_bot AS botId,
                        bi.nombre AS botNombre,
                        ot.precio_entrada AS precioEntrada,
                        ot.precio_actual AS precioActual,
                        ot.monto_invertido AS montoInvertido,
                        ot.fecha_apertura AS fechaApertura
                    FROM operacion_trading ot
                    INNER JOIN bot_inversion bi ON ot.fo_bot = bi.id_bot
                    WHERE ot.fo_usuario = $fo_usuario AND ot.fecha_cierre IS NULL
                    ORDER BY ot.fecha_apertura DESC";

            $res = mysqli_query($this->conexion, $sql) or die("Error en posicionesAbiertas: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $row['id'] = (int) $row['id'];
                $row['botId'] = (int) $row['botId'];
                $row['precioEntrada'] = (float) $row['precioEntrada'];
                $row['precioActual'] = $row['precioActual'] !== null ? (float) $row['precioActual'] : null;
                $row['montoInvertido'] = (float) $row['montoInvertido'];
                $vec[] = $row;
            }
            return $vec;
        }

        public function posicionesCerradas($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT
                        ot.id_operacion AS id,
                        ot.fo_bot AS botId,
                        bi.nombre AS botNombre,
                        ot.precio_entrada AS precioEntrada,
                        ot.precio_cierre AS precioCierre,
                        ot.monto_invertido AS montoInvertido,
                        ot.resultado_pnl AS resultadoPnl,
                        (ot.monto_invertido + ot.resultado_pnl) AS montoRetornado,
                        ot.fecha_apertura AS fechaApertura,
                        ot.fecha_cierre AS fechaCierre
                    FROM operacion_trading ot
                    INNER JOIN bot_inversion bi ON ot.fo_bot = bi.id_bot
                    WHERE ot.fo_usuario = $fo_usuario AND ot.fecha_cierre IS NOT NULL
                    ORDER BY ot.fecha_cierre DESC";

            $res = mysqli_query($this->conexion, $sql) or die("Error en posicionesCerradas: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $row['id'] = (int) $row['id'];
                $row['botId'] = (int) $row['botId'];
                $row['precioEntrada'] = (float) $row['precioEntrada'];
                $row['precioCierre'] = (float) $row['precioCierre'];
                $row['montoInvertido'] = (float) $row['montoInvertido'];
                $row['resultadoPnl'] = (float) $row['resultadoPnl'];
                $row['montoRetornado'] = (float) $row['montoRetornado'];
                $vec[] = $row;
            }
            return $vec;
        }
    }
?>
