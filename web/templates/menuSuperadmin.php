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

                    <!-- Presupuestos -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verPresupuestos">Ver Presupuestos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=formCrearPresupuesto">Añadir Presupuesto</a>
                    </li>

                    <!-- Metas Financieras -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verMetas">Ver Metas Financieras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=formCrearMeta">Añadir Meta Financiera</a>
                    </li>

                    <!-- Situación Financiera -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a>
                    </li>

                    <!-- Gestión de Usuarios (Superadmin) -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a>
                    </li>

                    <!-- Nueva opción: Asignar Usuario a Familia o Grupo -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=formAsignarUsuario">Asignar Usuario a Familia o Grupo</a>
                    </li>

                    <!-- Opciones avanzadas de Superadmin -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=listarGrupos">Gestionar Grupos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a>
                    </li>

                    <!-- Cerrar Sesión -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <?php
                    // Log para registrar intentos no autorizados
                    error_log("Intento de acceso al menú SuperAdmin sin permisos.");
                    error_log("Nivel de usuario actual: " . ($_SESSION['usuario']['nivel_usuario'] ?? 'No autenticado'));
                    error_log("Detalles de sesión: " . print_r($_SESSION, true));

                    // Mensaje de error para el usuario redirigido
                    $_SESSION['mensaje_error'] = "No tienes permisos de SuperAdmin para acceder a esta sección.";
                    ?>
                    <!-- Redirección al inicio de sesión con mensaje contextual -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=iniciarSesion" title="Debes iniciar sesión como SuperAdmin para acceder.">
                            Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>