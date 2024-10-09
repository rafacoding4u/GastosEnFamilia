<div class="container p-4">
    <!-- Verifica si el usuario está autenticado -->
    <?php if (isset($_SESSION['usuario'])): ?>
        <h2>Situación Financiera de <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>

        <!-- Mostrar Total Ingresos -->
        <p><b>Total Ingresos:</b> 
            <?= isset($totalIngresos) && $totalIngresos !== null 
                ? number_format(htmlspecialchars($totalIngresos), 2, ',', '.') . ' €' 
                : '<span class="text-muted">No disponible</span>' ?>
        </p>

        <!-- Mostrar Total Gastos -->
        <p><b>Total Gastos:</b> 
            <?= isset($totalGastos) && $totalGastos !== null 
                ? number_format(htmlspecialchars($totalGastos), 2, ',', '.') . ' €' 
                : '<span class="text-muted">No disponible</span>' ?>
        </p>

        <!-- Mostrar Balance -->
        <p><b>Balance:</b> 
            <span style="color: <?= isset($saldo) && $saldo >= 0 ? 'green' : 'red'; ?>;">
                <?= isset($saldo) && $saldo !== null 
                    ? number_format(htmlspecialchars($saldo), 2, ',', '.') . ' €' 
                    : '<span class="text-muted">No disponible</span>' ?>
            </span>
        </p>

        <!-- Mostrar mensaje personalizado -->
        <?php if (isset($mensaje) && !empty($mensaje)): ?>
            <div class="alert alert-info mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje de advertencia si no hay datos financieros -->
        <?php if ($totalIngresos == 0 && $totalGastos == 0 && $saldo == 0): ?>
            <div class="alert alert-warning mt-3">
                No se encontraron datos financieros. Por favor, añade ingresos y gastos para ver tu situación financiera.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Si el usuario no está autenticado, muestra los enlaces de inicio de sesión y registro -->
        <h2>Bienvenido a GastosEnFamilia</h2>
        <p>Para gestionar tus ingresos y gastos, por favor <a href="index.php?ctl=iniciarSesion">inicia sesión</a> o <a href="index.php?ctl=registro">regístrate</a>.</p>

        <!-- Mensaje adicional si hay alguno -->
        <?php if (isset($mensaje) && !empty($mensaje)): ?>
            <div class="alert alert-info mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
