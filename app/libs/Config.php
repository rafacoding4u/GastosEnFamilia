<?php

class Config
{
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = 'Ladilla7890@2';
    private static $db_name = 'gastosencasa_bd';
    private static $db_charset = 'utf8';

    private static $debug = true;

    private static $log_file = __DIR__ . '/../log/logExcepcio.txt';

    public static function getConexion()
    {
        try {
            $dsn = 'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_charset;
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $conexion = new PDO($dsn, self::$db_user, self::$db_pass, $opciones);
            return $conexion;
        } catch (PDOException $e) {
            self::manejarError($e);
            return null;
        }
    }

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
                'usuarios' => ['leer', 'escribir'],
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
            'inicio' => 'usuarios',
            'cerrarSesion' => 'usuarios',
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

    public static function manejarError($e)
    {
        $mensaje_error = "Error: " . $e->getMessage();
        if (self::$debug) {
            echo "<h2>{$mensaje_error}</h2>";
        }
        self::registrarError($mensaje_error);
        error_log($mensaje_error, 3, self::$log_file);
    }

    public static function registrarError($mensaje)
    {
        $fecha = date('Y-m-d H:i:s');
        $log_message = "[{$fecha}] {$mensaje}" . PHP_EOL;

        if (!file_exists(self::$log_file)) {
            file_put_contents(self::$log_file, '', LOCK_EX);
        }

        error_log($log_message, 3, self::$log_file);
    }

    public static function isDebug()
    {
        return self::$debug;
    }

    public static function setDebugMode($debug_mode)
    {
        self::$debug = $debug_mode;
    }

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
