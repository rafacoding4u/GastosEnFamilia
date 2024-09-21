<div class="container text-center p-4">
    <h3>Ha ocurrido un error</h3>
    
    <!-- Mostrar mensaje de error si está definido -->
    <?php if (isset($params['mensaje'])): ?>
        <p><b><span style="color: #d9534f;"> <!-- Usar una alerta de color rojo -->
            <?= htmlspecialchars($params['mensaje']) ?>
        </span></b></p>
    <?php else: ?>
        <p><b><span style="color: #d9534f;">
            Algo salió mal. Inténtalo de nuevo más tarde.
        </span></b></p>
    <?php endif; ?>

    <!-- Botón para regresar a la página principal -->
    <a href="index.php?ctl=home" class="btn btn-primary mt-3">Volver al inicio</a>
</div>
<?php $contenido = ob_get_clean(); ?>
