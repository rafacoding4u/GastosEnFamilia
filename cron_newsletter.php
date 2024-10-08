<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';
require_once 'app/controlador/FinanzasController.php';

// Instancias necesarias
$m = new GastosModelo();
$controller = new FinanzasController();

// Obtener todos los usuarios para enviarles la News Letter
$usuarios = $m->obtenerTodosLosUsuarios();

foreach ($usuarios as $usuario) {
    $idUser = $usuario['idUser'];
    $email = $usuario['email'];

    // Generar y enviar la News Letter para cada usuario
    $controller->enviarNewsLetter($idUser, $email);
}

echo "News Letters enviadas con éxito.";
