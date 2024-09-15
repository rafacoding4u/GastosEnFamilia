<?php ob_start(); ?>
<?php include 'layout.php'; ?>

<div class="container">
    <h2>Gestión de Categorías de Ingresos</h2>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar nueva categoría de ingresos -->
    <form action="index.php?ctl=insertarCategoriaIngreso" method="post" class="mb-3">
        <div class="form-group">
            <label for="nombreCategoria">Nombre de la nueva categoría:</label>
            <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
        </div>
        <button type="submit" name="bInsertarCategoriaIngreso" class="btn btn-primary">Agregar Categoría</button>
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
                            <a href="index.php?ctl=editarCategoriaIngreso&id=<?= $categoria['idCategoria'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="index.php?ctl=eliminarCategoriaIngreso&id=<?= $categoria['idCategoria'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
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

<?php include 'footer.php'; ?>
<?php $contenido = ob_get_clean(); ?>
<?php include 'layout.php'; ?>
