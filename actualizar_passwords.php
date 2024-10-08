<?php
require_once __DIR__ . '/app/modelo/classModelo.php'; // Asegúrate de que esta es la ruta correcta hacia tu modelo

$m = new GastosModelo();

// Probar la conexión antes de proceder
if (!$m->pruebaConexion()) {
    die('No se pudo establecer la conexión con la base de datos.');
}

// Define una contraseña temporal compleja
$contraseñaTemporal = 'Temp@1234ComplexLa7890@2';
$hashedPassword = password_hash($contraseñaTemporal, PASSWORD_DEFAULT);

// Actualizar las contraseñas de todas las familias
$familias = $m->obtenerFamilias();
foreach ($familias as $familia) {
    echo "Familia ID: " . $familia['idFamilia'] . "<br>";
    if (!isset($familia['password']) || empty($familia['password'])) {
        echo "La familia con ID {$familia['idFamilia']} no tiene una contraseña definida.\n";
        continue;
    }

    $sql = "UPDATE familias SET password = :hashedPassword WHERE idFamilia = :idFamilia";
    $stmt = $m->getConexion()->prepare($sql);
    $stmt->bindValue(':hashedPassword', $hashedPassword);
    $stmt->bindValue(':idFamilia', $familia['idFamilia']);
    $stmt->execute();

    echo "Contraseña de la familia con ID {$familia['idFamilia']} actualizada correctamente a la contraseña temporal.\n";
}

// Actualizar las contraseñas de todos los grupos
$grupos = $m->obtenerGrupos();
foreach ($grupos as $grupo) {
    echo "Grupo ID: " . $grupo['idGrupo'] . "<br>";
    if (!isset($grupo['password']) || empty($grupo['password'])) {
        echo "El grupo con ID {$grupo['idGrupo']} no tiene una contraseña definida.\n";
        continue;
    }

    $sql = "UPDATE grupos SET password = :hashedPassword WHERE idGrupo = :idGrupo";
    $stmt = $m->getConexion()->prepare($sql);
    $stmt->bindValue(':hashedPassword', $hashedPassword);
    $stmt->bindValue(':idGrupo', $grupo['idGrupo']);
    $stmt->execute();

    echo "Contraseña del grupo con ID {$grupo['idGrupo']} actualizada correctamente a la contraseña temporal.\n";
}

// Actualizar las contraseñas de todos los usuarios
$usuarios = $m->obtenerUsuarios();
foreach ($usuarios as $usuario) {
    // Forzar la actualización de la contraseña del superadmin
    if ($usuario['nivel_usuario'] == 'superadmin') {
        $sql = "UPDATE usuarios SET contrasenya = :hashedPassword WHERE idUser = :idUser";
        $stmt = $m->getConexion()->prepare($sql);
        $stmt->bindValue(':hashedPassword', $hashedPassword);
        $stmt->bindValue(':idUser', $usuario['idUser']);
        $stmt->execute();

        echo "Contraseña del superadmin con ID {$usuario['idUser']} actualizada correctamente a la contraseña temporal.\n";
    } else {
        // Actualizar las contraseñas del resto de usuarios
        $sql = "UPDATE usuarios SET contrasenya = :hashedPassword WHERE idUser = :idUser";
        $stmt = $m->getConexion()->prepare($sql);
        $stmt->bindValue(':hashedPassword', $hashedPassword);
        $stmt->bindValue(':idUser', $usuario['idUser']);
        $stmt->execute();

        echo "Contraseña del usuario con ID {$usuario['idUser']} actualizada correctamente a la contraseña temporal.\n";
    }
}

echo "Contraseñas de usuarios, familias y grupos actualizadas correctamente a la contraseña temporal.";

