<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Opciones del Administrador -->

                <!-- Gastos e Ingresos -->
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

                <!-- Situación Financiera -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=dashboard">Dashboard</a>
                </li>


                <!-- Gestión de Familias y Grupos -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=listarGrupos">Gestionar Grupos</a>
                </li>

                <!-- Nueva opción: Asignar Usuarios a Familias/Grupos -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=asignarUsuarioFamiliaGrupo">Asignar Usuarios a Familias/Grupos</a>
                </li>

                <!-- Gestión de categorías (el administrador no puede borrar categorías) -->
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