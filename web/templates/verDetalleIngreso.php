<div class="container p-4">
    <h2>Detalle del Ingreso</h2>

    <!-- Verificar si el ingreso existe y si el usuario tiene permisos -->
    <?php if (!empty($ingreso) && ($_SESSION['usuario']['nivel_usuario'] === 'superadmin' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['idUser'] === $ingreso['idUsuario'])): ?>
        <ul class="list-group">
            <li class="list-group-item"><strong>Concepto:</strong> <?= htmlspecialchars($ingreso['concepto']) ?></li>
            <li class="list-group-item"><strong>Importe:</strong> <?= number_format($ingreso['importe'], 2, ',', '.') ?> €</li>
            <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($ingreso['fecha']) ?></li>
            <li class="list-group-item"><strong>Origen:</strong> <?= htmlspecialchars($ingreso['origen']) ?></li>
            <li class="list-group-item"><strong>Categoría:</strong> <?= htmlspecialchars($ingreso['nombreCategoria']) ?></li>
        </ul>

        <a href="index.php?ctl=verIngresos" class="btn btn-primary mt-3">Volver a la lista</a>

    <?php else: ?>
        <!-- Mensaje de error si el ingreso no está disponible o el usuario no tiene permisos -->
        <div class="alert alert-danger mt-3">
            No tienes permiso para ver este ingreso o el ingreso no existe.
        </div>
        <a href="index.php?ctl=verIngresos" class="btn btn-primary mt-3">Volver a la lista</a>
    <?php endif; ?>
</div>
