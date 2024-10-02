<div class="container p-4">
    <h2>Editar Categoría de Ingreso</h2>

    <!-- Verificar permisos del usuario -->
    <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
    
        <!-- Formulario para editar categoría de ingresos -->
        <form action="index.php?ctl=actualizarCategoriaIngreso" method="post">
            <!-- Campo oculto para pasar el ID de la categoría -->
            <input type="hidden" name="idCategoria" value="<?= htmlspecialchars($categoria['idCategoria']) ?>">

            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">


            <!-- Campo para el nombre de la categoría -->
            <div class="form-group">
                <label for="nombreCategoria">Nombre de la categoría:</label>
                <input type="text" id="nombreCategoria" name="nombreCategoria" class="form-control" value="<?= htmlspecialchars($categoria['nombreCategoria']) ?>" required>
            </div>

            <!-- Botón para guardar los cambios -->
            <button type="submit" name="bEditarCategoriaIngreso" class="btn btn-primary mt-3">Guardar Cambios</button>

            <!-- Mostrar mensaje de éxito o error si existe -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info mt-3">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <!-- Mensaje de error si el usuario no tiene permisos -->
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    <?php endif; ?>
</div>
