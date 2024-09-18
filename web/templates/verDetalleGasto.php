<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Detalle del Gasto</h2>

    <?php if (!empty($gasto)): ?>
        <ul class="list-group">
            <li class="list-group-item"><strong>Concepto:</strong> <?= htmlspecialchars($gasto['concepto']) ?></li>
            <li class="list-group-item"><strong>Importe:</strong> <?= htmlspecialchars($gasto['importe']) ?> €</li>
            <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($gasto['fecha']) ?></li>
            <li class="list-group-item"><strong>Origen:</strong> <?= htmlspecialchars($gasto['origen']) ?></li>
            <li class="list-group-item"><strong>Categoría:</strong> <?= htmlspecialchars($gasto['nombreCategoria']) ?></li>
        </ul>

        <a href="index.php?ctl=verGastos" class="btn btn-primary mt-3">Volver a la lista</a>
    <?php else: ?>
        <p>El gasto no existe o no se encuentra disponible.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
