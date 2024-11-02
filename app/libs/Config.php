<?php

class Config
{
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = 'Ladilla7890@2'; // Ajusta esta contraseña si tu usuario root la tiene configurada
    private static $db_name = 'gastosencasa_bd';
    private static $db_charset = 'utf8';

    private static $debug = true; // Cambia a 'false' en producción

    // Path para el archivo de log
    private static $log_file = __DIR__ . '/../log/logExcepcio.txt'; // Unificamos en un solo archivo

    /**
     * Obtiene la conexión a la base de datos.
     * @return PDO|null Retorna una conexión PDO o null en caso de error.
     */
    public static function getConexion()
    {
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
     * Verifica si el usuario tiene permiso de superadmin para ciertas rutas exclusivas.
     * @param string $ruta La ruta actual que se está accediendo.
     * @return bool Retorna true si es superadmin y la ruta está permitida, false en caso contrario.
     */
    public static function verificarPermisos($ruta)
    {
        $nivelUsuario = $_SESSION['usuario']['nivel_usuario'] ?? 'registro';
        $idUser = $_SESSION['usuario']['id'] ?? null;

        // Definir permisos por rol
        $permisos = [
            'superadmin' => [
                'usuarios' => ['leer', 'escribir', 'eliminar'],
                'familias' => ['leer', 'escribir', 'eliminar'],
                'grupos' => ['leer', 'escribir', 'eliminar'],
                'gastos' => ['leer', 'escribir', 'eliminar'],
                'ingresos' => ['leer', 'escribir', 'eliminar'],
                'metas' => ['leer', 'escribir', 'eliminar'],
                'presupuestos' => ['leer', 'escribir', 'eliminar']
            ],
            'admin' => [
                'usuarios' => ['leer', 'escribir'], // Sin permiso de eliminar otros admin
                'familias' => ['leer', 'escribir'],
                'grupos' => ['leer', 'escribir'],
                'gastos' => ['leer', 'escribir', 'eliminar'],
                'ingresos' => ['leer', 'escribir', 'eliminar'],
                'metas' => ['leer', 'escribir', 'eliminar'],
                'presupuestos' => ['leer', 'escribir', 'eliminar']
            ],
            'usuario' => [
                'usuarios' => ['leer'],
                'gastos' => ['leer', 'escribir', 'eliminar'],
                'ingresos' => ['leer', 'escribir', 'eliminar'],
                'metas' => ['leer', 'escribir', 'eliminar'],
                'presupuestos' => ['leer', 'escribir', 'eliminar']
            ],
            'registro' => [
                'familias' => ['leer', 'escribir'],
                'grupos' => ['leer', 'escribir']
            ]
        ];

        // Rutas mapeadas a categorías
        $rutasCategorias = [
            'listarUsuarios' => 'usuarios',
            'crearUsuario' => 'usuarios',
            'eliminarUsuario' => 'usuarios',
            'actualizarUsuario' => 'usuarios',
            'verFamilias' => 'familias',
            'crearFamilia' => 'familias',
            'eliminarFamilia' => 'familias',
            'verGrupos' => 'grupos',
            'crearGrupo' => 'grupos',
            'eliminarGrupo' => 'grupos',
            'verGastos' => 'gastos',
            'crearGasto' => 'gastos',
            'eliminarGasto' => 'gastos',
            'verIngresos' => 'ingresos',
            'crearIngreso' => 'ingresos',
            'eliminarIngreso' => 'ingresos',
            'verMetas' => 'metas',
            'crearMeta' => 'metas',
            'eliminarMeta' => 'metas',
            'verPresupuestos' => 'presupuestos',
            'crearPresupuesto' => 'presupuestos',
            'eliminarPresupuesto' => 'presupuestos'
        ];

        // Verificar si la ruta corresponde a alguna categoría
        if (!isset($rutasCategorias[$ruta])) {
            return false; // Ruta no permitida
        }

        $categoria = $rutasCategorias[$ruta];

        // Comprobar permisos específicos para el rol actual
        if (!isset($permisos[$nivelUsuario][$categoria])) {
            return false; // Sin permisos para esta categoría
        }

        $accionesPermitidas = $permisos[$nivelUsuario][$categoria];

        // Definir las acciones permitidas en función de la ruta
        if (strpos($ruta, 'crear') !== false || strpos($ruta, 'actualizar') !== false) {
            $accion = 'escribir';
        } elseif (strpos($ruta, 'eliminar') !== false) {
            $accion = 'eliminar';
            // Restricción adicional: No permitir que superadmin o admin se eliminen a sí mismos
            if ($ruta === 'eliminarUsuario' && isset($_GET['id']) && $_GET['id'] == $idUser) {
                return false;
            }
        } else {
            $accion = 'leer';
        }

        return in_array($accion, $accionesPermitidas);
    }

    /**
     * Maneja errores de conexión y otros errores en base al estado de depuración.
     * @param Exception $e La excepción capturada.
     */
    public static function manejarError($e)
    {
        $mensaje_error = "Error: " . $e->getMessage();
        if (self::$debug) {
            // Mostrar el error en pantalla si está en modo debug
            echo "<h2>{$mensaje_error}</h2>";
        }
        // Registrar el error en el archivo de log siempre
        self::registrarError($mensaje_error);
        error_log($mensaje_error, 3, self::$log_file);  // Asegúrate de siempre registrar en el archivo
    }

    /**
     * Registra un error en un archivo de log con detalles adicionales.
     * @param string $mensaje El mensaje de error a registrar.
     */
    public static function registrarError($mensaje)
    {
        $fecha = date('Y-m-d H:i:s');
        $log_message = "[{$fecha}] {$mensaje}" . PHP_EOL;

        // Escribir el error en el archivo de log
        if (!file_exists(self::$log_file)) {
            // Crear el archivo si no existe
            file_put_contents(self::$log_file, '', LOCK_EX);
        }

        error_log($log_message, 3, self::$log_file);
    }

    /**
     * Verifica si el sistema está en modo depuración.
     * @return bool Retorna true si está en modo debug, false en caso contrario.
     */
    public static function isDebug()
    {
        return self::$debug;
    }

    /**
     * Habilita o deshabilita el modo de depuración.
     * @param bool $debug_mode Si es true, habilita el modo debug; si es false, lo deshabilita.
     */
    public static function setDebugMode($debug_mode)
    {
        self::$debug = $debug_mode;
    }

    // Métodos para obtener los valores privados
    public static function getDbUser()
    {
        return self::$db_user;
    }

    public static function getDbPass()
    {
        return self::$db_pass;
    }

    public static function getDbHost()
    {
        return self::$db_host;
    }

    public static function getDbName()
    {
        return self::$db_name;
    }

    public static function getDbCharset()
    {
        return self::$db_charset;
    }
}
