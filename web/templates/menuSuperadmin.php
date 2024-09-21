<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones de Gestión Financiera (Superadmin) -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a> <!-- Ver todos los gastos del sistema -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a> <!-- Ver todos los ingresos del sistema -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarGasto">Añadir Gasto</a> <!-- Formulario para insertar un nuevo gasto -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a> <!-- Formulario para insertar un nuevo ingreso -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a> <!-- Ver la situación financiera de usuarios y familias -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=dashboard">Dashboard Financiero</a> <!-- Ver el dashboard financiero -->
                </li>
                <!-- Gestión de Usuarios (Superadmin) -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a> <!-- Gestión completa de usuarios -->
                </li>
                <!-- Opciones avanzadas de Superadmin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a> <!-- Gestionar familias en la plataforma -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verGrupos">Gestionar Grupos</a> <!-- Gestionar grupos asociados a familias -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a> <!-- Gestionar categorías de gastos -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a> <!-- Gestionar categorías de ingresos -->
                </li>
                <!-- Cerrar Sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a> <!-- Opción para cerrar sesión -->
                </li>
            </ul>
        </div>
    </div>
</nav>
