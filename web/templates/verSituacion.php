<?php ob_start(); ?>
<?php include 'layout.php'; ?>

<div class="container">
    <h2>Situación Financiera de la Familia <?= htmlspecialchars($params['familia']['nombre_familia']) ?></h2>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Mostrar los datos de situación financiera por usuario de la familia -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Total Ingresos (€)</th>
                <th>Total Gastos (€)</th>
                <th>Saldo Actual (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($params['situacion'])): ?>
                <?php foreach ($params['situacion'] as $situacion): ?>
                    <tr>
                        <td><?= htmlspecialchars($situacion['nombre']) ?> <?= htmlspecialchars($situacion['apellido']) ?></td> <!-- Mostrar nombre y apellido del usuario -->
                        <td><?= number_format($situacion['total_ingresos'], 2, ',', '.') ?> €</td> <!-- Total ingresos -->
                        <td><?= number_format($situacion['total_gastos'], 2, ',', '.') ?> €</td> <!-- Total gastos -->
                        <td><?= number_format($situacion['saldo'], 2, ',', '.') ?> €</td> <!-- Mostrar el saldo (ingresos - gastos) -->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay datos disponibles para los miembros de esta familia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Mostrar el saldo global de la familia si está disponible -->
    <?php if (isset($params['totalSaldo'])): ?>
        <div class="alert alert-success">
            <strong>Total saldo global de la familia:</strong> <?= number_format($params['totalSaldo'], 2, ',', '.') ?> € <!-- Saldo global -->
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<?php $contenido = ob_get_clean(); ?>
<?php include 'layout.php'; ?>
