<?php
session_start();

// Configuración global de registro de errores
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp2/htdocs/PROY/LasCuentasClaras/app/log/php_errors.log');
error_reporting(E_ALL);  // Cambia a E_ERROR en producción

// Manejadores personalizados de errores y excepciones
function manejarErrores($severity, $message, $file, $line)
{
    error_log("Error [$severity]: $message en $file en la línea $line");
}

function manejarExcepciones($exception)
{
    error_log("Excepción no capturada: " . $exception->getMessage());
}

set_error_handler("manejarErrores");
set_exception_handler("manejarExcepciones");

require_once 'app/libs/Router.php';

// Instanciar el enrutador
$router = new Router();

// Agregar la ruta de error al router
$router->addRoute('error', 'AuthController', 'mostrarError');

// Definir rutas públicas
$router->addRoute('iniciarSesion', 'AuthController', 'iniciarSesion');
$router->addRoute('registro', 'AuthController', 'registro');
$router->addRoute('home', 'AuthController', 'home');

// Definir rutas protegidas para autenticación y funcionalidad principal
$router->addRoute('inicio', 'AuthController', 'inicio');
$router->addRoute('salir', 'AuthController', 'salir');

// Rutas para funcionalidades de familias
$router->addRoute('listarFamilias', 'FamiliaGrupoController', 'listarFamilias');
$router->addRoute('formCrearFamilia', 'FamiliaGrupoController', 'mostrarFormularioCrearFamilia');
$router->addRoute('crearFamilia', 'FamiliaGrupoController', 'crearFamilia');
$router->addRoute('crearVariasFamilias', 'FamiliaGrupoController', 'crearVariasFamilias');
$router->addRoute('editarFamilia', 'FamiliaGrupoController', 'editarFamilia');
$router->addRoute('eliminarFamilia', 'FamiliaGrupoController', 'eliminarFamilia');

// Rutas para funcionalidades de grupos
$router->addRoute('listarGrupos', 'FamiliaGrupoController', 'listarGrupos');
$router->addRoute('formCrearGrupo', 'FamiliaGrupoController', 'formCrearGrupo');
$router->addRoute('crearGrupo', 'FamiliaGrupoController', 'crearGrupo');
$router->addRoute('crearVariosGrupos', 'FamiliaGrupoController', 'crearVariosGrupos');
$router->addRoute('editarGrupo', 'FamiliaGrupoController', 'editarGrupo');
$router->addRoute('eliminarGrupo', 'FamiliaGrupoController', 'eliminarGrupo');

// Rutas para gestión de usuarios
$router->addRoute('crearUsuario', 'UsuarioController', 'crearUsuario');
$router->addRoute('actualizarUsuario', 'UsuarioController', 'actualizarUsuario');
$router->addRoute('eliminarUsuario', 'UsuarioController', 'eliminarUsuario');
$router->addRoute('listarUsuarios', 'UsuarioController', 'listarUsuarios');
$router->addRoute('formCrearUsuario', 'UsuarioController', 'formCrearUsuario');
$router->addRoute('formEditarUsuario', 'UsuarioController', 'formEditarUsuario');
$router->addRoute('editarUsuario', 'UsuarioController', 'editarUsuario');
$router->addRoute('asignarUsuario', 'UsuarioController', 'asignarUsuario');
$router->addRoute('formAsignarUsuario', 'FamiliaGrupoController', 'formAsignarUsuario');
$router->addRoute('asignarUsuarioFamiliaGrupo', 'FamiliaGrupoController', 'asignarUsuarioFamiliaGrupo');

// Rutas para categorías de gastos
$router->addRoute('verCategoriasGastos', 'CategoriaController', 'verCategoriasGastos');
$router->addRoute('insertarCategoriaGasto', 'CategoriaController', 'insertarCategoriaGasto');

// Rutas para categorías de ingresos
$router->addRoute('verCategoriasIngresos', 'CategoriaController', 'verCategoriasIngresos');
$router->addRoute('insertarCategoriaIngreso', 'CategoriaController', 'insertarCategoriaIngreso');
$router->addRoute('editarCategoriaIngreso', 'CategoriaController', 'editarCategoriaIngreso');
$router->addRoute('editarCategoriaGasto', 'CategoriaController', 'editarCategoriaGasto');   
$router->addRoute('eliminarCategoriaIngreso', 'CategoriaController', 'eliminarCategoriaIngreso');

// Verificar si el usuario está autenticado para acceder a rutas protegidas
$ruta = $_GET['ctl'] ?? 'home';
$rutasPermitidasSinAutenticacion = ['iniciarSesion', 'registro', 'home', 'error'];

if (!isset($_SESSION['usuario']) && !in_array($ruta, $rutasPermitidasSinAutenticacion)) {
    header('Location: index.php?ctl=iniciarSesion');
    exit();
}

// Procesar la solicitud de la ruta actual
$router->handleRequest($ruta);
