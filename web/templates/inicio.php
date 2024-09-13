<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Situación Financiera</h2>

    <!-- Total Ingresos -->
    <p><b>Total Ingresos:</b> 
        <?= isset($params['totalIngresos']) ? number_format(htmlspecialchars($params['totalIngresos']), 2) . ' €' : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Total Gastos -->
    <p><b>Total Gastos:</b> 
        <?= isset($params['totalGastos']) ? number_format(htmlspecialchars($params['totalGastos']), 2) . ' €' : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Balance -->
    <p><b>Balance:</b> 
        <?= isset($params['balance']) ? number_format(htmlspecialchars($params['balance']), 2) . ' €' : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Mensaje informativo si está definido -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info mt-3">
            <?= htmlspecialchars($params['mensaje']) ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje por si no hay datos -->
    <?php if (!isset($params['totalIngresos']) && !isset($params['totalGastos']) && !isset($params['balance'])): ?>
        <div class="alert alert-warning mt-3">
            No se encontraron datos financieros. Por favor, añade ingresos y gastos para ver tu situación financiera.
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

