<?php
require_once __DIR__ . '/app/modelo/classModelo.php';

$m = new GastosModelo();

// Define una nueva contraseña temporal para el superadmin
$nuevaContrasena = 'Temp@1234ComplexLa7890@2@';
$hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

// Actualizar la contraseña del superadmin
$sql = "UPDATE usuarios SET contrasenya = :hashedPassword WHERE nivel_usuario = 'superadmin'";
$stmt = $m->getConexion()->prepare($sql);
$stmt->bindValue(':hashedPassword', $hashedPassword);
$stmt->execute();

echo "Contraseña del superadmin actualizada correctamente. La nueva contraseña es: SuperAdmin@1234";
?>
