<?php include 'layout.php'; ?>

<div class="container p-4">
    <h3>Registro de Usuario</h3>

    <form action="index.php?ctl=registro" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= isset($params['nombre']) ? $params['nombre'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" value="<?= isset($params['apellido']) ? $params['apellido'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" value="<?= isset($params['alias']) ? $params['alias'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?= isset($params['fecha_nacimiento']) ? $params['fecha_nacimiento'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= isset($params['email']) ? $params['email'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control" value="<?= isset($params['telefono']) ? $params['telefono'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="nivel_usuario">Tipo de Usuario:</label>
            <select id="nivel_usuario" name="nivel_usuario" class="form-control" required>
                <option value="usuario" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" name="bRegistro" class="btn btn-primary mt-3">Registrarse</button>

        <?php if (isset($errores) && count($errores) > 0): ?>
            <div class="alert alert-danger mt-3">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>
