<div class="container p-4">
    <h2>Asignar Usuario a Familia o Grupo</h2>

    <form action="index.php?ctl=asignarUsuario" method="post">
        <div class="form-group">
            <label for="idUsuario">Seleccionar Usuario</label>
            <select class="form-control" id="idUsuario" name="idUsuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['idUser'] ?>"><?= htmlspecialchars($usuario['nombre']) . ' ' . htmlspecialchars($usuario['apellido']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="idFamilia">Asignar a Familia (opcional)</label>
            <select class="form-control" id="idFamilia" name="idFamilia">
                <option value="">-- Ninguna --</option>
                <?php foreach ($familias as $familia): ?>
                    <option value="<?= $familia['idFamilia'] ?>"><?= htmlspecialchars($familia['nombre_familia']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">

            <label for="idGrupo">Asignar a Grupo (opcional)</label>
            <select class="form-control" id="idGrupo" name="idGrupo">
                <option value="">-- Ninguno --</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?= $grupo['idGrupo'] ?>"><?= htmlspecialchars($grupo['nombre_grupo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="bAsignarUsuario" class="btn btn-primary">Asignar Usuario</button>
    </form>
</div>