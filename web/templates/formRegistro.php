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

        <!-- Opción de creación de familias y grupos -->
        <div class="form-group">
            <label for="opcion_creacion">¿Desea crear familias o grupos?</label>
            <select name="opcion_creacion" id="opcion_creacion" class="form-control" onchange="mostrarOpciones()">
                <option value="usuario">No, seré un usuario individual</option>
                <option value="crear_familia">Crear hasta cinco familias</option>
                <option value="crear_grupo">Crear hasta diez grupos</option>
                <option value="crear_ambos">Crear hasta cinco familias y diez grupos</option>
            </select>
        </div>

        <!-- Campos de creación de nuevas familias -->
        <div id="contenedor_familia" style="display:none;">
            <div class="form-group">
                <label for="nombre_nueva_familia_1">Nombre Nueva Familia 1:</label>
                <input type="text" name="nombre_nueva_familia_1" class="form-control" placeholder="Nombre de la familia 1">
                <label for="password_nueva_familia_1">Contraseña Familia 1:</label>
                <input type="password" name="password_nueva_familia_1" class="form-control" placeholder="Contraseña de la familia 1">
            </div>
            <!-- Repetir campos de creación de familia hasta 5 -->
            <?php for ($i = 2; $i <= 5; $i++): ?>
            <div class="form-group">
                <label for="nombre_nueva_familia_<?= $i ?>">Nombre Nueva Familia <?= $i ?> (opcional):</label>
                <input type="text" name="nombre_nueva_familia_<?= $i ?>" class="form-control" placeholder="Nombre de la familia <?= $i ?> (opcional)">
                <label for="password_nueva_familia_<?= $i ?>">Contraseña Familia <?= $i ?> (opcional):</label>
                <input type="password" name="password_nueva_familia_<?= $i ?>" class="form-control" placeholder="Contraseña de la familia <?= $i ?> (opcional)">
            </div>
            <?php endfor; ?>
        </div>

        <!-- Campos de creación de nuevos grupos -->
        <div id="contenedor_grupo" style="display:none;">
            <div class="form-group">
                <label for="nombre_nuevo_grupo_1">Nombre Nuevo Grupo 1:</label>
                <input type="text" name="nombre_nuevo_grupo_1" class="form-control" placeholder="Nombre del grupo 1">
                <label for="password_nuevo_grupo_1">Contraseña Grupo 1:</label>
                <input type="password" name="password_nuevo_grupo_1" class="form-control" placeholder="Contraseña del grupo 1">
            </div>
            <!-- Repetir campos de creación de grupo hasta 10 -->
            <?php for ($i = 2; $i <= 10; $i++): ?>
            <div class="form-group">
                <label for="nombre_nuevo_grupo_<?= $i ?>">Nombre Nuevo Grupo <?= $i ?> (opcional):</label>
                <input type="text" name="nombre_nuevo_grupo_<?= $i ?>" class="form-control" placeholder="Nombre del grupo <?= $i ?> (opcional)">
                <label for="password_nuevo_grupo_<?= $i ?>">Contraseña Grupo <?= $i ?> (opcional):</label>
                <input type="password" name="password_nuevo_grupo_<?= $i ?>" class="form-control" placeholder="Contraseña del grupo <?= $i ?> (opcional)">
            </div>
            <?php endfor; ?>
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
