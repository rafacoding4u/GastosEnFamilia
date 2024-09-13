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
echo '<pre>';
print_r($_SESSION); // Muestra el contenido de la sesión
echo '</pre>';

// Si el nivel de usuario no está definido, lo inicializamos a 0
if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0;
    echo "DEBUG: Estableciendo nivel_usuario a 0 <br>";
}

// Definir las rutas de la aplicación
$map = array(
    // Rutas de inicio y autenticación
    'home' => array('controller' => 'Controller', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'Controller', 'action' => 'inicio', 'nivel_usuario' => 1),
    'salir' => array('controller' => 'Controller', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'Controller', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'Controller', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'Controller', 'action' => 'registro', 'nivel_usuario' => 0),
    
    // Gestión de gastos
    'verGastos' => array('controller' => 'Controller', 'action' => 'verGastos', 'nivel_usuario' => 1),
    'insertarGasto' => array('controller' => 'Controller', 'action' => 'insertarGasto', 'nivel_usuario' => 1),
    'editarGasto' => array('controller' => 'Controller', 'action' => 'editarGasto', 'nivel_usuario' => 1),
    'eliminarGasto' => array('controller' => 'Controller', 'action' => 'eliminarGasto', 'nivel_usuario' => 1),
    
    // Gestión de ingresos
    'verIngresos' => array('controller' => 'Controller', 'action' => 'verIngresos', 'nivel_usuario' => 1),
    'insertarIngreso' => array('controller' => 'Controller', 'action' => 'insertarIngreso', 'nivel_usuario' => 1),
    'editarIngreso' => array('controller' => 'Controller', 'action' => 'editarIngreso', 'nivel_usuario' => 1),
    'eliminarIngreso' => array('controller' => 'Controller', 'action' => 'eliminarIngreso', 'nivel_usuario' => 1),

    // Gestión financiera
    'verSituacionFinanciera' => array('controller' => 'Controller', 'action' => 'verSituacionFinanciera', 'nivel_usuario' => 1),

    // Gestión de categorías (ingresos y gastos)
    'verCategoriasGastos' => array('controller' => 'Controller', 'action' => 'verCategoriasGastos', 'nivel_usuario' => 2),
    'insertarCategoriaGasto' => array('controller' => 'Controller', 'action' => 'insertarCategoriaGasto', 'nivel_usuario' => 2),
    'editarCategoriaGasto' => array('controller' => 'Controller', 'action' => 'editarCategoriaGasto', 'nivel_usuario' => 2),
    'eliminarCategoriaGasto' => array('controller' => 'Controller', 'action' => 'eliminarCategoriaGasto', 'nivel_usuario' => 2),

    'verCategoriasIngresos' => array('controller' => 'Controller', 'action' => 'verCategoriasIngresos', 'nivel_usuario' => 2),
    'insertarCategoriaIngreso' => array('controller' => 'Controller', 'action' => 'insertarCategoriaIngreso', 'nivel_usuario' => 2),
    'editarCategoriaIngreso' => array('controller' => 'Controller', 'action' => 'editarCategoriaIngreso', 'nivel_usuario' => 2),
    'eliminarCategoriaIngreso' => array('controller' => 'Controller', 'action' => 'eliminarCategoriaIngreso', 'nivel_usuario' => 2),

    // Gestión de usuarios (solo accesible para admins)
    'listarUsuarios' => array('controller' => 'Controller', 'action' => 'listarUsuarios', 'nivel_usuario' => 2),
    'editarUsuario' => array('controller' => 'Controller', 'action' => 'editarUsuario', 'nivel_usuario' => 2),
    'eliminarUsuario' => array('controller' => 'Controller', 'action' => 'eliminarUsuario', 'nivel_usuario' => 2),

    // Probar la conexión a la base de datos
    'probarConexionBD' => array('controller' => 'Controller', 'action' => 'probarConexionBD', 'nivel_usuario' => 0),
);

// Verificar si la ruta existe
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        // Manejar error 404 si la ruta no existe
        header('Status: 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' . htmlspecialchars($_GET['ctl']) . '</i></h1></body></html>';
        exit;
    }
} else {
    // Ruta por defecto es 'home'
    $ruta = 'home';
}

$controlador = $map[$ruta];

// Verificar si el método existe en el controlador
if (method_exists($controlador['controller'], $controlador['action'])) {
    // Verificar nivel de usuario
    if ($controlador['nivel_usuario'] <= $_SESSION['nivel_usuario']) {
        echo "DEBUG: Llamando a la acción del controlador<br>";
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        // Redirigir si no tiene el nivel de acceso adecuado
        echo "DEBUG: Redirigiendo a inicio por nivel de usuario insuficiente<br>";
        call_user_func(array(new $controlador['controller'], 'inicio'));
    }
} else {
    // Manejar error si no existe el controlador o la acción
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' . htmlspecialchars($controlador['controller']) . '->' . htmlspecialchars($controlador['action']) . '</i> no existe</h1></body></html>';
}

ob_end_flush(); // Enviar el contenido almacenado en el búfer y desactivar el almacenamiento en búfer de salida
