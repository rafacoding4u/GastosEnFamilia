<div class="container p-4">
    <h2>Registro de Nuevo Usuario</h2>

    <!-- Mostrar mensaje de éxito, si lo hay -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
    <?php endif; ?>

    <!-- Mostrar mensaje de error, si lo hay -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Mostrar errores de validación -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario de registro -->
    <form action="index.php?ctl=registro" method="POST">
        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
        </div>

        <!-- Apellido -->
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($apellido ?? '') ?>" required>
        </div>

        <!-- Alias -->
        <div class="form-group">
            <label for="alias">Alias (Nombre de Usuario):</label>
            <input type="text" name="alias" class="form-control" value="<?= htmlspecialchars($alias ?? '') ?>" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono ?? '') ?>" required>
        </div>

        <!-- Fecha de nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>" required>
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" name="contrasenya" class="form-control" required>
        </div>

        <!-- Rol del Usuario (Usuario o Administrador) -->
        <div class="form-group">
            <label for="rol_vinculo">Tipo de Usuario:</label>
            <select name="rol_vinculo" class="form-control" required>
                <option value="usuario">Usuario Regular</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <!-- Asignar a familia existente o crear nueva -->
        <div class="form-group">
            <label for="familia">Asignar a una Familia (opcional):</label>
            <select name="idFamilia" class="form-control">
                <option value="">Sin familia</option>
                <?php if (!empty($familias)): ?>
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?= htmlspecialchars($familia['idFamilia']) ?>" <?= isset($idFamilia) && $idFamilia == $familia['idFamilia'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($familia['nombre_familia']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay familias disponibles</option>
                <?php endif; ?>
            </select>
            <input type="text" name="nombre_nueva_familia" class="form-control mt-2" placeholder="Crear nueva familia (opcional)">
            <input type="password" name="password_nueva_familia" class="form-control mt-2" placeholder="Contraseña para nueva familia">
        </div>

        <!-- Asignar a grupo existente o crear nuevo -->
        <div class="form-group">
            <label for="grupo">Asignar a un Grupo (opcional):</label>
            <select name="idGrupo" class="form-control">
                <option value="">Sin grupo</option>
                <?php if (!empty($grupos)): ?>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>" <?= isset($idGrupo) && $idGrupo == $grupo['idGrupo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay grupos disponibles</option>
                <?php endif; ?>
            </select>
            <input type="text" name="nombre_nuevo_grupo" class="form-control mt-2" placeholder="Crear nuevo grupo (opcional)">
            <input type="password" name="password_nuevo_grupo" class="form-control mt-2" placeholder="Contraseña para nuevo grupo">
        </div>

        <!-- Contraseñas para familias y grupos existentes -->
        <div class="form-group">
            <label for="passwordFamiliaExistente">Contraseña para Familia Existente:</label>
            <input type="password" name="passwordFamiliaExistente" class="form-control" placeholder="Contraseña de la familia seleccionada">
        </div>

        <div class="form-group">
            <label for="passwordGrupoExistente">Contraseña para Grupo Existente:</label>
            <input type="password" name="passwordGrupoExistente" class="form-control" placeholder="Contraseña del grupo seleccionado">
        </div>

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

        <!-- Botón de envío -->
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>
