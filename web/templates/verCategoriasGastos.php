<div class="container">
    <h2>Gestión de Categorías de Gastos</h2>

    <!-- Mensaje informativo -->
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Mostrar el formulario para agregar una nueva categoría solo si es admin o superadmin -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
        <form action="index.php?ctl=insertarCategoriaGasto" method="post">
            <div class="form-group">
                <label for="nombreCategoria">Nueva Categoría de Gasto:</label>
                <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
            </div>
            <button type="submit" name="bInsertarCategoriaGasto" class="btn btn-primary mt-3">Agregar Categoría</button>
        </form>
    <?php endif; ?>

    <!-- Listado de categorías de gastos existentes -->
    <table class="table table-striped mt-4">
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
                            <!-- Verificar si el usuario es admin o superadmin para mostrar las opciones de edición/eliminación -->
                            <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                                <a href="index.php?ctl=editarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-warning btn-sm">Editar</a>

                                <!-- Mostrar "Categoría en uso" o permitir eliminar -->
                                <?php if (!$categoria['enUso']): ?>
                                    <a href="index.php?ctl=eliminarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')">Eliminar</a>
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
