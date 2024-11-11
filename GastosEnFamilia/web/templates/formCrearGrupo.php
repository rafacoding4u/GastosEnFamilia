<div class="container p-4">
    <h2>Crear Nuevos Grupos</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para crear varios grupos -->
        <form action="index.php?ctl=crearVariosGrupos" method="POST">
            <!-- Selección del número de grupos -->
            <div class="form-group">
                <label for="num_grupos">Número de Grupos a Crear</label>
                <select class="form-control" id="num_grupos" name="num_grupos" onchange="mostrarCamposGrupos()">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <!-- Contenedor donde se generarán los campos para cada grupo -->
            <div id="gruposContainer">
                <!-- Campos del primer grupo por defecto -->
                <h3>Grupo 1</h3>
                <div class="form-group">
                    <label for="nombre_grupo1">Nombre del Grupo</label>
                    <input type="text" class="form-control" id="nombre_grupo1" name="grupos[1][nombre]" required>
                </div>

                <div class="form-group">
                    <label for="password_grupo1">Contraseña del Grupo</label>
                    <input type="password" class="form-control" id="password_grupo1" name="grupos[1][password]" required>
                </div>

                <div class="form-group">
                    <label for="id_admin1">Seleccionar Administrador</label>
                    <select class="form-control" id="id_admin1" name="grupos[1][id_admin]" required>
                        <option value="">Seleccione un administrador</option>
                        <?php if (isset($administradores) && is_array($administradores)): ?>
                            <?php foreach ($administradores as $admin): ?>
                                <option value="<?= htmlspecialchars($admin['idUser']) ?>">
                                    <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay administradores disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Campo de búsqueda para grupos existentes -->
            <div class="form-group">
                <label for="buscar_grupo">Buscar Grupo Existente</label>
                <input type="text" id="buscar_grupo" class="form-control" placeholder="Buscar por descripción...">
                <select id="grupo_existente" name="grupo_existente" class="form-control">
                    <option value="">Seleccionar un grupo...</option>
                    <?php if (isset($grupos) && is_array($grupos)): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= htmlspecialchars($grupo['idGrupo'] ?? '') ?>">
                                <?= htmlspecialchars($grupo['nombre_grupo'] ?? 'Sin nombre') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option>No hay grupos disponibles.</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

            <!-- Mostrar posibles errores -->
            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensajes de éxito -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <!-- Botón para enviar el formulario -->
            <button type="submit" name="bCrearGrupos" class="btn btn-primary">Crear Grupos</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>

<script>
    function mostrarCamposGrupos() {
        const numGrupos = document.getElementById('num_grupos').value;
        const container = document.getElementById('gruposContainer');
        container.innerHTML = '';

        for (let i = 1; i <= numGrupos; i++) {
            const div = document.createElement('div');
            div.innerHTML = `
            <h3>Grupo ${i}</h3>
            <div class="form-group">
                <label for="nombre_grupo${i}">Nombre del Grupo</label>
                <input type="text" class="form-control" id="nombre_grupo${i}" name="grupos[${i}][nombre]" required>
            </div>

            <div class="form-group">
                <label for="password_grupo${i}">Contraseña del Grupo</label>
                <input type="password" class="form-control" id="password_grupo${i}" name="grupos[${i}][password]" required>
            </div>

            <div class="form-group">
                <label for="id_admin${i}">Seleccionar Administrador</label>
                <select class="form-control" id="id_admin${i}" name="grupos[${i}][id_admin]" required>
                    <option value="">Seleccione un administrador</option>
                    <?php if (isset($administradores) && is_array($administradores)): ?>
                        <?php foreach ($administradores as $admin): ?>
                            <option value="<?= htmlspecialchars($admin['idUser']) ?>">
                                <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay administradores disponibles</option>
                    <?php endif; ?>
                </select>
            </div>
        `;
            container.appendChild(div);
        }
    }

    // Filtrar grupos existentes
    document.getElementById('buscar_grupo').addEventListener('input', function() {
        var filter = this.value.toLowerCase();
        var options = document.getElementById('grupo_existente').options;
        for (var i = 0; i < options.length; i++) {
            var text = options[i].text.toLowerCase();
            options[i].style.display = text.includes(filter) ? '' : 'none';
        }
    });
</script>