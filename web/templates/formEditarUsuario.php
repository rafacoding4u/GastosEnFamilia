<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Editar Usuario</h2>

    <!-- Mostrar errores si existen -->
    <?php if (isset($errores) && count($errores) > 0): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Mostrar mensaje informativo -->
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para editar el usuario -->
    <form action="index.php?ctl=editarUsuario&id=<?= htmlspecialchars($idUser) ?>" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($apellido) ?>" required>
        </div>

        <div class="form-group">
            <label for="alias">Alias:</label>
            <input type="text" class="form-control" id="alias" name="alias" value="<?= htmlspecialchars($alias) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>" required>
        </div>

        <div class="form-group">
            <label for="nivel_usuario">Nivel de Usuario:</label>
            <select class="form-control" id="nivel_usuario" name="nivel_usuario" required>
                <option value="usuario" <?= $nivel_usuario == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= $nivel_usuario == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="superadmin" <?= $nivel_usuario == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
            </select>
        </div>

        <button type="submit" name="bEditarUsuario" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php include 'footer.php'; ?>
