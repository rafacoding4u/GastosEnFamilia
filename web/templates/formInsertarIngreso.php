<div class="container p-4">
    <h2>Añadir Ingreso</h2>

    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'usuario' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <form action="index.php?ctl=insertarIngreso" method="post">
            <!-- Campo para el concepto del ingreso -->
            <div class="form-group">
                <label for="concepto">Concepto:</label>
                <input type="text" id="concepto" name="concepto" class="form-control" required>
            </div>

            <!-- Campo para el importe del ingreso -->
            <div class="form-group">
                <label for="importe">Cantidad:</label>
                <input type="number" id="importe" name="importe" step="0.01" class="form-control" required>
            </div>

            <!-- Selector de categoría de ingreso -->
            <div class="form-group">
                <label for="idCategoria">Categoría:</label>
                <select id="idCategoria" name="idCategoria" class="form-control" required>
                    <option value="" disabled selected>Seleccione una categoría</option>
                    <?php foreach ($params['categorias'] as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
                            <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector para el origen del ingreso ('banco' o 'efectivo') -->
            <div class="form-group">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control" required>
                    <option value="banco">🏦 Banco</option>
                    <option value="efectivo">💵 Efectivo</option>
                </select>
            </div>

            <!-- Campo para la fecha del ingreso -->
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- Asignación a familia, grupo o individual -->
            <div class="form-group">
                <label for="asignacion">Asignar a:</label>
                <select id="asignacion" name="asignacion" class="form-control" required>
                    <option value="individual">Individual</option>
                    <?php if (!empty($params['familias'])): ?>
                        <?php foreach ($params['familias'] as $familia): ?>
                            <option value="familia_<?= htmlspecialchars($familia['idFamilia']) ?>">Familia: <?= htmlspecialchars($familia['nombre_familia']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($params['grupos'])): ?>
                        <?php foreach ($params['grupos'] as $grupo): ?>
                            <option value="grupo_<?= htmlspecialchars($grupo['idGrupo']) ?>">Grupo: <?= htmlspecialchars($grupo['nombre_grupo']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

            <button type="submit" name="bInsertarIngreso" class="btn btn-primary mt-3">Añadir Ingreso</button>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (isset($params['mensaje'])): ?>
                <div class="alert alert-danger mt-3">
                    <?= htmlspecialchars($params['mensaje']) ?>
                </div>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>