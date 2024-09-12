<?php include 'layout.php'; ?>

<div class="container text-center p-4">
    <h3>Iniciar Sesión</h3>

    <form action="index.php?ctl=iniciarSesion" method="post">
        <div class="form-group">
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <button type="submit" name="bIniciarSesion" class="btn btn-primary mt-3">Iniciar Sesión</button>

        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>


