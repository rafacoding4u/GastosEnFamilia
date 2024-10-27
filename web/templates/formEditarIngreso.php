<div class="container p-4">
    <h2>Editar Ingreso</h2>

    <!-- Verificación de permisos para mostrar el formulario solo a usuarios autorizados -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'usuario' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para editar ingreso -->
        <form action="index.php?ctl=editarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" method="post">
            <!-- Campo para el concepto del ingreso -->
            <div class="form-group">
                <label for="concepto">Concepto:</label>
                <input type="text" id="concepto" name="concepto" class="form-control" value="<?= htmlspecialchars($ingreso['concepto']) ?>" required>
            </div>

            <!-- Campo para el importe del ingreso -->
            <div class="form-group">
                <label for="importe">Cantidad:</label>
                <input type="number" id="importe" name="importe" step="0.01" class="form-control" value="<?= htmlspecialchars($ingreso['importe']) ?>" required>
            </div>

            <!-- Selector de categoría de ingreso -->
            <div class="form-group">
                <label for="idCategoria">Categoría:</label>
                <select id="idCategoria" name="idCategoria" class="form-control" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>" <?= ($ingreso['idCategoria'] == $categoria['idCategoria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector para el origen del ingreso ('banco' o 'efectivo') -->
            <div class="form-group">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control" required>
                    <option value="banco" <?= ($ingreso['origen'] == 'banco') ? 'selected' : '' ?>>🏦 Banco</option>
                    <option value="efectivo" <?= ($ingreso['origen'] == 'efectivo') ? 'selected' : '' ?>>💵 Efectivo</option>
                </select>
            </div>

            <!-- Campo para la fecha del ingreso -->
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <!-- Aquí se asegura que la fecha está en el formato correcto para el campo de fecha -->
                <input type="date" id="fecha" name="fecha" class="form-control" value="<?= htmlspecialchars(substr($ingreso['fecha'], 0, 10)) ?>" required>
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

            <!-- Botón para enviar el formulario -->
            <button type="submit" name="bEditarIngreso" class="btn btn-primary mt-3">Guardar Cambios</button>

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
