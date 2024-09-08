<?php ob_start(); ?>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if (isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
        <?php endif; ?>
    </div>
</div>

<div class="container text-center py-2">
    <h2>Lista de Usuarios</h2>
    <?php if (isset($params['usuarios']) && count($params['usuarios']) > 0) : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nombre de Usuario</th>
                    <th>Nivel de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['usuarios'] as $usuario) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></td>
                        <td><?php echo $usuario['nivel_usuario'] == 2 ? 'Administrador' : 'Usuario'; ?></td>
                        <td><a href="javascript:confirmDelete('index.php?ctl=eliminarUsuario&idUser=<?php echo $usuario['idUser']; ?>')">Eliminar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No hay usuarios que mostrar.</p>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>




