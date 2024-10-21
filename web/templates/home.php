<div class="container p-4">
    <h2>Bienvenido a GastosEnFamilia</h2>
    <p>Gestiona tus finanzas familiares de manera eficiente.</p>

    <?php if (isset($_SESSION['usuario'])): ?>
        <?php $nivel_usuario = $_SESSION['usuario']['nivel_usuario'] ?? null; ?>

        <!-- Mostrar las opciones según el nivel de usuario -->
        <?php if ($nivel_usuario === 'superadmin'): ?>
            <!-- Opciones para Superadmin -->
            <p>Accede a las opciones según tu rol:</p>
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

        <?php elseif ($nivel_usuario === 'admin'): ?>
            <!-- Opciones para Admin -->
            <p>Accede a las opciones según tu rol:</p>
            <ul>
                <li><a href="index.php?ctl=verGastos">Ver Gastos</a></li>
                <li><a href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                <li><a href="index.php?ctl=formCrearFamilia">Añadir Nueva Familia</a></li>
                <li><a href="index.php?ctl=formCrearGrupo">Añadir Nuevo Grupo</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                <li><a href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
            </ul>

        <?php elseif ($nivel_usuario === 0): ?>
            <!-- Nada se muestra en el centro para nivel 0 (solo mensajes en la barra superior) -->

        <?php else: ?>
            <p>Rol de usuario no identificado.</p>
        <?php endif; ?>

        <!-- Opción para cerrar sesión solo si la sesión está iniciada -->
        <?php if ($nivel_usuario !== 0): ?>
            <p><a href="index.php?ctl=salir">Cerrar Sesión</a></p>
        <?php endif; ?>

    <?php else: ?>
        <!-- Si el usuario no está autenticado, no mostrar nada aquí -->
    <?php endif; ?>
</div>
