<div class="container p-4">
    <h2>Editar Usuario</h2>

    <!-- Verificación de permisos para mostrar el formulario solo a admins y superadmins -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($params['mensaje']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?ctl=UsuarioController&action=actualizarUsuario&id=<?= htmlspecialchars($params['idUser']) ?>" method="POST">
            <!-- Nombre -->
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($params['nombre']) ?>" required>
            </div>

            <!-- Apellido -->
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($params['apellido']) ?>" required>
            </div>

            <!-- Alias -->
            <div class="form-group">
                <label for="alias">Alias</label>
                <input type="text" name="alias" class="form-control" value="<?= htmlspecialchars($params['alias']) ?>" required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($params['email']) ?>" required>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($params['telefono']) ?>" required>
            </div>

            <!-- Familia -->
            <div class="form-group">
                <label for="idFamilia">Familia</label>
                <select name="idFamilia" class="form-control">
                    <option value="">Sin Familia</option>
                    <?php foreach ($params['familias'] as $familia): ?>
                        <option value="<?= htmlspecialchars($familia['idFamilia']) ?>" <?= ($familia['idFamilia'] == $params['idFamilia']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($familia['nombre_familia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Grupo -->
            <div class="form-group">
                <label for="idGrupo">Grupo</label>
                <select name="idGrupo" class="form-control">
                    <option value="">Sin Grupo</option>
                    <?php foreach ($params['grupos'] as $grupo): ?>
                        <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>" <?= ($grupo['idGrupo'] == $params['idGrupo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nivel de usuario -->
            <div class="form-group">
                <label for="nivel_usuario">Nivel de Usuario</label>
                <select name="nivel_usuario" class="form-control" required>
                    <option value="usuario" <?= ($params['nivel_usuario'] == 'usuario') ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= ($params['nivel_usuario'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                    <option value="superadmin" <?= ($params['nivel_usuario'] == 'superadmin') ? 'selected' : '' ?>>Superusuario</option>
                </select>
            </div>

            <!-- Token CSRF para la seguridad -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

            <!-- Botón para guardar los cambios -->
            <button type="submit" name="bEditarUsuario" class="btn btn-primary">Guardar Cambios</button>
        </form>

    <?php else: ?>
        <!-- Mostrar mensaje de error si no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
