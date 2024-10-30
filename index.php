<?php
// Inicia el almacenamiento en búfer de salida para evitar problemas de cabeceras
ob_start();

require_once __DIR__ . '/app/libs/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';
require_once __DIR__ . '/app/modelo/classModelo.php';

// Incluimos los controladores necesarios
require_once __DIR__ . '/app/controlador/AuthController.php';
require_once __DIR__ . '/app/controlador/CategoriaController.php';
require_once __DIR__ . '/app/controlador/FamiliaGrupoController.php';
require_once __DIR__ . '/app/controlador/FinanzasController.php';
require_once __DIR__ . '/app/controlador/SituacionFinancieraController.php';
require_once __DIR__ . '/app/controlador/UsuarioController.php';
require_once __DIR__ . '/app/controlador/AuditoriaController.php';

// Definir la ruta para el archivo de log de errores
ini_set('error_log', __DIR__ . '/app/log/php-error.log');

// Configuración de errores basada en el modo debug
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    error_log("Modo debug activo, mostrando errores en pantalla", 3, __DIR__ . '/app/log/php-error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    error_log("Modo producción activo, ocultando errores en pantalla", 3, __DIR__ . '/app/log/php-error.log');
}

// Verificar si la sesión ya está iniciada antes de llamar a session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log("Sesión iniciada", 3, __DIR__ . '/app/log/php-error.log');
} else {
    error_log("Sesión ya activa, no se vuelve a iniciar", 3, __DIR__ . '/app/log/php-error.log');
}

// Inicializa el nivel de usuario si no está definido
if (!isset($_SESSION['usuario']['nivel_usuario'])) {
    $_SESSION['usuario']['nivel_usuario'] = 'registro'; // Asignamos el nivel 'registro' para usuarios no autenticados
    error_log("Nivel de usuario no definido, asignado a 'registro'", 3, __DIR__ . '/app/log/php-error.log');
}

// Mapeo de rutas y sus controladores, incluyendo las opciones de SuperUsuario
$map = array(
    // Rutas de inicio y autenticación
    'home' => array('controller' => 'AuthController', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'AuthController', 'action' => 'inicio', 'nivel_usuario' => 0),
    'salir' => array('controller' => 'AuthController', 'action' => 'salir', 'nivel_usuario' => 0),
    'error' => array('controller' => 'AuthController', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'AuthController', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'AuthController', 'action' => 'registro', 'nivel_usuario' => 0),

    // Nueva ruta para el registro individual
    'registroInd' => array('controller' => 'AuthController', 'action' => 'registroInd', 'nivel_usuario' => 0),

    // Gestión de usuarios
    'listarUsuarios' => array('controller' => 'UsuarioController', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    /*'editarUsuario' => array('controller' => 'UsuarioController', 'action' => 'editarUsuario', 'nivel_usuario' => 2),*/
    'eliminarUsuario' => array('controller' => 'UsuarioController', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2),
    'crearUsuario' => array('controller' => 'UsuarioController', 'action' => 'crearUsuario', 'nivel_usuario' => 2),
    // Añadir mapeo en el array $map
    'actualizarUsuario' => array('controller' => 'UsuarioController', 'action' => 'actualizarUsuario', 'nivel_usuario' => 1),

    'formCrearUsuario' => array('controller' => 'UsuarioController', 'action' => 'formCrearUsuario', 'nivel_usuario' => 1),

    // **Ruta para actualizar contraseñas**
    'actualizar_contraseñas' => array('controller' => 'UsuarioController', 'action' => 'ejecutarActualizacionContraseñas', 'nivel_usuario' => 1),
    // **Ruta para asignar contraseñas premium**
    'asignarPasswordPremium' => array('controller' => 'UsuarioController', 'action' => 'asignarPasswordPremium', 'nivel_usuario' => 1),

    // Gestión de categorías gastos
    'verCategoriasGastos' => array('controller' => 'CategoriaController', 'action' => 'verCategoriasGastos', 'nivel_usuario' => 1),
    'insertarCategoriaGasto' => array('controller' => 'CategoriaController', 'action' => 'insertarCategoriaGasto', 'nivel_usuario' => 1),
    'actualizarCategoriaGasto' => array('controller' => 'CategoriaController', 'action' => 'actualizarCategoriaGasto', 'nivel_usuario' => 1),
    'eliminarCategoriaGasto' => array('controller' => 'CategoriaController', 'action' => 'eliminarCategoriaGasto', 'nivel_usuario' => 1),
    'editarCategoriaGasto' => array('controller' => 'CategoriaController', 'action' => 'editarCategoriaGasto', 'nivel_usuario' => 1),

    // Gestión de categorías ingresos
    'verCategoriasIngresos' => array('controller' => 'CategoriaController', 'action' => 'verCategoriasIngresos', 'nivel_usuario' => 1),
    'insertarCategoriaIngreso' => array('controller' => 'CategoriaController', 'action' => 'insertarCategoriaIngreso', 'nivel_usuario' => 1),
    'editarCategoriaIngreso' => array('controller' => 'CategoriaController', 'action' => 'editarCategoriaIngreso', 'nivel_usuario' => 1),
    'actualizarCategoriaIngreso' => array('controller' => 'CategoriaController', 'action' => 'actualizarCategoriaIngreso', 'nivel_usuario' => 1),

    // Gestión de familias y grupos
    'listarFamilias' => array('controller' => 'FamiliaGrupoController', 'action' => 'listarFamilias', 'nivel_usuario' => 2),
    'listarGrupos' => array('controller' => 'FamiliaGrupoController', 'action' => 'listarGrupos', 'nivel_usuario' => 2),
    'formCrearFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'formCrearFamilia', 'nivel_usuario' => 2),
    'crearFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearFamilia', 'nivel_usuario' => 2),
    'formCrearGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'formCrearGrupo', 'nivel_usuario' => 2),
    'crearGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearGrupo', 'nivel_usuario' => 2),
    'editarFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'editarFamilia', 'nivel_usuario' => 2),
    'editarGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'editarGrupo', 'nivel_usuario' => 2),
    'eliminarFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'eliminarFamilia', 'nivel_usuario' => 2),
    'eliminarGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'eliminarGrupo', 'nivel_usuario' => 2),
    'verGrupos' => array('controller' => 'FamiliaGrupoController', 'action' => 'listarGrupos', 'nivel_usuario' => 2),

    // Ruta para crear familias y grupos adicionales
    'formCrearFamiliaGrupoAdicionales' => array('controller' => 'FamiliaGrupoController', 'action' => 'formCrearFamiliaGrupoAdicionales', 'nivel_usuario' => 'admin'),
    'crearFamiliaGrupoAdicionales' => array('controller' => 'UsuarioController', 'action' => 'crearFamiliaGrupoAdicionales', 'nivel_usuario' => 2),

    // Nuevas funciones para SuperUsuario (asignar usuarios a familias o grupos)
    'formAsignarUsuario' => array('controller' => 'FamiliaGrupoController', 'action' => 'formAsignarUsuario', 'nivel_usuario' => 2),
    'asignarUsuarioFamiliaGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'asignarUsuarioFamiliaGrupo', 'nivel_usuario' => 2),
    'crearVariasFamilias' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearVariasFamilias', 'nivel_usuario' => 2),
    'crearVariosGrupos' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearVariosGrupos', 'nivel_usuario' => 2),

    // Gestión financiera
    'verGastos' => array('controller' => 'FinanzasController', 'action' => 'verGastos', 'nivel_usuario' => 1),
    'formInsertarGasto' => array('controller' => 'FinanzasController', 'action' => 'formInsertarGasto', 'nivel_usuario' => 1),
    'insertarGasto' => array('controller' => 'FinanzasController', 'action' => 'insertarGasto', 'nivel_usuario' => 1),
    'editarGasto' => array('controller' => 'FinanzasController', 'action' => 'editarGasto', 'nivel_usuario' => 1),
    'eliminarGasto' => array('controller' => 'FinanzasController', 'action' => 'eliminarGasto', 'nivel_usuario' => 1),
    'actualizarGasto' => array('controller' => 'FinanzasController', 'action' => 'editarGasto', 'nivel_usuario' => 1),
    'verIngresos' => array('controller' => 'FinanzasController', 'action' => 'verIngresos', 'nivel_usuario' => 1),
    'formInsertarIngreso' => array('controller' => 'FinanzasController', 'action' => 'formInsertarIngreso', 'nivel_usuario' => 1),
    'insertarIngreso' => array('controller' => 'FinanzasController', 'action' => 'insertarIngreso', 'nivel_usuario' => 1),
    'editarIngreso' => array('controller' => 'FinanzasController', 'action' => 'editarIngreso', 'nivel_usuario' => 1),
    'actualizarIngreso' => array('controller' => 'FinanzasController', 'action' => 'editarIngreso', 'nivel_usuario' => 1),
    'eliminarIngreso' => array('controller' => 'FinanzasController', 'action' => 'eliminarIngreso', 'nivel_usuario' => 1),

    // Situación financiera
    'verSituacion' => array('controller' => 'SituacionFinancieraController', 'action' => 'verSituacion', 'nivel_usuario' => 1),
    'dashboard' => array('controller' => 'SituacionFinancieraController', 'action' => 'dashboard', 'nivel_usuario' => 1),
    'SituacionFinancieraController' => array('controller' => 'SituacionFinancieraController', 'action' => 'verSituacion', 'nivel_usuario' => 1),

    // Añadir la ruta para ver la auditoría (solo superadmin tiene permiso)
    'verAuditoria' => array('controller' => 'AuditoriaController', 'action' => 'verAuditoria', 'nivel_usuario' => 2),
);

// verificación de la URL para asignar contraseñas premium
if (isset($_GET['ctl']) && $_GET['ctl'] == 'asignarPasswordPremium') {
    if (isset($_GET['idUsuario']) && isset($_GET['password'])) {
        // Obtener los parámetros de la URL
        $idUsuario = $_GET['idUsuario'];
        $passwordPremium = $_GET['password'];

        // Crear una instancia del controlador
        $controlador = new UsuarioController();
        // Llamar al método que asigna la contraseña premium
        $controlador->asignarPasswordPremium($idUsuario, $passwordPremium);
        exit(); // Detener la ejecución para evitar que pase por el flujo del mapeo de rutas
    }
}

// Verificar si la ruta solicitada existe
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
        error_log("Ruta encontrada: {$_GET['ctl']}", 3, __DIR__ . '/app/log/php-error.log');
    } else {
        // Manejo de error 404 si la ruta no es válida
        header('HTTP/1.0 404 Not Found');
        error_log("Ruta no encontrada: " . htmlspecialchars($_GET['ctl'] ?? ''), 3, __DIR__ . '/app/log/php-error.log');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' . htmlspecialchars($_GET['ctl']) . '</i></h1></body></html>';
        exit;
    }
} else {
    $ruta = 'home';
    error_log("Ruta por defecto 'home' asignada", 3, __DIR__ . '/app/log/php-error.log');
}

$controlador = $map[$ruta];

// Verificar si el método solicitado existe en el controlador
try {
    $controllerInstance = new $controlador['controller'];

    // Debug adicional para verificar las acciones disponibles en el controlador
    error_log("Acciones disponibles en " . $controlador['controller'] . ": " . implode(', ', get_class_methods($controllerInstance)), 3, __DIR__ . '/app/log/php-error.log');

    if (isset($_GET['action']) && method_exists($controllerInstance, $_GET['action'])) {
        $action = $_GET['action'];
    } else if (method_exists($controllerInstance, $controlador['action'])) {
        $action = $controlador['action'];  // Acción por defecto si no está definida en la URL
    } else {
        throw new Exception("La acción especificada no existe en el controlador.");
    }

    if ($controlador['nivel_usuario'] <= $_SESSION['usuario']['nivel_usuario']) {
        error_log("Ejecutando acción: {$action} en {$controlador['controller']}", 3, __DIR__ . '/app/log/php-error.log');
        call_user_func(array($controllerInstance, $action));
    } else {
        header('HTTP/1.0 403 Forbidden');
        error_log("Acceso denegado para la ruta: {$_GET['ctl']}", 3, __DIR__ . '/app/log/php-error.log');
        echo '<html><body><h1>Acceso denegado: No tienes suficientes privilegios para acceder a esta página.</h1></body></html>';
        exit;
    }
} catch (Exception $e) {
    error_log("Error capturado: " . $e->getMessage(), 3, __DIR__ . '/app/log/php-error.log');
    if (Config::isDebug()) {
        echo '<h2>Error: ' . $e->getMessage() . '</h2>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        error_log($e->getMessage(), 3, __DIR__ . '/app/log/php-error.log');
        echo '<h2>Ocurrió un error. Por favor, inténtalo más tarde.</h2>';
    }
}

ob_end_flush(); // Finaliza el almacenamiento en búfer y envía la salida al navegador

// Información de debug adicional (solo en modo debug)
if (Config::isDebug()) {
    echo '<h3>Información de Depuración</h3>';
    echo '<h4>Datos de la sesión:</h4>';
    echo '<pre>' . print_r($_SESSION, true) . '</pre>';
    echo '<h4>Parámetros GET:</h4>';
    echo '<pre>' . print_r($_GET, true) . '</pre>';
    echo '<h4>Parámetros POST:</h4>';
    echo '<pre>' . print_r($_POST, true) . '</pre>';
}
