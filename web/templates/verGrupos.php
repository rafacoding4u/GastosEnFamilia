<div class="container p-4">
    <h2>Lista de Grupos</h2>

    <!-- Botón para añadir un nuevo grupo (solo visible para admin y superadmin) -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
        <a href="index.php?ctl=formCrearGrupo" class="btn btn-success mb-3">Añadir Grupo</a>
    <?php endif; ?>

    <!-- Mensaje informativo si está definido -->
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de grupos -->
    <?php if (!empty($grupos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grupo</th>
                    <th>Acciones</th> <!-- Columna para acciones -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $grupo): ?>
                    <tr>
                        <td><?= htmlspecialchars($grupo['idGrupo']) ?></td>
                        <td><?= htmlspecialchars($grupo['nombre_grupo']) ?></td>
                        <td>
                            <!-- Solo admins y superadmins pueden editar o eliminar -->
                            <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                                <a href="index.php?ctl=editarGrupo&id=<?= htmlspecialchars($grupo['idGrupo']); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarGrupo&id=<?= htmlspecialchars($grupo['idGrupo']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este grupo?');">Eliminar</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay grupos registrados.</p>
    <?php endif; ?>
</div>
