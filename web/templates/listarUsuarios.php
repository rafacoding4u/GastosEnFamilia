<div class="container p-4">
    <h2>Lista de Usuarios Registrados</h2>
    <?php if ($_SESSION['nivel_usuario'] === 'superadmin'): ?>
        <a href="index.php?ctl=UsuarioController&action=formCrearUsuario" class="btn btn-success">Crear Usuario</a>
    <?php endif; ?>

    <!-- Mostrar mensaje en caso de éxito o error -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Verificar si existen usuarios para mostrar -->
    <?php if (isset($params['usuarios']) && count($params['usuarios']) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Alias</th>
                    <th>Email</th>
                    <th>Nivel de Usuario</th>
                    <th>Familia</th>
                    <th>Grupo</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['usuarios'] as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['alias']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['nivel_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_familia'] ?? 'Sin Familia') ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_grupo'] ?? 'Sin Grupo') ?></td>
                        <td>
                            <?php if ($usuario['idFamilia'] == null && $usuario['idGrupo'] == null): ?>
                                Individual
                            <?php elseif ($usuario['idFamilia'] != null): ?>
                                Familiar
                            <?php elseif ($usuario['idGrupo'] != null): ?>
                                En Grupo
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex">
                                <!-- Botón para editar el usuario -->
                                <a href="index.php?ctl=UsuarioController&action=editarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning mr-2">Editar</a>
                                <!-- Botón para eliminar el usuario -->
                                <a href="index.php?ctl=UsuarioController&action=eliminarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</div>
