<?php

class Config {
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = 'Ladilla7890@2'; // Ajusta esta contraseña si tu usuario root la tiene configurada
    private static $db_name = 'gastosencasa_bd';
    private static $db_charset = 'utf8';

    private static $debug = true; // Cambia a 'false' en producción

    // Path para el archivo de log
    private static $log_file = __DIR__ . '/../log/logExcepcio.txt';

    /**
     * Obtiene la conexión a la base de datos.
     * @return PDO|null Retorna una conexión PDO o null en caso de error.
     */
    public static function getConexion() {
        try {
            $dsn = 'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_charset;
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false // Recomendado para prevenir inyecciones SQL
            ];

            $conexion = new PDO($dsn, self::$db_user, self::$db_pass, $opciones);
            return $conexion;
        } catch (PDOException $e) {
            self::manejarError($e);
            return null; // Retorna null en caso de error
        }
    }

    /**
     * Maneja errores de conexión y otros errores en base al estado de depuración.
     * @param PDOException $e La excepción capturada.
     */
    public static function manejarError($e) {
        $mensaje_error = "Error en la conexión a la base de datos: " . $e->getMessage();
        if (self::$debug) {
            // Si está en modo debug, muestra el error en pantalla
            die($mensaje_error);
        } else {
            // Si está en producción, registra el error en el archivo de log y muestra un mensaje genérico
            self::registrarError($mensaje_error);
            die("Ocurrió un problema. Inténtalo más tarde.");
        }
    }

    /**
     * Registra un error en un archivo de log con detalles adicionales.
     * @param string $mensaje El mensaje de error a registrar.
     */
    public static function registrarError($mensaje) {
        $fecha = date('Y-m-d H:i:s');
        $log_message = "[{$fecha}] {$mensaje}" . PHP_EOL;

        // Escribir el error en el archivo de log
        error_log($log_message, 3, self::$log_file);
    }

    /**
     * Verifica si el sistema está en modo depuración.
     * @return bool Retorna true si está en modo debug, false en caso contrario.
     */
    public static function isDebug() {
        return self::$debug;
    }

    /**
     * Habilita o deshabilita el modo de depuración.
     * @param bool $debug_mode Si es true, habilita el modo debug; si es false, lo deshabilita.
     */
    public static function setDebugMode($debug_mode) {
        self::$debug = $debug_mode;
    }

    // Métodos para obtener los valores privados
    public static function getDbUser() {
        return self::$db_user;
    }

    public static function getDbPass() {
        return self::$db_pass;
    }

    public static function getDbHost() {
        return self::$db_host;
    }

    public static function getDbName() {
        return self::$db_name;
    }

    public static function getDbCharset() {
        return self::$db_charset;
    }
}
