<?php
// Generar un nuevo hash de contraseña
$hashedPassword = password_hash('Temp@1234ComplexLa7890@2', PASSWORD_DEFAULT);
echo $hashedPassword;

