<div class="container p-4">
    <h2>Editar Categoría de Gasto</h2>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($params['mensaje']); ?></div>
    <?php endif; ?>

    <form action="index.php?ctl=editarCategoriaGasto" method="post">
        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $params['csrf_token']; ?>">
        <!-- Campo oculto para ID de la categoría -->
        <input type="hidden" name="idCategoria" value="<?= htmlspecialchars($params['categoria']['idCategoria']); ?>">

        <div class="form-group">
            <label for="nombreCategoria">Nombre de la Categoría:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" value="<?= htmlspecialchars($params['categoria']['nombreCategoria']); ?>" required>
        </div>

        <button type="submit" name="bEditarCategoriaGasto" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
