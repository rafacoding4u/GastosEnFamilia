<div class="container p-4">
    <h2>Crear Nuevo Grupo</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para crear un nuevo grupo -->
        <form action="index.php?ctl=crearGrupo" method="post">

            <!-- Nombre del grupo -->
            <div class="form-group">
                <label for="nombre_grupo">Nombre del Grupo</label>
                <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" required>
            </div>

            <!-- Contraseña del grupo -->
            <div class="form-group">
                <label for="password_grupo">Contraseña del Grupo</label>
                <input type="password" class="form-control" id="password_grupo" name="password_grupo" required>
            </div>

            <!-- Mostrar administradores si existen -->
            <?php if (isset($params['administradores']) && is_array($params['administradores'])): ?>
                <div class="form-group">
                    <label for="id_admin">Seleccionar Administrador</label>
                    <select class="form-control" id="id_admin" name="id_admin" required>
                        <?php foreach ($params['administradores'] as $admin): ?>
                            <option value="<?= htmlspecialchars($admin['idUser']) ?>">
                                <?= htmlspecialchars($admin['nombre']) . ' ' . htmlspecialchars($admin['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    No se encontraron administradores disponibles.
                </div>
            <?php endif; ?>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= isset($params['csrf_token']) ? htmlspecialchars($params['csrf_token']) : '' ?>">

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
            <button type="submit" name="bCrearGrupo" class="btn btn-primary">Crear Grupo</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
