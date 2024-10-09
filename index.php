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

// Definir la ruta para el archivo de log de errores
ini_set('error_log', 'C:/xampp/htdocs/DWES/GastosEnFamilia/php-error.log');

// Configuración de errores basada en el modo debug
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1); // Mostrar los errores en pantalla
} else {
    error_reporting(0); // Ocultar los errores en producción
    ini_set('display_errors', 0);
}

session_start();

// Inicializa el nivel de usuario si no está definido
if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0;
}

// Mapeo de rutas y sus controladores, incluyendo las opciones de SuperUsuario
$map = array(
    // Rutas de inicio y autenticación
    'home' => array('controller' => 'AuthController', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'AuthController', 'action' => 'inicio', 'nivel_usuario' => 1),
    'salir' => array('controller' => 'AuthController', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'AuthController', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'AuthController', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'AuthController', 'action' => 'registro', 'nivel_usuario' => 0),

    // Gestión de usuarios
    'listarUsuarios' => array('controller' => 'UsuarioController', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    'editarUsuario' => array('controller' => 'UsuarioController', 'action' => 'editarUsuario', 'nivel_usuario' => 2),
    'eliminarUsuario' => array('controller' => 'UsuarioController', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2),
    'crearUsuario' => array('controller' => 'UsuarioController', 'action' => 'crearUsuario', 'nivel_usuario' => 2),

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
    'eliminarCategoriaIngreso' => array('controller' => 'CategoriaController', 'action' => 'eliminarCategoriaIngreso', 'nivel_usuario' => 1),

    // Gestión de familias y grupos
    'listarFamilias' => array('controller' => 'FamiliaGrupoController', 'action' => 'listarFamilias', 'nivel_usuario' => 2),
    'listarGrupos' => array('controller' => 'FamiliaGrupoController', 'action' => 'listarGrupos', 'nivel_usuario' => 2),
    'formCrearFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'formCrearFamilia', 'nivel_usuario' => 2),
    'crearFamilia' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearFamilia', 'nivel_usuario' => 2),
    'formCrearGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'formCrearGrupo', 'nivel_usuario' => 2),
    'crearGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'crearGrupo', 'nivel_usuario' => 2),

    // Nuevas funciones para SuperUsuario (asignar usuarios a familias o grupos)
    'formAsignarUsuario' => array('controller' => 'FamiliaGrupoController', 'action' => 'formAsignarUsuario', 'nivel_usuario' => 2),
    // En la parte del mapeo de rutas en $map
    'asignarUsuarioFamiliaGrupo' => array('controller' => 'FamiliaGrupoController', 'action' => 'asignarUsuarioFamiliaGrupo', 'nivel_usuario' => 2),


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
);

// Verificar si la ruta solicitada existe
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        // Manejo de error 404 si la ruta no es válida
        header('HTTP/1.0 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' . htmlspecialchars($_GET['ctl']) . '</i></h1></body></html>';
        exit;
    }
} else {
    $ruta = 'home';
}

$controlador = $map[$ruta];

// Verificar si el método solicitado existe en el controlador
try {
    if (method_exists($controlador['controller'], $controlador['action'])) {
        // Comprobar el nivel de acceso del usuario
        if ($controlador['nivel_usuario'] <= $_SESSION['nivel_usuario']) {
            call_user_func(array(new $controlador['controller'], $controlador['action']));
        } else {
            // Redireccionar a una página de acceso denegado o página de inicio
            header('HTTP/1.0 403 Forbidden');
            echo '<html><body><h1>Acceso denegado: No tienes suficientes privilegios para acceder a esta página.</h1></body></html>';
            exit;
        }
    } else {
        throw new Exception("El controlador o acción no existe.");
    }
} catch (Exception $e) {
    if (Config::isDebug()) {
        echo '<h2>Error: ' . $e->getMessage() . '</h2>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        error_log($e->getMessage());
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
