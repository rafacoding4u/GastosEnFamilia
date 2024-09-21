<div class="container p-4">
    <h2>Dashboard Financiero</h2>
    
    <h3>Resumen</h3>
    <p>Total Ingresos: <?= htmlspecialchars($params['totalIngresos']) ?> €</p>
    <p>Total Gastos: <?= htmlspecialchars($params['totalGastos']) ?> €</p>
    <p>Balance: <?= htmlspecialchars($params['balance']) ?> €</p>

    <h3>Distribución de Gastos por Categoría</h3>
    <ul>
        <?php foreach ($params['gastosPorCategoria'] as $categoria): ?>
            <li><?= htmlspecialchars($categoria['nombreCategoria']) ?>: <?= htmlspecialchars($categoria['total']) ?> €</li>
        <?php endforeach; ?>
    </ul>

    <h3>Distribución de Ingresos por Categoría</h3>
    <ul>
        <?php foreach ($params['ingresosPorCategoria'] as $categoria): ?>
            <li><?= htmlspecialchars($categoria['nombreCategoria']) ?>: <?= htmlspecialchars($categoria['total']) ?> €</li>
        <?php endforeach; ?>
    </ul>
</div>
