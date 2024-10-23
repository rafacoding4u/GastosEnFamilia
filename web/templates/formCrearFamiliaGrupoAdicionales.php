<div class="container p-4">
    <h2>Crear Familias y Grupos Adicionales</h2>

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

    <!-- Formulario para la creación de familias y grupos -->
    <form action="index.php?ctl=crearFamiliaGrupoAdicionales" method="POST">
        <!-- Contraseña premium -->
        <div class="form-group">
            <label for="password_premium">Contraseña Premium:</label>
            <input type="password" name="password_premium" class="form-control" required>
        </div>

        <!-- Desplegable dinámico para creación de familias -->
        <div id="contenedor_familia">
            <div class="form-group">
                <label for="nombre_nueva_familia_1">Nombre Nueva Familia 1:</label>
                <input type="text" name="nombre_nueva_familia_1" class="form-control" placeholder="Nombre de la familia 1">
                <label for="password_nueva_familia_1">Contraseña Familia 1:</label>
                <input type="password" name="password_nueva_familia_1" class="form-control" placeholder="Contraseña de la familia 1">
            </div>
            <div id="familias_adicionales"></div>
            <button type="button" class="btn btn-link" onclick="agregarFamilia()">Añadir otra familia (hasta 4)</button>
        </div>

        <!-- Desplegable dinámico para creación de grupos -->
        <div id="contenedor_grupo">
            <div class="form-group">
                <label for="nombre_nuevo_grupo_1">Nombre Nuevo Grupo 1:</label>
                <input type="text" name="nombre_nuevo_grupo_1" class="form-control" placeholder="Nombre del grupo 1">
                <label for="password_nuevo_grupo_1">Contraseña Grupo 1:</label>
                <input type="password" name="password_nuevo_grupo_1" class="form-control" placeholder="Contraseña del grupo 1">
            </div>
            <div id="grupos_adicionales"></div>
            <button type="button" class="btn btn-link" onclick="agregarGrupo()">Añadir otro grupo (hasta 9)</button>
        </div>

        <!-- Campo oculto para el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token'] ?? '') ?>">

        <!-- Botón de envío -->
        <button type="submit" class="btn btn-primary">Crear Familias y Grupos</button>
    </form>
</div>

<script>
    let contadorFamilias = 1;
    let contadorGrupos = 1;
    const maxFamilias = 4;
    const maxGrupos = 9;

    // Función para añadir familias
    function agregarFamilia() {
        if (contadorFamilias < maxFamilias) {
            contadorFamilias++;
            const contenedor = document.getElementById('familias_adicionales');
            const nuevaFamilia = `
                <div class="form-group">
                    <label for="nombre_nueva_familia_${contadorFamilias}">Nombre Nueva Familia ${contadorFamilias}:</label>
                    <input type="text" name="nombre_nueva_familia_${contadorFamilias}" class="form-control" placeholder="Nombre de la familia ${contadorFamilias}">
                    <label for="password_nueva_familia_${contadorFamilias}">Contraseña Familia ${contadorFamilias}:</label>
                    <input type="password" name="password_nueva_familia_${contadorFamilias}" class="form-control" placeholder="Contraseña de la familia ${contadorFamilias}">
                </div>
            `;
            contenedor.insertAdjacentHTML('beforeend', nuevaFamilia);
        }
    }

    // Función para añadir grupos
    function agregarGrupo() {
        if (contadorGrupos < maxGrupos) {
            contadorGrupos++;
            const contenedor = document.getElementById('grupos_adicionales');
            const nuevoGrupo = `
                <div class="form-group">
                    <label for="nombre_nuevo_grupo_${contadorGrupos}">Nombre Nuevo Grupo ${contadorGrupos}:</label>
                    <input type="text" name="nombre_nuevo_grupo_${contadorGrupos}" class="form-control" placeholder="Nombre del grupo ${contadorGrupos}">
                    <label for="password_nuevo_grupo_${contadorGrupos}">Contraseña Grupo ${contadorGrupos}:</label>
                    <input type="password" name="password_nuevo_grupo_${contadorGrupos}" class="form-control" placeholder="Contraseña del grupo ${contadorGrupos}">
                </div>
            `;
            contenedor.insertAdjacentHTML('beforeend', nuevoGrupo);
        }
    }
</script>
