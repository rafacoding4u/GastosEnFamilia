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
            <span style="color: <?= isset($params['balance']) && $params['balance'] >= 0 ? 'green' : 'red'; ?>;">
                <?= isset($params['balance']) && $params['balance'] !== null 
                    ? number_format(htmlspecialchars($params['balance']), 2, ',', '.') . ' €' 
                    : '<span class="text-muted">No disponible</span>' ?>
            </span>
        </p>

        <!-- Mostrar mensaje personalizado -->
        <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
            <div class="alert alert-info mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>

        <!-- Menú condicional basado en el rol del usuario -->
        <div class="mt-4">
            <?php if ($_SESSION['usuario']['nivel_usuario'] == 2): ?>
                <h4>Opciones para SuperAdministrador:</h4>
                <ul>
                    <li><a href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                    <li><a href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                    <li><a href="index.php?ctl=listarGrupos">Gestionar Grupos</a></li>
                    <li><a href="index.php?ctl=verAuditoria">Ver Auditoría</a></li>
                </ul>
            <?php elseif ($_SESSION['usuario']['nivel_usuario'] == 1): ?>
                <h4>Opciones para Administrador:</h4>
                <ul>
                    <li><a href="index.php?ctl=verGastos">Ver Gastos</a></li>
                    <li><a href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                    <li><a href="index.php?ctl=formCrearFamilia">Añadir Nueva Familia</a></li>
                    <li><a href="index.php?ctl=formCrearGrupo">Añadir Nuevo Grupo</a></li>
                </ul>
            <?php else: ?>
                <h4>Opciones para Usuario Regular:</h4>
                <ul>
                    <li><a href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a></li>
                    <li><a href="index.php?ctl=formInsertarGasto">Añadir Gasto</a></li>
                    <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                </ul>
            <?php endif; ?>
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
