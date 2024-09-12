<?php ob_start(); ?>
<?php include 'layout.php'; ?>

<div class="container">
    <h2>Situación Financiera</h2>

    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?php echo $params['mensaje']; ?>
        </div>
    <?php endif; ?>

    <!-- Mostrar los datos de situación financiera -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Total Ingresos</th>
                <th>Total Gastos</th>
                <th>Saldo Actual</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($params['situacion'])): ?>
                <?php foreach ($params['situacion'] as $situacion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($situacion['usuario']); ?></td>
                        <td><?php echo number_format($situacion['ingresos'], 2); ?> €</td>
                        <td><?php echo number_format($situacion['gastos'], 2); ?> €</td>
                        <td><?php echo number_format($situacion['saldo'], 2); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay datos disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($params['totalSaldo'])): ?>
        <div class="alert alert-success">
            <strong>Total saldo global:</strong> <?php echo number_format($params['totalSaldo'], 2); ?> €
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<?php $contenido = ob_get_clean(); ?>
<?php include 'layout.php'; ?>
