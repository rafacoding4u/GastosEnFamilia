<div class="container p-4">
    <h2>Crear Nuevas Familias</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para crear varias familias -->
        <form action="index.php?ctl=crearVariasFamilias" method="POST">
            <!-- Selección del número de familias -->
            <div class="form-group">
                <label for="num_familias">Número de Familias a Crear</label>
                <select class="form-control" id="num_familias" name="num_familias" onchange="mostrarCamposFamilias()">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <!-- Contenedor donde se generarán los campos para cada familia -->
            <div id="familiasContainer">
                <!-- Campos de la primera familia por defecto -->
                <h3>Familia 1</h3>
                <div class="form-group">
                    <label for="nombre_familia1">Nombre de la Familia</label>
                    <input type="text" class="form-control" id="nombre_familia1" name="familias[1][nombre]" required>
                </div>

                <div class="form-group">
                    <label for="password_familia1">Contraseña de la Familia</label>
                    <input type="password" class="form-control" id="password_familia1" name="familias[1][password]" required>
                </div>

                <div class="form-group">
                    <label for="id_admin1">Seleccionar Administrador</label>
                    <select class="form-control" id="id_admin1" name="familias[1][id_admin]" required>
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

            <!-- Campo de búsqueda para familias existentes -->
            <div class="form-group">
                <label for="buscar_familia">Buscar Familia Existente</label>
                <input type="text" id="buscar_familia" class="form-control" placeholder="Buscar por descripción...">
                <select id="familia_existente" name="familia_existente" class="form-control">
                    <option value="">Seleccionar una familia...</option>
                    <?php if (isset($familias) && is_array($familias)): ?>
                        <?php foreach ($familias as $familia): ?>
                            <option value="<?= isset($familia['idFamilia']) ? htmlspecialchars($familia['idFamilia']) : '' ?>">
                                <?= isset($familia['nombre_familia']) ? htmlspecialchars($familia['nombre_familia']) : 'No especificado' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option>No hay familias disponibles.</option>
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
            <button type="submit" name="bCrearFamilias" class="btn btn-primary">Crear Familias</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>

<script>
    function mostrarCamposFamilias() {
        const numFamilias = document.getElementById('num_familias').value;
        const container = document.getElementById('familiasContainer');
        container.innerHTML = '';

        for (let i = 1; i <= numFamilias; i++) {
            const div = document.createElement('div');
            div.innerHTML = `
            <h3>Familia ${i}</h3>
            <div class="form-group">
                <label for="nombre_familia${i}">Nombre de la Familia</label>
                <input type="text" class="form-control" id="nombre_familia${i}" name="familias[${i}][nombre]" required>
            </div>

            <div class="form-group">
                <label for="password_familia${i}">Contraseña de la Familia</label>
                <input type="password" class="form-control" id="password_familia${i}" name="familias[${i}][password]" required>
            </div>

            <div class="form-group">
                <label for="id_admin${i}">Seleccionar Administrador</label>
                <select class="form-control" id="id_admin${i}" name="familias[${i}][id_admin]" required>
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

    // Filtrar familias existentes
    document.getElementById('buscar_familia').addEventListener('input', function() {
        var filter = this.value.toLowerCase();
        var options = document.getElementById('familia_existente').options;
        for (var i = 0; i < options.length; i++) {
            var text = options[i].text.toLowerCase();
            options[i].style.display = text.includes(filter) ? '' : 'none';
        }
    });
</script>
