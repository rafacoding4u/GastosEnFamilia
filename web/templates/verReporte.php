<div class="container p-4">
    <h2>Reporte Financiero</h2>

    <!-- Mostrar resumen del reporte -->
    <?php if (!empty($reporte)): ?>
        <p><strong>Fecha de Inicio:</strong> <?= htmlspecialchars($reporte['fechaInicio']) ?></p>
        <p><strong>Fecha de Fin:</strong> <?= htmlspecialchars($reporte['fechaFin']) ?></p>
        <p><strong>Total Ingresos:</strong> <?= number_format($reporte['totalIngresos'], 2, ',', '.') ?> €</p>
        <p><strong>Total Gastos:</strong> <?= number_format($reporte['totalGastos'], 2, ',', '.') ?> €</p>
        <p><strong>Balance:</strong> 
            <span style="color: <?= ($reporte['balance'] >= 0) ? 'green' : 'red'; ?>;">
                <?= number_format($reporte['balance'], 2, ',', '.') ?> €
            </span>
        </p>

        <!-- Mostrar detalles del reporte en tabla -->
        <h3>Detalles del Reporte</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Categoría</th>
                    <th>Tipo de Transacción</th>
                    <th>Origen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reporte['transacciones'] as $transaccion): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaccion['fecha']) ?></td>
                        <td><?= htmlspecialchars($transaccion['concepto']) ?></td>
                        <td><?= number_format($transaccion['importe'], 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($transaccion['nombreCategoria']) ?></td>
                        <td><?= htmlspecialchars($transaccion['tipoTransaccion']) ?></td>
                        <td><?= htmlspecialchars($transaccion['origen']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Gráficos del reporte -->
        <h3>Gráficos del Reporte</h3>
        <canvas id="graficoReporte"></canvas>
    <?php else: ?>
        <p>No se encontraron transacciones en el rango de fechas seleccionado.</p>
    <?php endif; ?>
</div>

<!-- Cargar la librería Chart.js para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('graficoReporte').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar', // Puedes cambiar el tipo de gráfico (bar, line, pie, etc.)
        data: {
            labels: [<?php foreach ($reporte['transacciones'] as $transaccion) { echo "'" . htmlspecialchars($transaccion['concepto']) . "',"; } ?>],
            datasets: [{
                label: 'Importe (€)',
                data: [<?php foreach ($reporte['transacciones'] as $transaccion) { echo htmlspecialchars($transaccion['importe']) . ","; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
