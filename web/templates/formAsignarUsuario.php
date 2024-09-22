<div class="container p-4">
    <h2>Asignar Usuario a Familia o Grupo</h2>

    <!-- Formulario para asignar usuario -->
    <form method="POST" action="index.php?ctl=asignarUsuarioFamiliaGrupo">
        <!-- Selección de usuario -->
        <div class="form-group">
            <label for="idUsuario">Selecciona un Usuario:</label>
            <select name="idUsuario" id="idUsuario" class="form-control">
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= htmlspecialchars($usuario['idUser']) ?>">
                        <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selección de tipo de vinculación (familia o grupo) -->
        <div class="form-group">
            <label for="tipoVinculo">Selecciona si es Familia o Grupo:</label>
            <select name="tipoVinculo" id="tipoVinculo" class="form-control" onchange="toggleVinculoOptions()">
                <option value="familia">Familia</option>
                <option value="grupo">Grupo</option>
            </select>
        </div>

        <!-- Desplegable de familias -->
        <div class="form-group" id="familiasGroup" style="display: none;">
            <label for="idFamilia">Selecciona una Familia:</label>
            <select name="idFamilia" id="idFamilia" class="form-control">
                <?php foreach ($familias as $familia): ?>
                    <option value="<?= htmlspecialchars($familia['idFamilia']) ?>">
                        <?= htmlspecialchars($familia['nombre_familia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Desplegable de grupos -->
        <div class="form-group" id="gruposGroup" style="display: none;">
            <label for="idGrupo">Selecciona un Grupo:</label>
            <select name="idGrupo" id="idGrupo" class="form-control">
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>">
                        <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Campo para introducir la contraseña -->
        <div class="form-group">
            <label for="passwordGrupoFamilia">Introduce la contraseña para acceder al grupo o familia:</label>
            <input type="password" name="passwordGrupoFamilia" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Asignar Usuario</button>
    </form>
</div>

<script>
    function toggleVinculoOptions() {
        var tipoVinculo = document.getElementById('tipoVinculo').value;
        document.getElementById('familiasGroup').style.display = (tipoVinculo === 'familia') ? 'block' : 'none';
        document.getElementById('gruposGroup').style.display = (tipoVinculo === 'grupo') ? 'block' : 'none';
    }
</script>
