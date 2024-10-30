<?php

// Instanciar el enrutador y definir rutas en este archivo para mantenerlas centralizadas

// Rutas públicas
$router->addRoute('iniciarSesion', 'AuthController', 'iniciarSesion');
$router->addRoute('registro', 'AuthController', 'registro');
$router->addRoute('home', 'AuthController', 'home');

// Rutas protegidas - Nivel usuario regular y superior
$router->addRoute('inicio', 'AuthController', 'inicio');
$router->addRoute('salir', 'AuthController', 'salir');
$router->addRoute('listarUsuarios', 'UsuarioController', 'listarUsuarios');
$router->addRoute('crearUsuario', 'UsuarioController', 'crearUsuario');
$router->addRoute('eliminarUsuario', 'UsuarioController', 'eliminarUsuario');
$router->addRoute('actualizarUsuario', 'UsuarioController', 'actualizarUsuario');
$router->addRoute('formCrearUsuario', 'UsuarioController', 'formCrearUsuario');

// Rutas específicas de SuperAdmin
$router->addRoute('verAuditoria', 'AuditoriaController', 'verAuditoria');
$router->addRoute('listarFamilias', 'FamiliaGrupoController', 'listarFamilias');
$router->addRoute('verGrupos', 'FamiliaGrupoController', 'listarGrupos');
$router->addRoute('verSituacion', 'SituacionFinancieraController', 'verSituacion');
$router->addRoute('verCategoriasGastos', 'CategoriaController', 'verCategoriasGastos');
$router->addRoute('verCategoriasIngresos', 'CategoriaController', 'verCategoriasIngresos');
$router->addRoute('verPresupuestos', 'FinanzasController', 'verPresupuestos');
$router->addRoute('verMetasGlobales', 'SituacionFinancieraController', 'verMetasGlobales');
$router->addRoute('formAsignarUsuario', 'FamiliaGrupoController', 'formAsignarUsuario');

// Rutas adicionales para otros roles o funcionalidades especiales
// Agrega más rutas según las necesidades de cada nivel de usuario
