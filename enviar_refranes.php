<?php
require_once 'app/modelo/classModelo.php';

// Función para enviar refrán diario
function enviarRefranDiario($momento) {
    $m = new GastosModelo();

    // Obtener el refrán que no se haya usado en los últimos 365 días
    $refran = $m->obtenerRefranNoUsado();
    
    if (!$refran) {
        echo "No hay refrán disponible para enviar.";
        return;
    }

    // Obtener la lista de usuarios
    $usuarios = $m->obtenerUsuarios();

    foreach ($usuarios as $usuario) {
        // Preparar asunto y mensaje del correo
        $asunto = "Refrán del día";
        $mensaje = "Estimado " . htmlspecialchars($usuario['nombre']) . ",\n\n" . htmlspecialchars($refran['texto_refran']);
        $headers = "From: no-reply@gastosfamilia.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Usar PHPMailer o cualquier otro servicio para el envío de correos en lugar de mail()
        if (mail($usuario['email'], $asunto, $mensaje, $headers)) {
            // Registrar el envío en la base de datos
            $m->registrarEnvioRefran($refran['idRefran'], $usuario['idUser'], $momento);
            echo "Refrán enviado a " . htmlspecialchars($usuario['email']) . "\n";
        } else {
            echo "Fallo al enviar el refrán a " . htmlspecialchars($usuario['email']) . "\n";
        }
    }

    echo "Refrán enviado con éxito a todos los usuarios.\n";
}

// Definir la hora de envío y el momento
$horaActual = date('H');

if ($horaActual >= 11 && $horaActual < 12) {
    enviarRefranDiario('mañana');
} elseif ($horaActual >= 20 && $horaActual < 21) {
    enviarRefranDiario('tarde');
} else {
    echo "No es el momento de enviar refranes.\n";
}
