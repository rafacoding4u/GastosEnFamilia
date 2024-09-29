<?php
// Conexión a la base de datos
$conexion = new PDO('mysql:host=localhost;dbname=gastosencasa_bd', 'root', '');

// ID de la familia o grupo a verificar
$idFamilia = 3; // Cambia este valor para probar diferentes familias o grupos
$contraseñaIntroducida = 'Test1234'; // Contraseña que se está verificando

// Consulta para obtener la contraseña cifrada de la familia o grupo
$sql = "SELECT password FROM familias WHERE idFamilia = :idFamilia";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificación de la contraseña
if ($resultado && password_verify($contraseñaIntroducida, $resultado['password'])) {
    echo "La contraseña es correcta.";
} else {
    echo "La contraseña es incorrecta.";
}
