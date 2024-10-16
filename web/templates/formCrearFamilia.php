<div class="container p-4">
    <h2>Crear Nueva Familia</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para crear una nueva familia -->
        <form action="index.php?ctl=crearFamilia" method="POST">
            <!-- Nombre de la familia -->
            <div class="form-group">
                <label for="nombre_familia">Nombre de la Familia</label>
                <input type="text" class="form-control" id="nombre_familia" name="nombre_familia" required>
            </div>

            <!-- Contraseña de la familia -->
            <div class="form-group">
                <label for="password_familia">Contraseña de la Familia</label>
                <input type="password" class="form-control" id="password_familia" name="password_familia" required>
            </div>

            <!-- Selección de administrador -->
            <div class="form-group">
                <label for="id_admin">Seleccionar Administrador</label>
                <select class="form-control" id="id_admin" name="id_admin" required>
                    <option value="">Seleccione un administrador</option>
                    <!-- Verificar si la variable 'administradores' existe y es un array antes de usarla -->
                    <?php if (isset($administradores) && is_array($administradores)): ?>
                        <?php foreach ($administradores as $admin): ?>
                            <option value="<?= htmlspecialchars($admin['idUser']) ?>">
                                <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay administradores disponibles</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

            <!-- Mostrar posibles errores -->
            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensajes de éxito -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <!-- Botón para enviar el formulario -->
            <button type="submit" name="bCrearFamilia" class="btn btn-primary">Crear Familia</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
