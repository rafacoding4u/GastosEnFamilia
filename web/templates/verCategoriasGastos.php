<div class="container p-4">
    <h2>Gestión de Categorías de Gastos</h2>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($params['mensaje']); ?></div>
    <?php endif; ?>

    <!-- Formulario para agregar una nueva categoría de gasto -->
    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
        <div class="mb-4"> <!-- Añadido un margen inferior al contenedor -->
            <form action="index.php?ctl=insertarCategoriaGasto" method="post">
                <!-- Campo oculto para el token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="nombreCategoria">Nueva Categoría de Gasto:</label>
                    <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" required>
                </div>
                <button type="submit" name="bInsertarCategoriaGasto" class="btn btn-primary mt-3">Agregar Categoría</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Filtros de búsqueda -->
    <div class="mb-4">
        <form method="GET" action="index.php">
            <input type="hidden" name="ctl" value="verCategoriasGastos">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="idCategoria" class="form-control" placeholder="ID Categoría" value="<?= htmlspecialchars($_GET['idCategoria'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="nombreCategoria" class="form-control" placeholder="Nombre Categoría" value="<?= htmlspecialchars($_GET['nombreCategoria'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="creado_por_alias" class="form-control" placeholder="Alias Creador" value="<?= htmlspecialchars($_GET['creado_por_alias'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="creado_por_id" class="form-control" placeholder="ID Usuario Creador" value="<?= htmlspecialchars($_GET['creado_por_id'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="creado_por_rol" class="form-control" placeholder="Rol Creador" value="<?= htmlspecialchars($_GET['creado_por_rol'] ?? '') ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Opciones para alternar visibilidad de columnas -->
    <div class="mb-3">
        <button data-column="idCategoria" class="toggle-column btn btn-secondary">ID Categoría</button>
        <button data-column="creado_por_id" class="toggle-column btn btn-secondary">ID Usuario Creador</button>
    </div>

    <!-- Tabla con desplazamiento horizontal y vertical y cabecera fija -->
    <div class="table-responsive" style="overflow-x: auto; max-height: 500px;">
        <table class="table table-bordered">
            <thead class="sticky-header">
                <tr>
                    <th class="details-column-idCategoria d-none">ID Categoría</th>
                    <th>Nombre Categoría</th>
                    <th>Alias Creador</th>
                    <th>Rol Creador</th>
                    <th class="details-column-creado_por_id d-none">ID Usuario Creador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['categorias'] as $categoria): ?>
                    <tr>
                        <td class="details-column-idCategoria d-none"><?= htmlspecialchars($categoria['idCategoria']) ?></td>
                        <td><?= htmlspecialchars($categoria['nombreCategoria']) ?></td>
                        <td><?= htmlspecialchars($categoria['creado_por_alias']) ?></td>
                        <td><?= htmlspecialchars($categoria['creado_por_rol']) ?></td>
                        <td class="details-column-creado_por_id d-none"><?= htmlspecialchars($categoria['idUser']) ?></td>
                        <td>
                            <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                                <a href="index.php?ctl=editarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <?php if (!$categoria['enUso']): ?>
                                    <a href="index.php?ctl=eliminarCategoriaGasto&id=<?= htmlspecialchars($categoria['idCategoria']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')">Eliminar</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Categoría en uso</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
                        </td>
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