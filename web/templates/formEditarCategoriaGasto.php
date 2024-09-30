<div class="container p-4">
    <h2>Editar Categoría de Gasto</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

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

            <!-- Mostrar mensaje de éxito o error si existe -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info mt-3">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
