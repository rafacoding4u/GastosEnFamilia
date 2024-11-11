<div class="container p-4">
    <h2>Lista de Usuarios Registrados</h2>

    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
        <a href="index.php?ctl=formCrearUsuario" class="btn btn-success mb-3">Crear Usuario</a>
    <?php endif; ?>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($params['mensaje']); ?></div>
    <?php endif; ?>

    <!-- Filtros de búsqueda -->
    <div class="mb-4">
        <form method="GET" action="index.php">
            <input type="hidden" name="ctl" value="listarUsuarios">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="apellido" class="form-control" placeholder="Apellido" value="<?= htmlspecialchars($_GET['apellido'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="text" name="alias" class="form-control" placeholder="Alias" value="<?= htmlspecialchars($_GET['alias'] ?? '') ?>">
                </div>
                <div class="col">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                </div>
                <div class="col">
                    <select name="nivel_usuario" class="form-control">
                        <option value="">Rol de Usuario</option>
                        <option value="usuario" <?= (isset($_GET['nivel_usuario']) && $_GET['nivel_usuario'] === 'usuario') ? 'selected' : '' ?>>Usuario</option>
                        <option value="admin" <?= (isset($_GET['nivel_usuario']) && $_GET['nivel_usuario'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                        <option value="superadmin" <?= (isset($_GET['nivel_usuario']) && $_GET['nivel_usuario'] === 'superadmin') ? 'selected' : '' ?>>Superadministrador</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Botones para alternar visibilidad de columnas de detalles -->
    <div class="mb-3">
        <button data-column="nombre" class="toggle-column btn btn-secondary">Nombre</button>
        <button data-column="apellido" class="toggle-column btn btn-secondary">Apellido</button>
        <button data-column="email" class="toggle-column btn btn-secondary">Email</button>
    </div>

    <!-- Tabla con desplazamiento horizontal y vertical -->
    <div class="table-responsive" style="overflow-x: auto; max-height: 500px;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- Columnas de detalles que estarán ocultas inicialmente -->
                    <th class="details-column-nombre d-none">Nombre</th>
                    <th class="details-column-apellido d-none">Apellido</th>
                    <th class="details-column-email d-none">Email</th>

                    <!-- Columnas visibles por defecto -->
                    <th>Alias</th>
                    <th>Nivel de Usuario</th>
                    <th>Familias</th>
                    <th>Grupos</th>
                    <th>Tipo de Usuario</th>
                    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['usuarios'] as $usuario): ?>
                    <tr>
                        <!-- Columnas de detalles (ocultas por defecto) -->
                        <td class="details-column-nombre d-none"><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td class="details-column-apellido d-none"><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td class="details-column-email d-none"><?= htmlspecialchars($usuario['email']) ?></td>

                        <!-- Columnas visibles por defecto -->
                        <td><?= htmlspecialchars($usuario['alias']) ?></td>
                        <td><?= htmlspecialchars($usuario['nivel_usuario']) ?></td>

                        <!-- Familias y Grupos en formato de texto -->
                        <td><?= !empty($usuario['familias']) ? htmlspecialchars(implode(', ', explode(',', $usuario['familias']))) : 'Sin Familia' ?></td>
                        <td><?= !empty($usuario['grupos']) ? htmlspecialchars(implode(', ', explode(',', $usuario['grupos']))) : 'Sin Grupo' ?></td>

                        <!-- Tipo de usuario -->
                        <td>
                            <?php
                            if (empty($usuario['familias']) && empty($usuario['grupos'])) {
                                echo "Individual";
                            } elseif (!empty($usuario['familias']) && !empty($usuario['grupos'])) {
                                echo "Familiar y en Grupo";
                            } elseif (!empty($usuario['familias'])) {
                                echo "Familiar";
                            } elseif (!empty($usuario['grupos'])) {
                                echo "En Grupo";
                            }
                            ?>
                        </td>

                        <!-- Acciones para usuarios con permisos -->
                        <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])): ?>
                            <td>
                                <div class="d-flex">
                                    <a href="index.php?ctl=editarUsuario&idUser=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-warning mr-2">Editar</a>
                                    <?php if ($usuario['idUser'] !== $_SESSION['usuario']['id']): ?>
                                        <a href="index.php?ctl=eliminarUsuario&idUser=<?= htmlspecialchars($usuario['idUser']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
                                    <?php endif; ?>
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

            // Cambiar estado del botón
            this.classList.toggle('btn-secondary');
            this.classList.toggle('btn-primary');
        });
    });
</script>