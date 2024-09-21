<div class="container">
    <h2>Gestión de Categorías de Gastos</h2>

    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar nueva categoría de gastos -->
    <form action="index.php?ctl=insertarCategoriaGasto" method="post" class="mb-3">
        <div class="form-group">
            <label for="nombreCategoria">Nombre de la nueva categoría:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
        </div>
        <button type="submit" name="bInsertarCategoriaGasto" class="btn btn-primary">Agregar Categoría</button>
    </form>

    <!-- Listado de categorías de gastos existentes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['nombreCategoria']); ?></td>
                        <td>
                            <a href="index.php?ctl=editarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="index.php?ctl=eliminarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No hay categorías de gastos registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
