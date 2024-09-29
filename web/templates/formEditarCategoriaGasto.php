<div class="container p-4">
    <h2>Editar Categoría de Gasto</h2>

    <!-- Formulario para editar categoría de gastos -->
    <form action="index.php?ctl=CategoriaController&action=actualizarCategoriaGasto" method="post">
        <!-- Campo oculto para pasar el ID de la categoría -->
        <input type="hidden" name="idCategoria" value="<?= htmlspecialchars($categoria['idCategoria']) ?>">

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

        <!-- Campo para el nombre de la categoría -->
        <div class="form-group">
            <label for="nombreCategoria">Nombre de la categoría:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" value="<?= htmlspecialchars($categoria['nombreCategoria']) ?>" required>
        </div>

        <!-- Botón para guardar los cambios -->
        <button type="submit" name="bEditarCategoriaGasto" class="btn btn-primary mt-3">Guardar Cambios</button>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
    </form>
</div>
