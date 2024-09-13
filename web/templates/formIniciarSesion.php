<?php include 'layout.php'; ?>

<div class="container text-center p-4">
    <h3>Iniciar Sesión</h3>

    <!-- Este formulario envía la solicitud de inicio de sesión al controlador correspondiente -->
    <form action="index.php?ctl=iniciarSesion" method="post">
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <!-- Se usa el campo 'alias', que corresponde con el campo 'alias' de la tabla 'usuarios' -->
            <input type="text" id="alias" name="alias" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <!-- 'contrasenya' debe ser verificada en el backend con password_hash o password_verify -->
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <button type="submit" name="bIniciarSesion" class="btn btn-primary mt-3">Iniciar Sesión</button>

        <!-- Este bloque muestra mensajes de error si los hay, como credenciales inválidas -->
        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>
