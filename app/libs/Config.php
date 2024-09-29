<?php
class Config {
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = '';
    private static $db_name = 'gastosencasa_bd';
    private static $db_charset = 'utf8';

    private static $debug = true; // Cambia a 'false' en producción

    public static function getConexion() {
        try {
            $conexion = new PDO(
                'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_charset,
                self::$db_user,
                self::$db_pass,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );
            return $conexion;
        } catch (PDOException $e) {
            self::manejarError($e);
        }
    }

    public static function manejarError($e) {
        if (self::$debug) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        } else {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            die("Ocurrió un problema. Inténtalo más tarde.");
        }
    }

    public static function isDebug() {
        return self::$debug;
    }
}

