<?php
require_once __DIR__ . '/app/modelo/classModelo.php'; // Asegúrate de que este es el camino correcto hacia tu modelo

$m = new GastosModelo();

// Probar la conexión antes de proceder
if (!$m->pruebaConexion()) {
    die('No se pudo establecer la conexión con la base de datos.');
}

// Actualizar las contraseñas de todas las familias
$familias = $m->obtenerFamilias();
foreach ($familias as $familia) {
    $hashedPassword = password_hash('Test1234', PASSWORD_DEFAULT);
    $sql = "UPDATE familias SET password = :hashedPassword WHERE idFamilia = :idFamilia";
    $stmt = $m->getConexion()->prepare($sql);
    $stmt->bindValue(':hashedPassword', $hashedPassword);
    $stmt->bindValue(':idFamilia', $familia['idFamilia']);
    $stmt->execute();
}

// Actualizar las contraseñas de todos los grupos
$grupos = $m->obtenerGrupos();
foreach ($grupos as $grupo) {
    $hashedPassword = password_hash('Test1234', PASSWORD_DEFAULT);
    $sql = "UPDATE grupos SET password = :hashedPassword WHERE idGrupo = :idGrupo";
    $stmt = $m->getConexion()->prepare($sql);
    $stmt->bindValue(':hashedPassword', $hashedPassword);
    $stmt->bindValue(':idGrupo', $grupo['idGrupo']);
    $stmt->execute();
}

echo "Contraseñas actualizadas correctamente.";

