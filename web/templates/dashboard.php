<div class="container p-4">
    <h2>Resultado Financiero</h2>
    
    <!-- Resumen Global del Administrador -->
    <h3>Resumen General</h3>
    <p>Total Ingresos: <?= number_format($params['totalIngresos'] ?? 0, 2, ',', '.') ?> €</p>
    <p>Total Gastos: <?= number_format($params['totalGastos'] ?? 0, 2, ',', '.') ?> €</p>
    <p>Balance: 
        <span style="color: <?= ($params['balance'] ?? 0) >= 0 ? 'green' : 'red'; ?>;">
            <?= number_format($params['balance'] ?? 0, 2, ',', '.') ?> €
        </span>
    </p>

    <!-- Resumen Financiero por Familia -->
    <?php if (!empty($params['familias'])): ?>
        <h3>Resultado Financiero por Familia</h3>
        <?php foreach ($params['familias'] as $familia): ?>
            <h4><?= htmlspecialchars($familia['nombreFamilia']) ?></h4>
            <p>Total Ingresos: <?= number_format($familia['totalIngresos'] ?? 0, 2, ',', '.') ?> €</p>
            <p>Total Gastos: <?= number_format($familia['totalGastos'] ?? 0, 2, ',', '.') ?> €</p>
            <p>Balance: 
                <span style="color: <?= ($familia['balance'] ?? 0) >= 0 ? 'green' : 'red'; ?>;">
                    <?= number_format($familia['balance'] ?? 0, 2, ',', '.') ?> €
                </span>
            </p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No perteneces a ninguna familia actualmente.</p>
    <?php endif; ?>

    <!-- Resumen Financiero por Grupo -->
    <?php if (!empty($params['grupos'])): ?>
        <h3>Resultado Financiero por Grupo</h3>
        <?php foreach ($params['grupos'] as $grupo): ?>
            <h4><?= htmlspecialchars($grupo['nombreGrupo']) ?></h4>
            <p>Total Ingresos: <?= number_format($grupo['totalIngresos'] ?? 0, 2, ',', '.') ?> €</p>
            <p>Total Gastos: <?= number_format($grupo['totalGastos'] ?? 0, 2, ',', '.') ?> €</p>
            <p>Balance: 
                <span style="color: <?= ($grupo['balance'] ?? 0) >= 0 ? 'green' : 'red'; ?>;">
                    <?= number_format($grupo['balance'] ?? 0, 2, ',', '.') ?> €
                </span>
            </p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No perteneces a ningún grupo actualmente.</p>
    <?php endif; ?>
</div>
