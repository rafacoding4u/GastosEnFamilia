<?php
class Config {
    // Datos de conexión a la base de datos
    private static $db_host = 'localhost';  // El servidor de la base de datos
    private static $db_user = 'root';       // Usuario por defecto en XAMPP
    private static $db_pass = '';           // Contraseña vacía por defecto en XAMPP
    private static $db_name = 'gastosencasa_bd';  // Nombre de la base de datos
    private static $db_charset = 'utf8';    // Conjunto de caracteres

    // Método para establecer la conexión a la base de datos
    public static function getConexion() {
        try {
            // Creamos una nueva instancia de PDO con los datos de conexión
            $conexion = new PDO(
                'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_charset,
                self::$db_user,
                self::$db_pass,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Modo de error: lanzar excepciones
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC  // Modo de obtención de resultados: arrays asociativos
                )
            );
            return $conexion;
        } catch (PDOException $e) {
            // En caso de error, registramos el mensaje en el archivo de log
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>
