<div class="container p-4">
    <h2>Editar Familia</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para editar una familia -->
        <form action="index.php?ctl=editarFamilia&id=<?= htmlspecialchars($params['idFamilia'] ?? '') ?>" method="POST">
            <!-- Nombre de la familia -->
            <div class="form-group">
                <label for="nombre_familia">Nombre de la Familia</label>
                <input type="text" class="form-control" id="nombre_familia" name="nombre_familia" value="<?= htmlspecialchars($params['nombreFamilia'] ?? '') ?>" required>
            </div>

            <!-- Selección de Administrador -->
            <div class="form-group">
                <label for="idAdmin">Asignar Administrador a la Familia</label>
                <select name="idAdmin" id="idAdmin" class="form-control" required>
                    <option value="">Seleccione un administrador</option>
                    <?php if (!empty($usuarios) && is_array($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario['idUser']) ?>" <?= $usuario['idUser'] == $params['idAdmin'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay usuarios disponibles</option>
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
                    <?= htmlspecialchars($mensaje ?? '') ?>
                </div>
            <?php endif; ?>

            <!-- Botón para enviar el formulario -->
            <button type="submit" name="bEditarFamilia" class="btn btn-primary">Guardar cambios</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
