<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=UsuarioController&action=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Gastos e Ingresos -->
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

                <!-- Situación Financiera -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Situación Financiera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=estadoFinanciero">Estado Financiero</a>
                </li>

                <!-- Gestión de Familias y Grupos -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=formAsignarUsuario">Asignar Usuarios a Familias/Grupos</a>
                </li>

                <!-- Gestión de Categorías -->
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
