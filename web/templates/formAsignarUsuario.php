<div class="container p-4">
    <h2>Asignar Usuario a Familias o Grupos</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para asignar usuario -->
        <form method="POST" action="index.php?ctl=asignarUsuarioFamiliaGrupo">

            <!-- Selección de usuario -->
            <div class="form-group">
                <label for="idUsuario">Selecciona un Usuario:</label>
                <select name="idUsuario" id="idUsuario" class="form-control" required>
                    <?php if (empty($usuarios)): ?>
                        <option disabled>No hay usuarios disponibles</option>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario['idUser']) ?>">
                                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Selección de rol del usuario -->
            <div class="form-group">
                <label for="rolUsuario">Selecciona el Rol del Usuario:</label>
                <select name="rolUsuario" id="rolUsuario" class="form-control" required>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                    <option value="usuario">Usuario Regular</option>
                </select>
            </div>

            <!-- Creación de nuevas familias o grupos -->
            <div class="form-group">
                <label for="numFamilias">Número de Familias a Crear:</label>
                <input type="number" name="numFamilias" id="numFamilias" class="form-control" min="0" value="0" onchange="renderFamiliaInputs()">
            </div>
            <div id="familiasContainer"></div>

            <div class="form-group">
                <label for="numGrupos">Número de Grupos a Crear:</label>
                <input type="number" name="numGrupos" id="numGrupos" class="form-control" min="0" value="0" onchange="renderGrupoInputs()">
            </div>
            <div id="gruposContainer"></div>

            <!-- Selección de familias existentes -->
            <div class="form-group">
                <label for="idFamilia">Selecciona Familias Existentes:</label>
                <select name="idFamilia[]" id="idFamilia" class="form-control" multiple onchange="renderPasswordInputs('familia')">
                    <?php if (empty($familias)): ?>
                        <option disabled>No hay familias disponibles</option>
                    <?php else: ?>
                        <?php foreach ($familias as $familia): ?>
                            <option value="<?= htmlspecialchars($familia['idFamilia']) ?>">
                                <?= htmlspecialchars($familia['nombre_familia']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div id="familiaPasswordsContainer"></div>

            <!-- Selección de grupos existentes -->
            <div class="form-group">
                <label for="idGrupo">Selecciona Grupos Existentes:</label>
                <select name="idGrupo[]" id="idGrupo" class="form-control" multiple onchange="renderPasswordInputs('grupo')">
                    <?php if (empty($grupos)): ?>
                        <option disabled>No hay grupos disponibles</option>
                    <?php else: ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>">
                                <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div id="grupoPasswordsContainer"></div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <button type="submit" class="btn btn-primary">Asignar Usuario</button>
        </form>

    <?php else: ?>
        <div class="alert alert-danger">No tienes permiso para acceder a esta página.</div>
    <?php endif; ?>
</div>

<script>
    // Función para renderizar campos de contraseña para cada familia o grupo seleccionado
    function renderPasswordInputs(type) {
        const select = document.getElementById(type === 'familia' ? 'idFamilia' : 'idGrupo');
        const container = document.getElementById(type === 'familia' ? 'familiaPasswordsContainer' : 'grupoPasswordsContainer');
        container.innerHTML = '';

        Array.from(select.selectedOptions).forEach(option => {
            const div = document.createElement('div');
            div.className = 'form-group';
            div.innerHTML = `
                <label for="${type}Password_${option.value}">Contraseña para ${type === 'familia' ? 'Familia' : 'Grupo'} ${option.text}:</label>
                <input type="password" name="${type}Password[${option.value}]" class="form-control" required>
            `;
            container.appendChild(div);
        });
    }

    // Renderizar campos para crear nuevas familias
    function renderFamiliaInputs() {
        const numFamilias = document.getElementById('numFamilias').value;
        const familiasContainer = document.getElementById('familiasContainer');
        familiasContainer.innerHTML = '';

        for (let i = 0; i < numFamilias; i++) {
            const div = document.createElement('div');
            div.className = 'form-group';
            div.innerHTML = `
                <label for="nombreFamilia_${i}">Nombre de la Nueva Familia ${i + 1}:</label>
                <input type="text" name="nombreFamilia[]" id="nombreFamilia_${i}" class="form-control" required>
                <label for="passwordFamilia_${i}">Contraseña de la Nueva Familia ${i + 1}:</label>
                <input type="password" name="passwordFamilia[]" id="passwordFamilia_${i}" class="form-control" required>
            `;
            familiasContainer.appendChild(div);
        }
    }

    // Renderizar campos para crear nuevos grupos
    function renderGrupoInputs() {
        const numGrupos = document.getElementById('numGrupos').value;
        const gruposContainer = document.getElementById('gruposContainer');
        gruposContainer.innerHTML = '';

        for (let i = 0; i < numGrupos; i++) {
            const div = document.createElement('div');
            div.className = 'form-group';
            div.innerHTML = `
                <label for="nombreGrupo_${i}">Nombre del Nuevo Grupo ${i + 1}:</label>
                <input type="text" name="nombreGrupo[]" id="nombreGrupo_${i}" class="form-control" required>
                <label for="passwordGrupo_${i}">Contraseña del Nuevo Grupo ${i + 1}:</label>
                <input type="password" name="passwordGrupo[]" id="passwordGrupo_${i}" class="form-control" required>
            `;
            gruposContainer.appendChild(div);
        }
    }
</script>