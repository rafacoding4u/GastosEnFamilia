<?php
require_once 'app/modelo/classModelo.php';

function enviarRefranDiario($momento) {
    // Instanciar el modelo
    $m = new GastosModelo();

    // Obtener el refrán que no se haya usado en 365 días
    $refran = $m->obtenerRefranNoUsado();

    // Si no hay refrán disponible, salir
    if (!$refran) {
        echo "No hay refrán disponible para enviar.";
        return;
    }

    // Obtener la lista de usuarios
    $usuarios = $m->listarUsuarios();

    foreach ($usuarios as $usuario) {
        // Enviar refrán por correo electrónico
        $asunto = "Refrán del día";
        $mensaje = $refran['texto_refran'];
        $headers = "From: no-reply@gastosfamilia.com";
        
        // Aquí usamos la función mail() para enviar el correo
        mail($usuario['email'], $asunto, $mensaje, $headers);

        // Si tienes un servicio de SMS, puedes usar una función aquí para enviar SMS al usuario:
        // enviarSMS($usuario['telefono'], $mensaje);

        // Registrar el envío en la base de datos
        $m->registrarEnvioRefran($refran['idRefran'], $usuario['idUser'], $momento);
    }

    echo "Refrán enviado con éxito a todos los usuarios.";
}

// Determinar si es la mañana o la tarde
$horaActual = date('H');
if ($horaActual >= 11 && $horaActual < 12) {
    // Enviar a las 11:00 AM
    enviarRefranDiario('mañana');
} elseif ($horaActual >= 20 && $horaActual < 21) {
    // Enviar a las 8:00 PM
    enviarRefranDiario('tarde');
} else {
    echo "No es el momento de enviar refranes.";
}
