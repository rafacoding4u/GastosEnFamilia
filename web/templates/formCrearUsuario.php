<div class="container p-4">
    <h2>Añadir Nuevo Usuario</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

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

        <!-- El formulario ahora apunta correctamente a la ruta crearUsuario -->
        <form action="index.php?ctl=crearUsuario" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($apellido ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="alias">Alias:</label>
                <input type="text" name="alias" class="form-control" value="<?= htmlspecialchars($alias ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="contrasenya">Contraseña:</label>
                <input type="password" name="contrasenya" class="form-control" required>
            </div>

            <!-- Solo Superadmins pueden asignar roles de Superadmin o Admin -->
            <div class="form-group">
                <label for="nivel_usuario">Nivel de Usuario:</label>
                <select name="nivel_usuario" class="form-control" required>
                    <option value="usuario" <?= isset($nivel_usuario) && $nivel_usuario === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                        <option value="admin" <?= isset($nivel_usuario) && $nivel_usuario === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Asignar a familia (opcional) o crear nueva -->
            <div class="form-group">
                <label for="familia">Familia:</label>
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

            <!-- Asignar a grupo (opcional) o crear nuevo -->
            <div class="form-group">
                <label for="grupo">Grupo:</label>
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

            <!-- Contraseña para asociarse a una familia o grupo existente -->
            <div class="form-group">
                <label for="passwordGrupoFamilia">Contraseña de Familia/Grupo:</label>
                <input type="password" name="passwordGrupoFamilia" class="form-control" placeholder="Contraseña para familia o grupo seleccionado">
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

            <button type="submit" class="btn btn-success">Añadir Usuario</button>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
