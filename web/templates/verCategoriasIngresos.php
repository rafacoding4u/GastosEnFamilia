<div class="container">
    <h2>Gestión de Categorías de Ingresos</h2>

    <!-- Mensaje informativo -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar nueva categoría de ingresos -->
    <form action="index.php?ctl=CategoriaController&action=insertarCategoriaIngreso" method="post">
        <div class="form-group">
            <label for="nombreCategoria">Nueva Categoría de Ingreso:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
        </div>
        <button type="submit" name="bInsertarCategoriaIngreso" class="btn btn-primary mt-3">Agregar Categoría</button>
    </form>

    <!-- Listado de categorías de ingresos existentes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($params['categorias'])): ?>
                <?php foreach ($params['categorias'] as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['nombreCategoria']); ?></td>
                        <td>
                            <!-- Comprobar si el usuario tiene permisos para editar o eliminar -->
                            <?php if ($_SESSION['nivel_usuario'] === 'superadmin' || ($_SESSION['nivel_usuario'] === 'admin' && $categoria['creado_por'] !== 'superadmin')): ?>
                                <a href="index.php?ctl=CategoriaController&action=editarCategoriaIngreso&id=<?= $categoria['idCategoria'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <?php if (!$categoria['enUso']): ?>
                                    <a href="index.php?ctl=CategoriaController&action=eliminarCategoriaIngreso&id=<?= $categoria['idCategoria'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
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
                    <td colspan="2">No hay categorías de ingresos registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
