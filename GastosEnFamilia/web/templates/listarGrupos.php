<div class="container p-4">
    <h2>Lista de Grupos</h2>

    <!-- Botón para añadir un nuevo grupo (visible para superadmin y admin) -->
    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
        <a href="index.php?ctl=formCrearGrupo" class="btn btn-success mb-3">Añadir Grupo</a>
    <?php endif; ?>

    <!-- Mostrar mensaje de feedback -->
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros de búsqueda -->
    <div class="mb-4">
        <form method="GET" action="index.php">
            <input type="hidden" name="ctl" value="listarGrupos">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="id" class="form-control" placeholder="ID" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="nombre_grupo" class="form-control" placeholder="Nombre del Grupo" value="<?= htmlspecialchars($_GET['nombre_grupo'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="administrador" class="form-control" placeholder="Administrador" value="<?= htmlspecialchars($_GET['administrador'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="usuario" class="form-control" placeholder="Usuario" value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Opciones para alternar visibilidad de columnas -->
    <div class="mb-3">
        <button data-column="id" class="toggle-column btn btn-secondary">ID</button>
    </div>

    <!-- Tabla de grupos con desplazamiento horizontal y vertical y cabecera fija -->
    <div class="table-responsive" style="overflow-x: auto; max-height: 500px;">
        <table class="table table-bordered">
            <thead class="sticky-header">
                <tr>
                    <!-- Columna de ID que estará oculta inicialmente -->
                    <th class="details-column-id d-none">ID</th>

                    <!-- Columnas visibles por defecto -->
                    <th>Nombre del Grupo</th>
                    <th>Administrador</th>
                    <th>Usuarios</th>

                    <!-- Columna de acciones para superadmin y admin -->
                    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $grupo): ?>
                    <tr>
                        <!-- Columna de ID oculta por defecto -->
                        <td class="details-column-id d-none"><?= htmlspecialchars($grupo['idGrupo']) ?></td>

                        <!-- Nombre del Grupo visible por defecto -->
                        <td><?= htmlspecialchars($grupo['nombre_grupo']) ?></td>

                        <!-- Columna de Administradores en formato vertical -->
                        <td>
                            <?php if (!empty($grupo['administradores'])): ?>
                                <?= nl2br(htmlspecialchars(str_replace(', ', "\n", $grupo['administradores']))) ?>
                            <?php else: ?>
                                Sin Administradores
                            <?php endif; ?>
                        </td>

                        <!-- Columna de Usuarios en formato vertical -->
                        <td>
                            <?php if (!empty($grupo['usuarios'])): ?>
                                <?= nl2br(htmlspecialchars(str_replace(', ', "\n", $grupo['usuarios']))) ?>
                            <?php else: ?>
                                Sin Usuarios
                            <?php endif; ?>
                        </td>

                        <!-- Acciones de Editar y Eliminar para superadmin y admin -->
                        <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                            <td>
                                <div class="d-flex">
                                    <a href="index.php?ctl=editarGrupo&id=<?= $grupo['idGrupo'] ?>" class="btn btn-warning btn-sm mr-2">Editar</a>
                                    <a href="index.php?ctl=eliminarGrupo&id=<?= $grupo['idGrupo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este grupo?')">Eliminar</a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript para alternar visibilidad de las columnas de detalles -->
<script>
    // Alternar visibilidad para columnas específicas
    document.querySelectorAll('.toggle-column').forEach(button => {
        button.addEventListener('click', function() {
            const columnClass = `.details-column-${this.getAttribute('data-column')}`;
            document.querySelectorAll(columnClass).forEach(column => column.classList.toggle('d-none'));
            this.classList.toggle('btn-secondary');
            this.classList.toggle('btn-primary');
        });
    });
</script>