<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Situación Financiera</h2>

    <!-- Mostrar Total Ingresos -->
    <p><b>Total Ingresos:</b> 
        <?= isset($params['totalIngresos']) && $params['totalIngresos'] !== null 
            ? number_format(htmlspecialchars($params['totalIngresos']), 2, ',', '.') . ' €' 
            : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Mostrar Total Gastos -->
    <p><b>Total Gastos:</b> 
        <?= isset($params['totalGastos']) && $params['totalGastos'] !== null 
            ? number_format(htmlspecialchars($params['totalGastos']), 2, ',', '.') . ' €' 
            : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Mostrar Balance -->
    <p><b>Balance:</b> 
        <?= isset($params['balance']) && $params['balance'] !== null 
            ? number_format(htmlspecialchars($params['balance']), 2, ',', '.') . ' €' 
            : '<span class="text-muted">No disponible</span>' ?>
    </p>

    <!-- Mensaje informativo si está definido -->
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info mt-3">
            <?= htmlspecialchars($params['mensaje']) ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje de advertencia si no hay datos financieros -->
    <?php if (!isset($params['totalIngresos']) && !isset($params['totalGastos']) && !isset($params['balance'])): ?>
        <div class="alert alert-warning mt-3">
            No se encontraron datos financieros. Por favor, añade ingresos y gastos para ver tu situación financiera.
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
