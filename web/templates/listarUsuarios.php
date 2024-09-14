<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Usuarios</h2>

    <!-- Verificar si hay un mensaje de éxito o error -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Verificar si hay usuarios para mostrar -->
    <?php if (isset($usuarios) && count($usuarios) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Alias</th> <!-- Mostrar el alias de usuario -->
                    <th>Nivel de Usuario</th> <!-- Nivel de acceso (usuario, admin, superadmin) -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['alias']) ?></td> <!-- Ajustado a "alias" -->
                        <td><?= htmlspecialchars($usuario['nivel_usuario']) ?></td>
                        <td>
                            <!-- Botón para editar el usuario -->
                            <a href="index.php?ctl=editarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning">Editar</a>
                            <!-- Botón para eliminar el usuario -->
                            <a href="index.php?ctl=eliminarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
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
