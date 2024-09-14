<?php include 'layout.php'; ?>

<div class="container p-4">
    <h3>Registro de Usuario</h3>

    <form action="index.php?ctl=registro" method="post">
        <!-- Campo para el nombre -->
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" 
                   value="<?= isset($params['nombre']) ? htmlspecialchars($params['nombre']) : '' ?>" required>
        </div>

        <!-- Campo para el apellido -->
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" 
                   value="<?= isset($params['apellido']) ? htmlspecialchars($params['apellido']) : '' ?>" required>
        </div>

        <!-- Campo para el alias (nombre de usuario) -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" 
                   value="<?= isset($params['alias']) ? htmlspecialchars($params['alias']) : '' ?>" required>
        </div>

        <!-- Campo para la contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <!-- Campo para la fecha de nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" 
                   value="<?= isset($params['fecha_nacimiento']) ? htmlspecialchars($params['fecha_nacimiento']) : '' ?>" required>
        </div>

        <!-- Campo para el correo electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= isset($params['email']) ? htmlspecialchars($params['email']) : '' ?>" required>
        </div>

        <!-- Campo para el teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control" 
                   value="<?= isset($params['telefono']) ? htmlspecialchars($params['telefono']) : '' ?>" required>
        </div>

        <!-- Selector de nivel de usuario -->
        <div class="form-group">
            <label for="nivel_usuario">Tipo de Usuario:</label>
            <select id="nivel_usuario" name="nivel_usuario" class="form-control" required>
                <option value="usuario" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <!-- Botón de envío -->
        <button type="submit" name="bRegistro" class="btn btn-primary mt-3">Registrarse</button>

        <!-- Mostrar errores de validación -->
        <?php if (isset($errores) && count($errores) > 0): ?>
            <div class="alert alert-danger mt-3">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito -->
        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>
