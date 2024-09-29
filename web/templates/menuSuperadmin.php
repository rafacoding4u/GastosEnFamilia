<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=UsuarioController&action=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones de Gestión Financiera (Superadmin) -->
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
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Situación Financiera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=dashboard">Dashboard Financiero</a>
                </li>
                <!-- Gestión de Usuarios (Superadmin) -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=UsuarioController&action=listarUsuarios">Gestionar Usuarios</a>
                </li>
                <!-- Opciones avanzadas de Superadmin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=listarFamilias">Gestionar Familias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=verGrupos">Gestionar Grupos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=CategoriaController&action=verCategoriasGastos">Gestionar Categorías de Gastos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=CategoriaController&action=verCategoriasIngresos">Gestionar Categorías de Ingresos</a>
                </li>
                <!-- Cerrar Sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=AuthController&action=salir">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
