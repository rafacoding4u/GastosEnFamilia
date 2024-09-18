<?php include 'layout.php'; ?>

<div class="container p-4">
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
                    <th>Alias</th> <!-- Mostrar el alias de usuario -->
                    <th>Email</th> <!-- Mostrar el correo del usuario -->
                    <th>Nivel de Usuario</th> <!-- Mostrar el nivel de usuario (usuario, admin, superadmin) -->
                    <th>Familia</th> <!-- Mostrar el nombre de la familia a la que pertenece el usuario -->
                    <th>Grupo</th> <!-- Mostrar el nombre del grupo a la que pertenece el usuario -->
                    <th>Tipo de Usuario</th> <!-- Mostrar si es individual, pertenece a familia o grupo -->
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
                        <td><?= htmlspecialchars($usuario['nombre_familia'] ?? 'Sin Familia') ?></td> <!-- Familia o "Sin Familia" -->
                        <td><?= htmlspecialchars($usuario['nombre_grupo'] ?? 'Sin Grupo') ?></td> <!-- Grupo o "Sin Grupo" -->
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
                                <a href="index.php?ctl=editarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning mr-2">Editar</a>
                                <!-- Botón para eliminar el usuario -->
                                <a href="index.php?ctl=eliminarUsuario&id=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
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

<?php include 'footer.php'; ?>