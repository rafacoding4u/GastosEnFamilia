<?php include 'layout.php'; ?>

<div class="container p-4">
    <!-- Verifica si el usuario está autenticado -->
    <?php if (isset($_SESSION['usuario'])): ?>
        <h2>Situación Financiera de <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>

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

        <!-- Enlaces a otras funcionalidades si el usuario está autenticado -->
        <div class="mt-4">
            <a href="index.php?ctl=formInsertarIngreso" class="btn btn-success">Añadir Ingreso</a>
            <a href="index.php?ctl=formInsertarGasto" class="btn btn-danger">Añadir Gasto</a>
        </div>

        <!-- Mensaje de advertencia si no hay datos financieros -->
        <?php if (!isset($params['totalIngresos']) && !isset($params['totalGastos']) && !isset($params['balance'])): ?>
            <div class="alert alert-warning mt-3">
                No se encontraron datos financieros. Por favor, añade ingresos y gastos para ver tu situación financiera.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Si el usuario no está autenticado, muestra los enlaces de inicio de sesión y registro -->
        <h2>Bienvenido a GastosEnFamilia</h2>
        <p>Para gestionar tus ingresos y gastos, por favor <a href="index.php?ctl=iniciarSesion">inicia sesión</a> o <a href="index.php?ctl=registro">regístrate</a>.</p>

        <!-- Mensaje adicional si hay alguno -->
        <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
            <div class="alert alert-info mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
