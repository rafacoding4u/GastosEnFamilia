<h1>Bienvenido a Las Cuentas Claras</h1>
<p><?php echo $mensaje; ?></p>

<?php if ($nivel_usuario === 'superadmin'): ?>
    <h2>Finanzas Globales</h2>
    <p>Total Ingresos: <?php echo $finanzasGlobales['totalIngresos']; ?></p>
    <p>Total Gastos: <?php echo $finanzasGlobales['totalGastos']; ?></p>
    <p>Saldo: <?php echo $finanzasGlobales['saldo']; ?></p>
<?php elseif ($nivel_usuario === 'admin'): ?>
    <h2>Resumen de Finanzas de tus Familias y Grupos</h2>
    <p>Familias gestionadas: <?php echo count($finanzasFamilias); ?></p>
    <p>Grupos gestionados: <?php echo count($finanzasGrupos); ?></p>
<?php else: ?>
    <h2>Situación Financiera Personal</h2>
    <p>Total Ingresos: <?php echo $finanzasPersonales['totalIngresos']; ?></p>
    <p>Total Gastos: <?php echo $finanzasPersonales['totalGastos']; ?></p>
    <p>Saldo: <?php echo $finanzasPersonales['saldo']; ?></p>
<?php endif; ?>