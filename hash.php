<?php
// Generar un nuevo hash de contraseña
$hashedPassword = password_hash('Test1234', PASSWORD_DEFAULT);
echo $hashedPassword;
?>

