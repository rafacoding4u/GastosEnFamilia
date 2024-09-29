<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=UsuarioController&action=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones del usuario normal -->
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
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Mi Situación Financiera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=dashboard">Dashboard Financiero</a>
                </li>
                <!-- Opción de cerrar sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=AuthController&action=salir">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
