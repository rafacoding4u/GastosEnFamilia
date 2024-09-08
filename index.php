<?php
require_once __DIR__ . '/app/libs/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';
require_once __DIR__ . '/app/modelo/classModelo.php';
require_once __DIR__ . '/app/controlador/Controller.php';

session_start();
if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0;
}

$map = array(
    'home' => array('controller' => 'Controller', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'Controller', 'action' => 'inicio', 'nivel_usuario' => 0),
    'salir' => array('controller' => 'Controller', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'Controller', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'Controller', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'Controller', 'action' => 'registro', 'nivel_usuario' => 0),

    // Rutas actualizadas para gestionar gastos
    'listarGastos' => array('controller' => 'Controller', 'action' => 'listarGastos', 'nivel_usuario' => 1),
    'insertarGasto' => array('controller' => 'Controller', 'action' => 'insertarGasto', 'nivel_usuario' => 1),

    // Mantén las rutas de usuarios si las necesitas
    'listarUsuarios' => array('controller' => 'Controller', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    'eliminarUsuario' => array('controller' => 'Controller', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2)
);

if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        header('Status: 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' . $_GET['ctl'] . '</i></h1></body></html>';
        exit;
    }
} else {
    $ruta = 'home';
}

$controlador = $map[$ruta];
if (method_exists($controlador['controller'], $controlador['action'])) {
    if ($controlador['nivel_usuario'] <= $_SESSION['nivel_usuario']) {
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        call_user_func(array(new $controlador['controller'], 'inicio'));
    }
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' . $controlador['controller'] . '->' . $controlador['action'] . '</i> no existe</h1></body></html>';
}
?>
