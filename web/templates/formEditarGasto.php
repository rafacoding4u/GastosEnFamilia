<div class="container p-4">
    <h2>Editar Gasto</h2>

    <!-- Verificar si el usuario tiene permisos -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'usuario' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para editar gasto -->
        <form action="index.php?ctl=actualizarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" method="POST">
            <!-- Campo para el concepto -->
            <div class="form-group">
                <label for="concepto">Concepto</label>
                <input type="text" class="form-control" name="concepto" value="<?= htmlspecialchars($gasto['concepto']) ?>" required>
            </div>

            <!-- Campo para el importe -->
            <div class="form-group">
                <label for="importe">Importe</label>
                <input type="number" step="0.01" class="form-control" name="importe" value="<?= htmlspecialchars($gasto['importe']) ?>" required>
            </div>

            <!-- Campo para la fecha -->
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" class="form-control" name="fecha" value="<?= htmlspecialchars($gasto['fecha']) ?>" required>
            </div>

            <!-- Selección de origen del gasto -->
            <div class="form-group">
                <label for="origen">Origen</label>
                <select class="form-control" name="origen" required>
                    <option value="banco" <?= $gasto['origen'] == 'banco' ? 'selected' : '' ?>>Banco</option>
                    <option value="efectivo" <?= $gasto['origen'] == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                </select>
            </div>

            <!-- Selección de categoría -->
            <div class="form-group">
                <label for="categoria">Categoría</label>
                <select class="form-control" name="categoria" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>" <?= $gasto['idCategoria'] == $categoria['idCategoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Token CSRF para protección -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

            <!-- Botón para guardar los cambios -->
            <button type="submit" name="bEditarGasto" class="btn btn-primary">Guardar Cambios</button>
        </form>

        <!-- Botón para cancelar y volver a la vista de gastos -->
        <a href="index.php?ctl=verGastos" class="btn btn-secondary mt-3">Cancelar</a>

    <?php else: ?>
        <!-- Mostrar mensaje de error si no tiene permisos -->
        <div class="alert alert-danger mt-3">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
