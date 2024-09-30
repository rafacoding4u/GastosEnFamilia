<div class="container p-4">
    <h2>Bienvenido a GastosEnFamilia</h2>
    <p>Gestiona tus finanzas familiares de manera eficiente.</p>

    <?php if (isset($_SESSION['usuario'])): ?>
        <!-- Si el usuario está autenticado, mostrar mensaje de bienvenida personalizado -->
        <p>Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>. Ya has iniciado sesión.</p>

        <!-- Mostrar las opciones según el nivel de usuario -->
        <p>Accede a las opciones según tu rol:</p>
        <?php if ($_SESSION['usuario']['nivel_usuario'] == 2): ?>
            <ul>
                <li><a href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                <li><a href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                <li><a href="index.php?ctl=listarGrupos">Gestionar Grupos</a></li>
                <li><a href="index.php?ctl=verAuditoria">Ver Auditoría</a></li>
            </ul>
        <?php elseif ($_SESSION['usuario']['nivel_usuario'] == 1): ?>
            <ul>
                <li><a href="index.php?ctl=verGastos">Ver Gastos</a></li>
                <li><a href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                <li><a href="index.php?ctl=formCrearFamilia">Añadir Nueva Familia</a></li>
                <li><a href="index.php?ctl=formCrearGrupo">Añadir Nuevo Grupo</a></li>
            </ul>
        <?php else: ?>
            <ul>
                <li><a href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a></li>
                <li><a href="index.php?ctl=formInsertarGasto">Añadir Gasto</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
            </ul>
        <?php endif; ?>

        <!-- Opción para cerrar sesión -->
        <p><a href="index.php?ctl=salir">Cerrar Sesión</a></p>

    <?php else: ?>
        <!-- Si el usuario no está autenticado, mostrar los enlaces de inicio de sesión y registro -->
        <p>Por favor, <a href="index.php?ctl=iniciarSesion">inicia sesión</a> o <a href="index.php?ctl=registro">regístrate</a> para continuar.</p>
    <?php endif; ?>
</div>

