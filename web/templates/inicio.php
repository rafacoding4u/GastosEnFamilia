<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Situación Financiera</h2>

    <p><b>Total Ingresos:</b> <?= htmlspecialchars($params['totalIngresos']) ?></p>
    <p><b>Total Gastos:</b> <?= htmlspecialchars($params['totalGastos']) ?></p>
    <p><b>Balance:</b> <?= htmlspecialchars($params['balance']) ?></p>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']) ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
