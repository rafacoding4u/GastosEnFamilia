<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Añadir Nuevo Usuario</h2>
    <form action="index.php?ctl=crearUsuario" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="alias">Alias:</label>
            <input type="text" name="alias" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>


        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" name="contrasenya" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nivel_usuario">Nivel de Usuario:</label>
            <select name="nivel_usuario" class="form-control" required>
                <option value="usuario">Usuario</option>
                <option value="admin">Administrador</option>
                <option value="superadmin">Super Administrador</option>
            </select>
        </div>

        <div class="form-group">
            <label for="familia">Familia:</label>
            <select name="idFamilia" class="form-control">
                <option value="">Sin familia</option>
                <?php foreach ($familias as $familia): ?>
                    <option value="<?= htmlspecialchars($familia['idFamilia']) ?>"><?= htmlspecialchars($familia['nombre_familia']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="grupo">Grupo:</label>
            <select name="idGrupo" class="form-control">
                <option value="">Sin grupo</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>"><?= htmlspecialchars($grupo['nombre_grupo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Añadir Usuario</button>
    </form>
</div>

<?php include 'footer.php'; ?>