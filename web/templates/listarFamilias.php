<div class="container p-4">
    <h2>Lista de Familias</h2>

    <!-- Botón para añadir una nueva familia (solo visible para superadmin) -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
        <a href="index.php?ctl=formCrearFamilia" class="btn btn-success mb-3">Añadir Familia</a>
    <?php endif; ?>

    <!-- Mostrar mensaje de feedback -->
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de familias -->
    <?php if (!empty($familias)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Familia</th>
                    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($familias as $familia): ?>
                    <tr>
                        <td><?= htmlspecialchars($familia['idFamilia']) ?></td>
                        <td><?= htmlspecialchars($familia['nombre_familia']) ?></td>
                        <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                            <td>
                                <a href="index.php?ctl=editarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta familia?')">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay familias registradas.</p>
    <?php endif; ?>
</div>
