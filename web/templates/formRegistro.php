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
            <label for="telefono">Teléfono (opcional):</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono ?? '') ?>">
        </div>

        <!-- Fecha de nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento (opcional):</label>
            <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>">
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="contrasenya">Contraseña:</label>
            <input type="password" name="contrasenya" class="form-control" required>
        </div>

        <!-- Opción de creación de familia o grupo -->
        <div class="form-group">
            <label for="opcion_creacion">¿Desea crear una familia o grupo?</label>
            <select name="opcion_creacion" id="opcion_creacion" class="form-control" onchange="mostrarOpciones()">
                <option value="usuario">No, seré un usuario individual</option>
                <option value="crear_familia">Crear una familia</option>
                <option value="crear_grupo">Crear un grupo</option>
                <option value="crear_ambos">Crear una familia y un grupo</option>
            </select>
        </div>

        <!-- Campos de creación de nueva familia -->
        <div id="contenedor_familia" style="display:none;">
            <div class="form-group">
                <label for="nombre_nueva_familia">Nombre Nueva Familia:</label>
                <input type="text" name="nombre_nueva_familia" class="form-control" placeholder="Escriba el nombre de la familia">
            </div>
            <div class="form-group">
                <label for="password_nueva_familia">Contraseña para Nueva Familia:</label>
                <input type="password" name="password_nueva_familia" class="form-control" placeholder="Escriba una contraseña para la familia">
            </div>
        </div>

        <!-- Campos de creación de nuevo grupo -->
        <div id="contenedor_grupo" style="display:none;">
            <div class="form-group">
                <label for="nombre_nuevo_grupo">Nombre Nuevo Grupo:</label>
                <input type="text" name="nombre_nuevo_grupo" class="form-control" placeholder="Escriba el nombre del grupo">
            </div>
            <div class="form-group">
                <label for="password_nuevo_grupo">Contraseña para Nuevo Grupo:</label>
                <input type="password" name="password_nuevo_grupo" class="form-control" placeholder="Escriba una contraseña para el grupo">
            </div>
        </div>

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

        <!-- Botón de envío -->
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<script>
    // Mostrar dinámicamente las opciones según la selección
    function mostrarOpciones() {
        const opcion = document.getElementById('opcion_creacion').value;
        const contenedorFamilia = document.getElementById('contenedor_familia');
        const contenedorGrupo = document.getElementById('contenedor_grupo');

        contenedorFamilia.style.display = 'none';
        contenedorGrupo.style.display = 'none';

        if (opcion === 'crear_familia') {
            contenedorFamilia.style.display = 'block';
        } else if (opcion === 'crear_grupo') {
            contenedorGrupo.style.display = 'block';
        } else if (opcion === 'crear_ambos') {
            contenedorFamilia.style.display = 'block';
            contenedorGrupo.style.display = 'block';
        }
    }
</script>
