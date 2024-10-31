<?php
// Inicia el almacenamiento en búfer de salida
ob_start();

// Configuración de rutas y sesión
require_once __DIR__ . '/app/libs/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';
require_once __DIR__ . '/app/libs/Router.php';
require_once __DIR__ . '/app/modelo/classModelo.php';

// Configuración de errores y logs
ini_set('error_log', __DIR__ . '/app/log/php-error.log');
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    error_log("Modo debug activo", 3, __DIR__ . '/app/log/php-error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    error_log("Modo producción activo", 3, __DIR__ . '/app/log/php-error.log');
}

// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log("Sesión iniciada", 3, __DIR__ . '/app/log/php-error.log');
}

// Asignar nivel de usuario 'registro' si no está definido
if (!isset($_SESSION['usuario']['nivel_usuario'])) {
    $_SESSION['usuario']['nivel_usuario'] = 'registro';
    error_log("Nivel de usuario no definido, asignado a 'registro'", 3, __DIR__ . '/app/log/php-error.log');
}

// Crear instancia del enrutador y definir rutas desde el archivo `Router.php`
$router = new Router();
require_once __DIR__ . '/app/libs/RouterConfig.php';  // Archivo donde configurar todas las rutas

// Verificar autenticación para rutas protegidas
$ruta = $_GET['ctl'] ?? 'home';
$rutasPermitidasSinAutenticacion = ['iniciarSesion', 'registro', 'home'];
$rutasSoloSuperadmin = [
    'verAuditoria',
    'listarFamilias',
    'verGrupos',
    'verSituacion',
    'verCategoriasGastos',
    'verCategoriasIngresos',
    'verPresupuestos',
    'verMetasGlobales',
    'formAsignarUsuario'
];

// Redirigir a iniciar sesión si no está autenticado y la ruta no es pública
if (!isset($_SESSION['usuario']) && !in_array($ruta, $rutasPermitidasSinAutenticacion)) {
    header('Location: index.php?ctl=iniciarSesion');
    exit();
}

// Verificar si el usuario tiene permisos de superadmin para acceder a rutas exclusivas de este nivel
if (in_array($ruta, $rutasSoloSuperadmin) && $_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
    header('Location: index.php?ctl=error');
    exit();
}

// Procesar la solicitud de la ruta actual
$router->handleRequest($ruta);

ob_end_flush(); // Finaliza el almacenamiento en búfer y envía la salida al navegador

// Información de depuración (solo en modo debug)
if (Config::isDebug()) {
    echo '<h3>Información de Depuración</h3>';
    echo '<h4>Datos de la sesión:</h4>';
    echo '<pre>' . print_r($_SESSION, true) . '</pre>';
    echo '<h4>Parámetros GET:</h4>';
    echo '<pre>' . print_r($_GET, true) . '</pre>';
    echo '<h4>Parámetros POST:</h4>';
    echo '<pre>' . print_r($_POST, true) . '</pre>';
}
