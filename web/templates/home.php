<div class="container p-4">
    <h2>Bienvenido a GastosEnFamilia</h2>
    <p>Gestiona tus finanzas familiares de manera eficiente.</p>

    <?php if (isset($_SESSION['usuario'])): ?>
        <!-- Si el usuario está autenticado, mostrar mensaje de bienvenida personalizado -->
        <p>Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>. Ya has iniciado sesión.</p>

        <!-- Mostrar las opciones según el nivel de usuario -->
        <p>Accede a las opciones según tu rol:</p>

        <!-- Superadmin (nivel_usuario = 'superadmin') -->
        <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
            <ul>
                <li><a href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                <li><a href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                <li><a href="index.php?ctl=verGrupos">Gestionar Grupos</a></li>
                <li><a href="index.php?ctl=verAuditoria">Ver Auditoría</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                <li><a href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                <li><a href="index.php?ctl=verPresupuestos">Gestionar Presupuestos</a></li>
                <li><a href="index.php?ctl=verMetasGlobales">Ver Metas Globales</a></li>
                <li><a href="index.php?ctl=formAsignarUsuario">Asignar Usuario a Familia o Grupo</a></li>
            </ul>

        <!-- Admin (nivel_usuario = 'admin') -->
        <?php elseif ($_SESSION['usuario']['nivel_usuario'] === 'admin'): ?>
            <ul>
                <li><a href="index.php?ctl=verGastos">Ver Gastos</a></li>
                <li><a href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                <li><a href="index.php?ctl=formCrearFamilia">Añadir Nueva Familia</a></li>
                <li><a href="index.php?ctl=formCrearGrupo">Añadir Nuevo Grupo</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                <li><a href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
            </ul>

        <!-- Usuario regular (nivel_usuario = 'usuario') -->
        <?php elseif ($_SESSION['usuario']['nivel_usuario'] === 'usuario'): ?>
            <ul>
                <li><a href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a></li>
                <li><a href="index.php?ctl=formInsertarGasto">Añadir Gasto</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
            </ul>
        <?php else: ?>
            <p>Rol de usuario no identificado.</p>
        <?php endif; ?>

        <!-- Opción para cerrar sesión -->
        <p><a href="index.php?ctl=salir">Cerrar Sesión</a></p>

    <?php else: ?>
        <!-- Si el usuario no está autenticado, mostrar los enlaces de inicio de sesión y registro -->
        <p>Por favor, <a href="index.php?ctl=iniciarSesion">inicia sesión</a> o <a href="index.php?ctl=registro">regístrate</a> para continuar.</p>
    <?php endif; ?>
</div>