<?php
// Cargar archivos esenciales
require_once __DIR__ . '/app/libs/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';
require_once __DIR__ . '/app/modelo/classModelo.php';
require_once __DIR__ . '/app/modelo/classGastos.php'; // Adaptado a Gastos
require_once __DIR__ . '/app/controlador/GastosController.php'; // Adaptado a GastosController

// Iniciar sesión si no está iniciada
session_start();
if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0; // Nivel 0 por defecto
}

// Definir las rutas del sitio adaptadas a GastosEnFamilia
$map = array(
    'home' => array('controller' => 'GastosController', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'GastosController', 'action' => 'inicio', 'nivel_usuario' => 0),
    'salir' => array('controller' => 'GastosController', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'GastosController', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'GastosController', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'GastosController', 'action' => 'registro', 'nivel_usuario' => 0),
    
    // Rutas específicas para gestionar gastos
    'listarGastos' => array('controller' => 'GastosController', 'action' => 'listarGastos', 'nivel_usuario' => 0),
    'verGasto' => array('controller' => 'GastosController', 'action' => 'verGasto', 'nivel_usuario' => 0),
    'registrarGasto' => array('controller' => 'GastosController', 'action' => 'registrarGasto', 'nivel_usuario' => 1),
    'eliminarGasto' => array('controller' => 'GastosController', 'action' => 'eliminarGasto', 'nivel_usuario' => 1),  
    
    // Administración de usuarios
    'listarUsuarios' => array('controller' => 'GastosController', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    'eliminarUsuario' => array('controller' => 'GastosController', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2)
);

// Verificar la ruta solicitada
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        // Si la ruta no existe, mostrar un error 404
        header('Status: 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' . htmlspecialchars($_GET['ctl']) . '</i></h1></body></html>';
        exit;
    }
} else {
    // Ruta predeterminada si no se ha especificado ninguna
    $ruta = 'home';
}

// Verificar si el método del controlador existe y tiene permisos
$controlador = $map[$ruta];
if (method_exists($controlador['controller'], $controlador['action'])) {
    if ($controlador['nivel_usuario'] <= $_SESSION['nivel_usuario']) {
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        // Redirigir a inicio si no tiene permisos
        call_user_func(array(new $controlador['controller'], 'inicio'));
    }
} else {
    // Error 404 si el método no existe
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' . $controlador['controller'] . '->' . $controlador['action'] . '</i> no existe</h1></body></html>';
}
