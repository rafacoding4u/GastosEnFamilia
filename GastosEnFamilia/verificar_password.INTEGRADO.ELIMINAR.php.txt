<?php
// Incluir el archivo de configuración para obtener la conexión
require_once 'app/libs/Config.php';

try {
    // Obtener la conexión a la base de datos
    $conexion = Config::getConexion();

    // Parámetros a verificar: puedes cambiar estos valores para hacer diferentes pruebas
    $idFamilia = 3; // ID de la familia o grupo a verificar
    $contraseñaIntroducida = 'La7890@2'; // Contraseña que se está verificando

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

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
