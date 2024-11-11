<div class="container p-4">
    <h2>Editar Usuario</h2>

    <?php if (isset($_SESSION['usuario']) && ($_SESSION['usuario']['nivel_usuario'] === 'superadmin' || $_SESSION['usuario']['nivel_usuario'] === 'admin')): ?>

        <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($params['mensaje']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($params['errores']) && !empty($params['errores'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($params['errores'] as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?ctl=actualizarUsuario&idUser=<?= htmlspecialchars($params['usuario']['idUser'] ?? '') ?>" method="post">
            <input type="hidden" name="idUser" value="<?= htmlspecialchars($params['usuario']['idUser'] ?? '') ?>">

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($params['usuario']['nombre'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($params['usuario']['apellido'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="alias">Alias</label>
                <input type="text" name="alias" class="form-control" value="<?= htmlspecialchars($params['usuario']['alias'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($params['usuario']['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono (Opcional)</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($params['usuario']['telefono'] ?? '') ?>" pattern="\d{9}" title="El teléfono debe tener 9 dígitos">
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento (Opcional)</label>
                <input type="text" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($params['usuario']['fecha_nacimiento'] ?? '') ?>" pattern="\d{2}/\d{2}/\d{4}" title="La fecha debe tener el formato dd/mm/yyyy">
            </div>

            <!-- Selección de familias -->
            <div class="form-group">
                <label for="idFamilia">Familia(s)</label>
                <select name="idFamilia[]" id="idFamilia" class="form-control" multiple>
                    <option value="">Sin Familia</option>
                    <?php if (isset($params['familias'])): ?>
                        <?php foreach ($params['familias'] as $familia): ?>
                            <option value="<?= htmlspecialchars($familia['idFamilia']) ?>" <?= (isset($params['usuario']['idFamilia']) && in_array($familia['idFamilia'], (array) $params['usuario']['idFamilia'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($familia['nombre_familia']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Selección de grupos -->
            <div class="form-group">
                <label for="idGrupo">Grupo(s)</label>
                <select name="idGrupo[]" id="idGrupo" class="form-control" multiple>
                    <option value="">Sin Grupo</option>
                    <?php if (isset($params['grupos'])): ?>
                        <?php foreach ($params['grupos'] as $grupo): ?>
                            <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>" <?= (isset($params['usuario']['idGrupo']) && in_array($grupo['idGrupo'], (array) $params['usuario']['idGrupo'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Nivel de Usuario -->
            <div class="form-group">
                <label for="nivel_usuario">Nivel de Usuario</label>
                <select name="nivel_usuario" class="form-control" required <?= ($params['usuario']['idUser'] ?? null) === ($_SESSION['usuario']['id'] ?? null) ? 'disabled' : '' ?>>
                    <option value="usuario" <?= (isset($params['usuario']['nivel_usuario']) && $params['usuario']['nivel_usuario'] == 'usuario') ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= (isset($params['usuario']['nivel_usuario']) && $params['usuario']['nivel_usuario'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                        <option value="superadmin" <?= (isset($params['usuario']['nivel_usuario']) && $params['usuario']['nivel_usuario'] == 'superadmin') ? 'selected' : '' ?>>Superusuario</option>
                    <?php endif; ?>
                </select>
            </div>

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <button type="submit" name="bActualizarUsuario" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php?ctl=listarUsuarios" class="btn btn-secondary">Cancelar</a>
        </form>

    <?php else: ?>
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>