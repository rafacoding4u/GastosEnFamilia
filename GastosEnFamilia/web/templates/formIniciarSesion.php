<div class="container text-center p-4">
    <h3>Iniciar Sesión</h3>

    <!-- Formulario para iniciar sesión, envía la solicitud al controlador AuthController -->
    <form action="index.php?ctl=iniciarSesion" method="post">
        <!-- Campo de entrada para el alias (nombre de usuario) -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" 
                   required pattern="[A-Za-z0-9_]{4,20}" 
                   title="El alias debe tener entre 4 y 20 caracteres alfanuméricos.">
        </div>

        <!-- Campo de entrada para la contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" 
                   required minlength="6" 
                   title="La contraseña debe tener al menos 6 caracteres.">
        </div>

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

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
