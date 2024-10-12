<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=AuthController&action=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] === 0): ?>
                    <!-- Opciones del usuario normal -->
                    
                    <!-- Gestión de Gastos e Ingresos -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verGastos">Ver Mis Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verIngresos">Ver Mis Ingresos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formInsertarGasto">Añadir Gasto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formInsertarIngreso">Añadir Ingreso</a>
                    </li>

                    <!-- Situación Financiera -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Mi Situación Financiera</a>
                    </li>

                    <!-- Cerrar Sesión -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=AuthController&action=salir">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <!-- Si no hay sesión o el nivel no es correcto, redirigir al inicio de sesión -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=AuthController&action=iniciarSesion">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
