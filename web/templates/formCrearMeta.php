<div class="container p-4">
    <h2>Crear Meta Financiera</h2>

    <!-- Formulario para crear una meta financiera -->
    <form action="index.php?ctl=crearMeta" method="post">
        <!-- Campo para el nombre de la meta -->
        <div class="form-group">
            <label for="nombreMeta">Nombre de la Meta:</label>
            <input type="text" id="nombreMeta" name="nombreMeta" class="form-control" required>
        </div>

        <!-- Campo para la cantidad objetivo de la meta -->
        <div class="form-group">
            <label for="cantidadObjetivo">Cantidad Objetivo (€):</label>
            <input type="number" id="cantidadObjetivo" name="cantidadObjetivo" class="form-control" step="0.01" required>
        </div>

        <!-- Selector para la categoría asociada -->
        <div class="form-group">
            <label for="idCategoria">Categoría:</label>
            <select id="idCategoria" name="idCategoria" class="form-control" required>
                <option value="" disabled selected>Seleccionar Categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
                        <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Campo para seleccionar la fecha límite de la meta -->
        <div class="form-group">
            <label for="fechaLimite">Fecha Límite:</label>
            <input type="date" id="fechaLimite" name="fechaLimite" class="form-control" required>
        </div>

        <!-- Botón para crear la meta -->
        <button type="submit" name="bCrearMeta" class="btn btn-primary mt-3">Crear Meta</button>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
    </form>
</div>
