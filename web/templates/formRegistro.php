<?php include 'layout.php'; ?>

<div class="container p-4">
    <h3>Registro de Usuario</h3>

    <form action="index.php?ctl=registro" method="post">
        <!-- Campo para el nombre -->
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" 
                   value="<?= isset($params['nombre']) ? htmlspecialchars($params['nombre']) : '' ?>" required>
        </div>

        <!-- Campo para el apellido -->
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" 
                   value="<?= isset($params['apellido']) ? htmlspecialchars($params['apellido']) : '' ?>" required>
        </div>

        <!-- Campo para el alias (nombre de usuario) -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" id="alias" name="alias" class="form-control" 
                   value="<?= isset($params['alias']) ? htmlspecialchars($params['alias']) : '' ?>" required>
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
                   value="<?= isset($params['fecha_nacimiento']) ? htmlspecialchars($params['fecha_nacimiento']) : '' ?>" required>
        </div>

        <!-- Campo para el correo electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= isset($params['email']) ? htmlspecialchars($params['email']) : '' ?>" required>
        </div>

        <!-- Campo para el teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control" 
                   value="<?= isset($params['telefono']) ? htmlspecialchars($params['telefono']) : '' ?>" required>
        </div>

        <!-- Selector de nivel de usuario -->
        <div class="form-group">
            <label for="nivel_usuario">Tipo de Usuario:</label>
            <select id="nivel_usuario" name="nivel_usuario" class="form-control" required>
                <option value="usuario" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= isset($params['nivel_usuario']) && $params['nivel_usuario'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <!-- Selección de grupo o familia -->
        <div class="form-group">
            <label for="tipo_vinculo">¿Perteneces a un grupo o familia existente?</label>
            <select id="tipo_vinculo" name="tipo_vinculo" class="form-control" required>
                <option value="grupo">Grupo</option>
                <option value="familia">Familia</option>
                <option value="individual">Usuario individual (sin grupo o familia)</option>
            </select>
        </div>

        <!-- Campo para seleccionar grupo/familia existente -->
        <div class="form-group" id="selectGrupoFamilia" style="display:none;">
            <label for="idGrupoFamilia">Seleccionar Grupo o Familia:</label>
            <select id="idGrupoFamilia" name="idGrupoFamilia" class="form-control">
                <optgroup label="Grupos">
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="grupo_<?= $grupo['idGrupo'] ?>"><?= $grupo['nombre_grupo'] ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Familias">
                    <?php foreach ($familias as $familia): ?>
                        <option value="familia_<?= $familia['idFamilia'] ?>"><?= $familia['nombre_familia'] ?></option>
                    <?php endforeach; ?>
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

        <!-- Botón de envío -->
        <button type="submit" name="bRegistro" class="btn btn-primary mt-3">Registrarse</button>

        <!-- Mostrar errores de validación -->
        <?php if (isset($errores) && count($errores) > 0): ?>
            <div class="alert alert-danger mt-3">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito -->
        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
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
        } else if (this.value === 'individual') {
            selectGrupoFamilia.style.display = 'none';
            passwordGrupoFamilia.style.display = 'none';
            crearGrupoFamilia.style.display = 'none';
        } else {
            selectGrupoFamilia.style.display = 'none';
            passwordGrupoFamilia.style.display = 'none';
            crearGrupoFamilia.style.display = 'block';
        }
    });
</script>

<?php include 'footer.php'; ?>
