<div class="container p-4">
    <h2>Editar Usuario</h2>

    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?ctl=actualizarUsuario&id=<?= htmlspecialchars($params['idUser']) ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($params['nombre']) ?>" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($params['apellido']) ?>" required>
        </div>

        <div class="form-group">
            <label for="alias">Alias</label>
            <input type="text" name="alias" class="form-control" value="<?= htmlspecialchars($params['alias']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($params['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($params['telefono']) ?>" required>
        </div>

        <!-- Selección de familia -->
        <div class="form-group">
            <label for="idFamilia">Familia</label>
            <select name="idFamilia" class="form-control">
                <option value="">Sin Familia</option>
                <?php foreach ($params['familias'] as $familia): ?>
                    <option value="<?= $familia['idFamilia'] ?>" <?= ($familia['idFamilia'] == $params['idFamilia']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($familia['nombre_familia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selección de grupo -->
        <div class="form-group">
            <label for="idGrupo">Grupo</label>
            <select name="idGrupo" class="form-control">
                <option value="">Sin Grupo</option>
                <?php foreach ($params['grupos'] as $grupo): ?>
                    <option value="<?= $grupo['idGrupo'] ?>" <?= ($grupo['idGrupo'] == $params['idGrupo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nivel de acceso del usuario -->
        <div class="form-group">
            <label for="nivel_usuario">Nivel de Usuario</label>
            <select name="nivel_usuario" class="form-control" id="nivel_usuario">
                <option value="usuario" <?= ($params['nivel_usuario'] == 'usuario') ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= ($params['nivel_usuario'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                <option value="superadmin" <?= ($params['nivel_usuario'] == 'superadmin') ? 'selected' : '' ?>>Superusuario</option>
            </select>
        </div>

        <button type="submit" name="bEditarUsuario" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
