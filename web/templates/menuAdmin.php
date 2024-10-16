<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] === 'admin'): ?>
                    <!-- Opciones del administrador -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verGastos">Ver Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verIngresos">Ver Ingresos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formInsertarGasto">Añadir Gasto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formInsertarIngreso">Añadir Ingreso</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verPresupuestos">Ver Presupuestos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formCrearPresupuesto">Añadir Presupuesto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verMetas">Ver Metas Financieras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formCrearMeta">Añadir Meta Financiera</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Situación Financiera</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=formAsignarUsuario">Asignar Usuarios a Familias/Grupos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Resumen financiero -->
<div class="container mt-4">
    <h4>Resumen Financiero</h4>
    <?php
    // Instancia del modelo para obtener la situación financiera
    $modeloGastos = new GastosModelo();
    $situacion = $modeloGastos->obtenerSituacionFinanciera($_SESSION['usuario']['id']);
    ?>
    <div class="card">
        <div class="card-header">
            <strong>Total Ingresos:</strong> <?= number_format($situacion['totalIngresos'], 2, ',', '.') ?> €
        </div>
        <div class="card-header">
            <strong>Total Gastos:</strong> <?= number_format($situacion['totalGastos'], 2, ',', '.') ?> €
        </div>
        <div class="card-header">
            <strong>Saldo:</strong>
            <span style="color: <?= $situacion['saldo'] > 0 ? 'green' : ($situacion['saldo'] < 0 ? 'red' : 'gray') ?>;">
                <?= number_format($situacion['saldo'], 2, ',', '.') ?> €
            </span>
        </div>
        <div class="card-body">
            <button class="btn btn-info toggle-details">Mostrar detalles</button>
            <div class="details-section" style="display: none;">
                <h5>Detalles de Ingresos</h5>
                <ul>
                    <?php foreach ($situacion['detalles_ingresos'] as $ingreso): ?>
                        <li>
                            <?= htmlspecialchars($ingreso['concepto']) ?>: <?= number_format($ingreso['importe'], 2, ',', '.') ?> € (<?= htmlspecialchars($ingreso['fecha']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h5>Detalles de Gastos</h5>
                <ul>
                    <?php foreach ($situacion['detalles_gastos'] as $gasto): ?>
                        <li>
                            <?= htmlspecialchars($gasto['concepto']) ?>: <?= number_format($gasto['importe'], 2, ',', '.') ?> € (<?= htmlspecialchars($gasto['fecha']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('.toggle-details').addEventListener('click', function () {
        const detailsSection = document.querySelector('.details-section');
        if (detailsSection.style.display === 'none') {
            detailsSection.style.display = 'block';
            this.textContent = 'Ocultar detalles';
        } else {
            detailsSection.style.display = 'none';
            this.textContent = 'Mostrar detalles';
        }
    });
</script>
