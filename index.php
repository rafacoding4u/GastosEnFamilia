<?php
// Asegúrate de que no haya ninguna salida antes de session_start
ob_start(); // Inicia el almacenamiento en búfer de salida

require_once __DIR__ . '/app/libs/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';
require_once __DIR__ . '/app/modelo/classModelo.php';
require_once __DIR__ . '/app/controlador/Controller.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
echo "DEBUG: Iniciando index.php <br>";
print_r($_SESSION);

if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0;
    echo "DEBUG: Estableciendo nivel_usuario a 0 <br>";
}

$map = array(
    'home' => array('controller' => 'Controller', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'Controller', 'action' => 'inicio', 'nivel_usuario' => 0),
    'salir' => array('controller' => 'Controller', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'Controller', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'Controller', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'Controller', 'action' => 'registro', 'nivel_usuario' => 0),
    'listarGastos' => array('controller' => 'Controller', 'action' => 'listarGastos', 'nivel_usuario' => 1),
    'insertarGasto' => array('controller' => 'Controller', 'action' => 'insertarGasto', 'nivel_usuario' => 1),
    'listarUsuarios' => array('controller' => 'Controller', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    'eliminarUsuario' => array('controller' => 'Controller', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2),
    
    // Nueva ruta para probar la conexión a la base de datos
    'probarConexionBD' => array('controller' => 'Controller', 'action' => 'probarConexionBD', 'nivel_usuario' => 0)
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
        echo "DEBUG: Llamando a la acción del controlador<br>";
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        echo "DEBUG: Redirigiendo a inicio<br>";
        call_user_func(array(new $controlador['controller'], 'inicio'));
    }
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' . $controlador['controller'] . '->' . $controlador['action'] . '</i> no existe</h1></body></html>';
}

ob_end_flush(); // Enviar el contenido almacenado en el búfer y desactivar el almacenamiento en búfer de salida
?>
