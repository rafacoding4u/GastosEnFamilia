<div class="container p-4">
    <h2>Gestión de Usuarios Administrados</h2>

    <a href="index.php?ctl=formCrearUsuarioAdmin" class="btn btn-success mb-3">Crear Usuario</a>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($params['mensaje']); ?></div>
    <?php endif; ?>

    <div class="table-responsive" style="overflow-x: auto; max-height: 500px;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Alias</th>
                    <th>Nivel de Usuario</th>
                    <th>Familias</th>
                    <th>Grupos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['usuarios'] as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['alias']) ?></td>
                        <td><?= htmlspecialchars($usuario['nivel_usuario']) ?></td>
                        <td><?= !empty($usuario['familias']) ? htmlspecialchars(implode(', ', explode(',', $usuario['familias']))) : 'Sin Familia' ?></td>
                        <td><?= !empty($usuario['grupos']) ? htmlspecialchars(implode(', ', explode(',', $usuario['grupos']))) : 'Sin Grupo' ?></td>
                        <td>
                            <div class="d-flex">
                                <?php if ($usuario['nivel_usuario'] === 'usuario'): ?>
                                    <a href="index.php?ctl=editarUsuarioAdmin&idUser=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning mr-2">Editar</a>
                                    <a href="index.php?ctl=eliminarUsuarioAdmin&idUser=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>No permitido</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>