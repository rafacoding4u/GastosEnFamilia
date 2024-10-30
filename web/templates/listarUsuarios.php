<div class="container p-4">
    <!-- Botón para crear un nuevo usuario (visible para superadmin y admin) -->
    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
        <a href="index.php?ctl=formCrearUsuario" class="btn btn-primary mb-3">Crear Usuario</a>
    <?php endif; ?>

    <h2>Lista de Usuarios Registrados</h2>

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
                    <!-- Columna de Acciones para superadmin y admin -->
                    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
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
                            <?php
                            $nombreFamilia = $usuario['nombre_familia'] ?? 'Sin Familia';
                            $nombreGrupo = $usuario['nombre_grupo'] ?? 'Sin Grupo';
                            if ($nombreFamilia === 'Sin Familia' && $nombreGrupo === 'Sin Grupo') {
                                echo "Individual";
                            } elseif ($nombreFamilia !== 'Sin Familia' && $nombreGrupo !== 'Sin Grupo') {
                                echo "Familiar y en Grupo";
                            } elseif ($nombreFamilia !== 'Sin Familia') {
                                echo "Familiar";
                            } elseif ($nombreGrupo !== 'Sin Grupo') {
                                echo "En Grupo";
                            }
                            ?>
                        </td>
                        <!-- Acciones de Editar y Eliminar según permisos de superadmin y admin -->
                        <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                            <td>
                                <div class="d-flex">
                                    <?php
                                    $esGestionado = $usuario['gestionado'] ?? true;
                                    if ($esGestionado || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                                        <a href="index.php?ctl=actualizarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning mr-2">Editar</a>
                                        <?php if ($usuario['idUser'] !== $_SESSION['usuario']['id']): ?>
                                            <a href="index.php?ctl=eliminarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>Acción no permitida</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</div>