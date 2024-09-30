<div class="container p-4">
    <h2>Añadir Ingreso</h2>

    <!-- Verificación de permisos para mostrar el formulario solo a usuarios autorizados -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'usuario' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para insertar ingreso -->
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

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

            <!-- Botón para enviar el formulario -->
            <button type="submit" name="bInsertarIngreso" class="btn btn-primary mt-3">Añadir Ingreso</button>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (isset($params['mensaje'])): ?>
                <div class="alert alert-danger mt-3">
                    <?= htmlspecialchars($params['mensaje']) ?>
                </div>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <!-- Mostrar mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
