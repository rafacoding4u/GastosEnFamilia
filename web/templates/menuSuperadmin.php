<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones para Superadmin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarGasto">Añadir Gasto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a>
                </li>

                <!-- Gestión de usuarios para Superadmin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a>
                </li>

                <!-- Nuevas opciones para gestión avanzada de Superadmin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarGrupos">Gestionar Grupos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarCategoriasGastos">Gestionar Categorías de Gastos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarCategoriasIngresos">Gestionar Categorías de Ingresos</a>
                </li>

                <!-- Cerrar Sesión -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
