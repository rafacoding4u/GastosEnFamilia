<div class="container p-4">
    <h2>Lista de Familias</h2>

    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
        <a href="index.php?ctl=formCrearFamilia" class="btn btn-success mb-3">Añadir Familia</a>
    <?php endif; ?>

    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros de búsqueda -->
    <div class="mb-4">
        <form method="GET" action="index.php">
            <input type="hidden" name="ctl" value="listarFamilias">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="id" class="form-control" placeholder="ID" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="nombre_familia" class="form-control" placeholder="Nombre de la Familia" value="<?= htmlspecialchars($_GET['nombre_familia'] ?? '') ?>">
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

    <!-- Tabla de familias con cabecera fija y desplazamiento horizontal y vertical -->
    <div class="table-responsive" style="overflow-x: auto; max-height: 500px;">
        <table class="table table-bordered">
            <thead class="sticky-header">
                <tr>
                    <th class="details-column-id d-none">ID</th>
                    <th>Nombre de la Familia</th>
                    <th>Administrador</th>
                    <th>Usuarios</th>
                    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($familias as $familia): ?>
                    <tr>
                        <td class="details-column-id d-none"><?= htmlspecialchars($familia['idFamilia']) ?></td>
                        <td><?= htmlspecialchars($familia['nombre_familia']) ?></td>

                        <!-- Administradores en formato vertical -->
                        <td>
                            <?php if (!empty($familia['administradores'])): ?>
                                <?= nl2br(htmlspecialchars($familia['administradores'])) ?>
                            <?php else: ?>
                                Sin Administradores
                            <?php endif; ?>
                        </td>

                        <!-- Usuarios en formato vertical -->
                        <td>
                            <?php if (!empty($familia['usuarios'])): ?>
                                <?= nl2br(htmlspecialchars($familia['usuarios'])) ?>
                            <?php else: ?>
                                Sin Usuarios
                            <?php endif; ?>
                        </td>

                        <!-- Acciones de Editar y Eliminar para superadmin y admin -->
                        <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                            <td>
                                <div class="d-flex">
                                    <a href="index.php?ctl=editarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-warning btn-sm mr-2">Editar</a>
                                    <a href="index.php?ctl=eliminarFamilia&id=<?= $familia['idFamilia'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta familia?')">Eliminar</a>
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
    document.querySelectorAll('.toggle-column').forEach(button => {
        button.addEventListener('click', function() {
            const columnClass = `.details-column-${this.getAttribute('data-column')}`;
            document.querySelectorAll(columnClass).forEach(column => column.classList.toggle('d-none'));
            this.classList.toggle('btn-secondary');
            this.classList.toggle('btn-primary');
        });
    });
</script>