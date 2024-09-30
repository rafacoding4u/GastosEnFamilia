<div class="container p-4">
    <h2>Crear Presupuesto</h2>

    <!-- Verificación de permisos para mostrar el formulario solo a usuarios autorizados -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'usuario' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>

        <!-- Formulario para crear un presupuesto -->
        <form action="index.php?ctl=FinanzasController&action=crearPresupuesto" method="post">
            <!-- Campo para el nombre del presupuesto -->
            <div class="form-group">
                <label for="nombrePresupuesto">Nombre del Presupuesto:</label>
                <input type="text" id="nombrePresupuesto" name="nombrePresupuesto" class="form-control" required>
            </div>

            <!-- Selector de categoría asociada al presupuesto -->
            <div class="form-group">
                <label for="idCategoria">Categoría:</label>
                <select id="idCategoria" name="idCategoria" class="form-control" required>
                    <option value="" disabled selected>Seleccionar Categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
                            <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo para la cantidad límite del presupuesto -->
            <div class="form-group">
                <label for="cantidadLimite">Límite Mensual (€):</label>
                <input type="number" id="cantidadLimite" name="cantidadLimite" class="form-control" step="0.01" required>
            </div>

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($params['csrf_token']) ?>">

            <!-- Botón para crear el presupuesto -->
            <button type="submit" name="bCrearPresupuesto" class="btn btn-primary mt-3">Crear Presupuesto</button>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-danger mt-3">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <!-- Mostrar mensaje si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
