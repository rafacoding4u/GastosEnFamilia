<div class="container p-4">
    <h2>Editar Categoría de Ingreso</h2>

    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
        <form action="index.php?ctl=editarCategoriaIngreso" method="post">
            <input type="hidden" name="idCategoria" value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="form-group">
                <label for="nombreCategoria">Nombre de la categoría:</label>
                <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" value="<?= htmlspecialchars($categoria['nombreCategoria']) ?>" required>
            </div>

            <button type="submit" name="bEditarCategoriaIngreso" class="btn btn-primary mt-3">Guardar Cambios</button>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info mt-3">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>