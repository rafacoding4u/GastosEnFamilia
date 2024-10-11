<div class="container p-4">
    <h2>Editar Grupo</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para editar un grupo -->
        <form action="index.php?ctl=FamiliaGrupoController&action=editarGrupo&id=<?= htmlspecialchars($params['idGrupo']) ?>" method="post">
            <!-- Nombre del grupo -->
            <div class="form-group">
                <label for="nombre_grupo">Nombre del Grupo</label>
                <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" value="<?= htmlspecialchars($params['nombre_grupo']) ?>" required>
            </div>

            <!-- Asignar administrador del grupo -->
            <div class="form-group">
                <label for="id_admin">Seleccionar Administrador del Grupo</label>
                <select class="form-control" id="id_admin" name="id_admin" required>
                    <?php foreach ($params['administradores'] as $admin): ?>
                        <option value="<?= htmlspecialchars($admin['idUser']) ?>" <?= $params['idAdmin'] == $admin['idUser'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

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
            <button type="submit" name="bEditarGrupo" class="btn btn-primary">Actualizar Grupo</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
