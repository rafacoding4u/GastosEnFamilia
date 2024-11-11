<div class="container p-4">
    <h2>Editar Grupo</h2>

    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <form action="index.php?ctl=editarGrupo&id=<?= htmlspecialchars($params['idGrupo'] ?? '') ?>" method="POST">
            <div class="form-group">
                <label for="nombre_grupo">Nombre del Grupo</label>
                <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" value="<?= htmlspecialchars($params['nombreGrupo'] ?? '') ?>" required>
            </div>

            <!-- Selección de múltiples Administradores -->
            <div class="form-group">
                <label for="idAdmin">Asignar Administradores al Grupo</label>
                <select name="idAdmin[]" id="idAdmin" class="form-control" multiple required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['idUser']) ?>" <?= in_array($usuario['idUser'], $params['administradoresAsignados']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selección de múltiples Usuarios -->
            <div class="form-group">
                <label for="usuarios">Asignar Usuarios al Grupo</label>
                <select name="usuarios[]" id="usuarios" class="form-control" multiple>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['idUser']) ?>" <?= in_array($usuario['idUser'], $params['usuariosAsignados']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                    <?= htmlspecialchars($mensaje ?? '') ?>
                </div>
            <?php endif; ?>

            <button type="submit" name="bEditarGrupo" class="btn btn-primary">Guardar cambios</button>
        </form>

    <?php else: ?>
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>