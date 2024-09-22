<h1>Lista de Grupos</h1>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del Grupo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grupos as $grupo): ?>
            <tr>
                <td><?php echo htmlspecialchars($grupo['idGrupo']); ?></td>
                <td><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></td>
                <td>
                    <a href="index.php?ctl=editarGrupo&id=<?php echo $grupo['idGrupo']; ?>">Editar</a>
                    <a href="index.php?ctl=eliminarGrupo&id=<?php echo $grupo['idGrupo']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
