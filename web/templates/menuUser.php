<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones del usuario normal -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verGastos">Ver Mis Gastos</a> <!-- Visualiza los gastos del usuario actual -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verIngresos">Ver Mis Ingresos</a> <!-- Visualiza los ingresos del usuario actual -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarGasto">Añadir Gasto</a> <!-- Formulario para añadir gastos -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a> <!-- Formulario para añadir ingresos -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verSituacion">Ver Mi Situación Financiera</a> <!-- Ver la situación financiera del usuario (Ingresos vs Gastos) -->
                </li>
                
                <!-- Opción de cerrar sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a> <!-- Cerrar sesión del usuario -->
                </li>
            </ul>
        </div>
    </div>
</nav>
