<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Añadir Ingreso</h2>

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

        <!-- Selector de categoría (extraído de la base de datos) -->
        <div class="form-group">
            <label for="idCategoria">Categoría:</label>
            <select id="idCategoria" name="idCategoria" class="form-control" required>
                <?php foreach ($categorias as $categoria): ?>
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
                <option value="banco">Banco</option>
                <option value="efectivo">Efectivo</option>
            </select>
        </div>

        <!-- Campo para la fecha del ingreso -->
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <!-- Botón para enviar el formulario -->
        <button type="submit" name="bInsertarIngreso" class="btn btn-primary mt-3">Añadir Ingreso</button>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>
