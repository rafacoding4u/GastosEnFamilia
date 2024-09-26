<div class="container">
    <h2>Gestión de Categorías de Gastos</h2>

    <!-- Mensaje informativo -->
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar nueva categoría de gastos -->
    <form action="index.php?ctl=insertarCategoriaGasto" method="post">
        <div class="form-group">
            <label for="nombreCategoria">Nueva Categoría de Gasto:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
        </div>
        <button type="submit" name="bInsertarCategoriaGasto" class="btn btn-primary mt-3">Agregar Categoría</button>
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
                            <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
                                <!-- Solo admins y superadmins pueden editar/eliminar -->
                                <a href="index.php?ctl=editarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <?php if (!$categoria['enUso']): ?>
                                    <a href="index.php?ctl=eliminarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Categoría en uso</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
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
