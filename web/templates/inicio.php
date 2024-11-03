<div class="container p-4">
    <!-- Verifica si el usuario está autenticado y que tiene un nombre -->
    <?php if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['nombre'])): ?>
        <h2>Situación Financiera de <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>

        <!-- Definir variables por defecto si no están presentes -->
        <?php
        $totalIngresos = $totalIngresos ?? 0;
        $totalGastos = $totalGastos ?? 0;
        $saldo = $saldo ?? 0;
        ?>

        <!-- Mostrar Total Ingresos -->
        <p><b>Total Ingresos:</b>
            <?= number_format($totalIngresos, 2, ',', '.') . ' €' ?>
        </p>

        <!-- Mostrar Total Gastos -->
        <p><b>Total Gastos:</b>
            <?= number_format($totalGastos, 2, ',', '.') . ' €' ?>
        </p>

        <!-- Mostrar Balance -->
        <p><b>Balance:</b>
            <span style="color: <?= $saldo >= 0 ? 'green' : 'red'; ?>;">
                <?= number_format($saldo, 2, ',', '.') . ' €' ?>
            </span>
        </p>

        <!-- Mostrar notificaciones si hay alguna -->
        <?php if (!empty($_SESSION['notificaciones'])): ?>
            <div class="alert alert-info mt-3">
                <?php foreach ($_SESSION['notificaciones'] as $notificacion): ?>
                    <p><?= htmlspecialchars($notificacion['mensaje']); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje personalizado -->
        <?php if (!empty($mensaje)): ?>
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
        <p>Para gestionar tus ingresos y gastos, por favor <a href="index.php?ctl=iniciarSesion">inicia sesión</a> o regístrate:</p>
        <ul>
            <li><a href="index.php?ctl=registro">Registro Administrador</a></li>
            <li><a href="index.php?ctl=registroInd">Registro Usuario</a></li>
        </ul>

        <!-- Mensaje adicional si hay alguno -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>