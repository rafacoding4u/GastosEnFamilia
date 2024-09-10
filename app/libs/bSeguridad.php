<?php

// Función para encriptar la contraseña con un coste de procesamiento configurable
function encriptar($password, $cost = 10) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
}

// Función para verificar la contraseña ingresada con la almacenada en la base de datos
function comprobarhash($pass, $passBD) {
    return password_verify($pass, $passBD);
}

// Función para iniciar sesión y establecer la sesión de usuario
function iniciarSesion($usuario) {
    session_start();
    session_regenerate_id(true); // Previene ataques de sesión (session fixation)
    $_SESSION['usuario'] = $usuario; // Almacena el usuario en la sesión
}

// Función para cerrar sesión y limpiar los datos de la sesión
function cerrarSesion() {
    session_unset(); // Limpia todas las variables de sesión
    session_destroy(); // Destruye la sesión
}

// Función para comprobar si un usuario ha iniciado sesión
function checkUser() {
    if (!isset($_SESSION['usuario'])) {
        // Redirigir al formulario de inicio de sesión si no hay usuario en la sesión
        header("Location: web/templates/formIniciarSesion.php");
        exit();
    }
    return $_SESSION['usuario']; // Retorna los datos del usuario almacenados en la sesión
}

// Funciones para verificar los roles del usuario

// Verifica si el usuario es superadmin
function esSuperadmin() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] == 'superadmin';
}

// Verifica si el usuario es administrador
function esAdmin() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] == 'admin';
}

// Verifica si el usuario es un usuario normal
function esUsuarioNormal() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] == 'usuario';
}

?>
