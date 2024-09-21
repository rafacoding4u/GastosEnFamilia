<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones del Administrador -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a> <!-- Permite ver todos los gastos del sistema -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a> <!-- Permite ver todos los ingresos del sistema -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarGasto">Añadir Gasto</a> <!-- Formulario para insertar un nuevo gasto -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a> <!-- Formulario para insertar un nuevo ingreso -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a> <!-- Ver la situación financiera de los usuarios -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=dashboard">Dashboard Financiero</a> <!-- Ver el dashboard financiero -->
                </li>
                <!-- Gestión de usuarios (solo para admins) -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a> <!-- Permite ver y gestionar todos los usuarios -->
                </li>
                <!-- Gestión de categorías de gastos e ingresos -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarCategoriasGastos">Gestionar Categorías de Gastos</a> <!-- Gestionar categorías de gastos -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarCategoriasIngresos">Gestionar Categorías de Ingresos</a> <!-- Gestionar categorías de ingresos -->
                </li>
                <!-- Cerrar Sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a> <!-- Permite al administrador cerrar sesión -->
                </li>
            </ul>
        </div>
    </div>
</nav>
