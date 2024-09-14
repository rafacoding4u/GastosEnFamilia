<?php include 'layout.php'; ?>

<div class="container text-center p-4">
    <h3>Iniciar Sesión</h3>

    <!-- Formulario para iniciar sesión, envía la solicitud al controlador de iniciar sesión -->
    <form action="index.php?ctl=iniciarSesion" method="post">
        <!-- Campo de entrada para el alias (nombre de usuario) -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" required>
        </div>

        <!-- Campo de entrada para la contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <!-- Botón de envío del formulario -->
        <button type="submit" name="bIniciarSesion" class="btn btn-primary mt-3">Iniciar Sesión</button>

        <!-- Bloque para mostrar los mensajes de error, si existen -->
        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>
