<div class="container p-4">
    <h2>Detalle del Ingreso</h2>

    <?php if (!empty($ingreso)): ?>
        <ul class="list-group">
            <li class="list-group-item"><strong>Concepto:</strong> <?= htmlspecialchars($ingreso['concepto']) ?></li>
            <li class="list-group-item"><strong>Importe:</strong> <?= htmlspecialchars($ingreso['importe']) ?> €</li>
            <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($ingreso['fecha']) ?></li>
            <li class="list-group-item"><strong>Origen:</strong> <?= htmlspecialchars($ingreso['origen']) ?></li>
            <li class="list-group-item"><strong>Categoría:</strong> <?= htmlspecialchars($ingreso['nombreCategoria']) ?></li>
        </ul>

        <a href="index.php?ctl=FinanzasController&action=verIngresos" class="btn btn-primary mt-3">Volver a la lista</a>
    <?php else: ?>
        <p>El ingreso no existe o no se encuentra disponible.</p>
    <?php endif; ?>
</div>