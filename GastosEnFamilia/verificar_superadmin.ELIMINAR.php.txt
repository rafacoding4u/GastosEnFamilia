<?php
// Contraseña temporal que estás probando
$password_input = 'Temp@1234ComplexLa7890@2';

// Hash almacenado en la base de datos
$hashed_password = '$2y$10$bZhCGhmWcm4MruLos3oSL.ukjcyumiMw4dE0RhlFkt6H5TB9ZbEjW'; // Coloca aquí el hash completo

// Verificar si la contraseña es correcta
if (password_verify($password_input, $hashed_password)) {
    echo "La contraseña es correcta.";
} else {
    echo "La contraseña es incorrecta.";
}
