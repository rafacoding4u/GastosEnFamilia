<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Familias</h2>

    <!-- Botón para añadir una nueva familia -->
    <a href="index.php?ctl=formCrearFamilia" class="btn btn-success mb-3">Añadir Familia</a>

    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($familias)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Familia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($familias as $familia): ?>
                    <tr>
                        <td><?= htmlspecialchars($familia['idFamilia']) ?></td>
                        <td><?= htmlspecialchars($familia['nombre_familia']) ?></td>
                        <td>
                            <a href="index.php?ctl=editarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="index.php?ctl=eliminarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta familia?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay familias registradas.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
