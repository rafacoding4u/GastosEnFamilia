<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Usuarios</h2>

    <?php if (isset($usuarios) && count($usuarios) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Usuario</th>
                    <th>Nivel de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombreUsuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nivel_usuario']) ?></td>
                        <td>
                            <a href="index.php?ctl=editarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?ctl=eliminarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

