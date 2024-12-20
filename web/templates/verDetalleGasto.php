<div class="container p-4">
    <h2>Detalle del Gasto</h2>

    <!-- Verificar si el gasto existe y si el usuario tiene permisos -->
    <?php if (!empty($gasto) && ($_SESSION['usuario']['nivel_usuario'] === 'superadmin' || $_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['idUser'] === $gasto['idUsuario'])): ?>
        <ul class="list-group">
            <li class="list-group-item"><strong>Concepto:</strong> <?= htmlspecialchars($gasto['concepto']) ?></li>
            <li class="list-group-item"><strong>Importe:</strong> <?= number_format($gasto['importe'], 2, ',', '.') ?> €</li>
            <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($gasto['fecha']) ?></li>
            <li class="list-group-item"><strong>Origen:</strong> <?= htmlspecialchars($gasto['origen']) ?></li>
            <li class="list-group-item"><strong>Categoría:</strong> <?= htmlspecialchars($gasto['nombreCategoria']) ?></li>
        </ul>

        <a href="index.php?ctl=verGastos" class="btn btn-primary mt-3">Volver a la lista</a>

    <?php else: ?>
        <!-- Mensaje de error si el gasto no está disponible o el usuario no tiene permisos -->
        <div class="alert alert-danger mt-3">
            No tienes permiso para ver este gasto o el gasto no existe.
        </div>
        <a href="index.php?ctl=verGastos" class="btn btn-primary mt-3">Volver a la lista</a>
    <?php endif; ?>
</div>
