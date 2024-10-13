<div class="container p-4">
    <h3>Registro de Usuario</h3>

    <form action="index.php?ctl=registro" method="post">
        <!-- Campo para el nombre -->
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" 
                   value="<?= isset($params['nombre']) ? htmlspecialchars($params['nombre'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Campo para el apellido -->
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" 
                   value="<?= isset($params['apellido']) ? htmlspecialchars($params['apellido'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Campo para el alias (nombre de usuario) -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" 
                   value="<?= isset($params['alias']) ? htmlspecialchars($params['alias'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Campo para la contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" class="form-control" required>
        </div>

        <!-- Campo para la fecha de nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" 
                   value="<?= isset($params['fecha_nacimiento']) ? htmlspecialchars($params['fecha_nacimiento'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Campo para el correo electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= isset($params['email']) ? htmlspecialchars($params['email'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Campo para el teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control" 
                   value="<?= isset($params['telefono']) ? htmlspecialchars($params['telefono'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
        </div>

        <!-- Selección de grupo o familia -->
        <div class="form-group">
            <label for="tipo_vinculo">¿Perteneces a un grupo o familia existente?</label>
            <select id="tipo_vinculo" name="tipo_vinculo" class="form-control" required>
                <option value="grupo">Grupo</option>
                <option value="familia">Familia</option>
                <option value="crear_familia">Crear nueva familia</option>
                <option value="crear_grupo">Crear nuevo grupo</option>
                <option value="individual">Usuario individual (sin grupo o familia)</option>
            </select>
        </div>

        <!-- Campo para seleccionar grupo/familia existente -->
        <div class="form-group" id="selectGrupoFamilia" style="display:none;">
            <label for="idGrupoFamilia">Seleccionar Grupo o Familia:</label>
            <select id="idGrupoFamilia" name="idGrupoFamilia" class="form-control">
                <optgroup label="Grupos">
                    <?php if (!empty($grupos)) : ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="grupo_<?= htmlspecialchars($grupo['idGrupo'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($grupo['nombre_grupo'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay grupos disponibles</option>
                    <?php endif; ?>
                </optgroup>
                <optgroup label="Familias">
                    <?php if (!empty($familias)) : ?>
                        <?php foreach ($familias as $familia): ?>
                            <option value="familia_<?= htmlspecialchars($familia['idFamilia'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($familia['nombre_familia'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay familias disponibles</option>
                    <?php endif; ?>
                </optgroup>
            </select>
        </div>

        <!-- Campo para la contraseña del grupo/familia -->
        <div class="form-group" id="passwordGrupoFamilia" style="display:none;">
            <label for="passwordGrupoFamilia">Contraseña del Grupo/Familia:</label>
            <input type="password" id="passwordGrupoFamilia" name="passwordGrupoFamilia" class="form-control">
        </div>

        <!-- Creación de un nuevo grupo o familia -->
        <div class="form-group" id="crearGrupoFamilia" style="display:none;">
            <label for="nombre_nuevo">Nombre del Nuevo Grupo/Familia:</label>
            <input type="text" id="nombre_nuevo" name="nombre_nuevo" class="form-control">
            <label for="password_nuevo">Contraseña del Nuevo Grupo/Familia:</label>
            <input type="password" id="password_nuevo" name="password_nuevo" class="form-control">
        </div>

        <!-- Campo oculto para el token CSRF -->
        <?php if (isset($params['csrf_token'])): ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <?php else: ?>
            <input type="hidden" name="csrf_token" value="">
        <?php endif; ?>

        <!-- Botón de envío -->
        <button type="submit" name="bRegistro" class="btn btn-primary mt-3">Registrarse</button>

        <!-- Mostrar errores de validación -->
        <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    document.getElementById('tipo_vinculo').addEventListener('change', function() {
        var selectGrupoFamilia = document.getElementById('selectGrupoFamilia');
        var passwordGrupoFamilia = document.getElementById('passwordGrupoFamilia');
        var crearGrupoFamilia = document.getElementById('crearGrupoFamilia');
        
        if (this.value === 'grupo' || this.value === 'familia') {
            selectGrupoFamilia.style.display = 'block';
            passwordGrupoFamilia.style.display = 'block';
            crearGrupoFamilia.style.display = 'none';
        } else if (this.value === 'crear_familia' || this.value === 'crear_grupo') {
            selectGrupoFamilia.style.display = 'none';
            passwordGrupoFamilia.style.display = 'none';
            crearGrupoFamilia.style.display = 'block';
        } else if (this.value === 'individual') {
            selectGrupoFamilia.style.display = 'none';
            passwordGrupoFamilia.style.display = 'none';
            crearGrupoFamilia.style.display = 'none';
        }
    });
</script>
