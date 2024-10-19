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
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['idUser']) ?>">
                            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
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
                <select name="idFamilia[]" id="idFamilia" class="form-control" multiple>
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?= htmlspecialchars($familia['idFamilia']) ?>">
                            <?= htmlspecialchars($familia['nombre_familia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selección de grupos existentes -->
            <div class="form-group">
                <label for="idGrupo">Selecciona Grupos Existentes:</label>
                <select name="idGrupo[]" id="idGrupo" class="form-control" multiple>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>">
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Contraseña para asociación existente -->
            <div class="form-group">
                <label for="passwordGrupoFamilia">Introduce la contraseña para acceder al grupo o familia existente (si aplica):</label>
                <input type="password" name="passwordGrupoFamilia" class="form-control">
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= isset($params['csrf_token']) ? htmlspecialchars($params['csrf_token']) : '' ?>">

            <button type="submit" class="btn btn-primary">Asignar Usuario</button>
        </form>

    <?php else: ?>
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>

<script>
    // Renderizar campos para crear familias
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

    // Renderizar campos para crear grupos
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

