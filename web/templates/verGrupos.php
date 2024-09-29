<div class="container p-4">
    <h2>Lista de Grupos</h2>

    <!-- Botón para añadir un nuevo grupo -->
    <a href="index.php?ctl=FamiliaGrupoController&action=formCrearGrupo" class="btn btn-success mb-3">Añadir Grupo</a>

    <!-- Mensaje informativo si está definido -->
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($grupos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grupo</th>
                    <th>Acciones</th> <!-- Nueva columna para acciones -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $grupo): ?>
                    <tr>
                        <td><?= htmlspecialchars($grupo['idGrupo']) ?></td>
                        <td><?= htmlspecialchars($grupo['nombre_grupo']) ?></td>
                        <td>
                            <a href="index.php?ctl=FamiliaGrupoController&action=editarGrupo&id=<?= htmlspecialchars($grupo['idGrupo']); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="index.php?ctl=FamiliaGrupoController&action=eliminarGrupo&id=<?= htmlspecialchars($grupo['idGrupo']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este grupo?');">Eliminar</a>
                        </td> <!-- Enlaces para editar y eliminar -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay grupos registrados.</p>
    <?php endif; ?>
</div>
