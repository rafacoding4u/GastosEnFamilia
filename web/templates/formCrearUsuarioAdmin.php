<div class="container p-4">
    <h2>Crear Nuevo Usuario (Admin)</h2>

    <?php if (isset($params['errores']) && !empty($params['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($params['errores'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?ctl=crearUsuarioAdmin" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>

        <div class="form-group">
            <label for="alias">Alias:</label>
            <input type="text" class="form-control" id="alias" name="alias" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono (opcional):</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento (opcional):</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
        </div>

        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" class="form-control" id="contrasenya" name="contrasenya" required>
        </div>

        <!-- Nivel de Usuario (sin opción de superadmin) -->
        <div class="form-group">
            <label for="nivel_usuario">Rol de Usuario:</label>
            <select class="form-control" id="nivel_usuario" name="nivel_usuario" required>
                <option value="usuario">Usuario</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <!-- Opciones de creación de familia o grupo -->
        <div class="form-group">
            <label for="opcion_creacion">¿Desea crear una o más familias o grupos?</label>
            <select name="opcion_creacion" id="opcion_creacion" class="form-control" onchange="mostrarOpciones()">
                <option value="usuario">No, será un usuario individual</option>
                <option value="crear_familia">Crear una familia</option>
                <option value="crear_grupo">Crear un grupo</option>
                <option value="crear_ambos">Crear una familia y un grupo</option>
            </select>
        </div>

        <!-- Contraseña premium para habilitar múltiples creaciones -->
        <div class="form-group" id="password_premium_group" style="display:none;">
            <label for="password_premium">Contraseña Premium:</label>
            <input type="password" name="password_premium" class="form-control" placeholder="Introduce la contraseña premium para crear más de una familia o grupo">
        </div>

        <!-- Desplegable dinámico para creación de familia -->
        <div id="contenedor_familia" style="display:none;">
            <div class="form-group">
                <label for="nombre_nueva_familia">Nombre de la Familia:</label>
                <input type="text" name="nombre_nueva_familia" class="form-control" placeholder="Nombre de la familia">
                <label for="password_nueva_familia">Contraseña de la Familia:</label>
                <input type="password" name="password_nueva_familia" class="form-control" placeholder="Contraseña de la familia">
            </div>
        </div>

        <!-- Desplegable dinámico para creación de grupo -->
        <div id="contenedor_grupo" style="display:none;">
            <div class="form-group">
                <label for="nombre_nuevo_grupo">Nombre del Grupo:</label>
                <input type="text" name="nombre_nuevo_grupo" class="form-control" placeholder="Nombre del grupo">
                <label for="password_nuevo_grupo">Contraseña del Grupo:</label>
                <input type="password" name="password_nuevo_grupo" class="form-control" placeholder="Contraseña del grupo">
            </div>
        </div>

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>

<script>
    function mostrarOpciones() {
        const opcion = document.getElementById('opcion_creacion').value;
        const contenedorFamilia = document.getElementById('contenedor_familia');
        const contenedorGrupo = document.getElementById('contenedor_grupo');
        const passwordPremiumGroup = document.getElementById('password_premium_group');

        contenedorFamilia.style.display = 'none';
        contenedorGrupo.style.display = 'none';
        passwordPremiumGroup.style.display = 'none';

        if (opcion === 'crear_familia') {
            contenedorFamilia.style.display = 'block';
        } else if (opcion === 'crear_grupo') {
            contenedorGrupo.style.display = 'block';
        } else if (opcion === 'crear_ambos') {
            contenedorFamilia.style.display = 'block';
            contenedorGrupo.style.display = 'block';
        }

        // Mostrar campo de contraseña premium si se selecciona crear una o más familias o grupos
        if (opcion !== 'usuario') {
            passwordPremiumGroup.style.display = 'block';
        }
    }
</script>