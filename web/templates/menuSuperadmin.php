<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?ctl=inicio">GastosEnFamilia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
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

                    <!-- Presupuestos -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verPresupuestos">Ver Presupuestos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formCrearPresupuesto">Añadir Presupuesto</a>
                    </li>

                    <!-- Metas Financieras -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=verMetas">Ver Metas Financieras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FinanzasController&action=formCrearMeta">Añadir Meta Financiera</a>
                    </li>

                    <!-- Situación Financiera -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=SituacionFinancieraController&action=verSituacion">Ver Situación Financiera</a>
                    </li>

                    <!-- Gestión de Usuarios (Superadmin) -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=UsuarioController&action=listarUsuarios">Gestionar Usuarios</a>
                    </li>
                    
                    <!-- Nueva opción: Asignar Usuario a Familia o Grupo -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=formAsignarUsuario">Asignar Usuario a Familia o Grupo</a>
                    </li>

                    <!-- Opciones avanzadas de Superadmin -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=listarFamilias">Gestionar Familias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=FamiliaGrupoController&action=listarGrupos">Gestionar Grupos</a>
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
                <?php else: ?>
                    <!-- Si no es superadmin, redirigir al inicio de sesión -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=AuthController&action=iniciarSesion">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
