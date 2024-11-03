<div class="container p-4">
    <h2>Bienvenido a GastosEnFamilia</h2>
    <p>Gestiona tus finanzas familiares de manera eficiente.</p>

    <?php if (isset($_SESSION['usuario'])): ?>
        <?php $nivel_usuario = $_SESSION['usuario']['nivel_usuario'] ?? 'registro'; ?>

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
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                <li><a href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                <li><a href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                <li><a href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
                <li><a href="index.php?ctl=formCrearFamiliaGrupoAdicionales">Crear Familias/Grupos adicionales</a></li>
                <li><a href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                <li><a href="index.php?ctl=verGrupos">Gestionar Grupos</a></li>
                <li><a href="index.php?ctl=asignarUsuarioFamiliaGrupo">Asignar Usuario a Familia o Grupo</a></li>
            </ul>

        <?php elseif ($nivel_usuario === 'usuario'): ?>
            <!-- Opciones para Usuario regular -->
            <p>Accede a las opciones disponibles:</p>
            <ul>
                <li><a href="index.php?ctl=verGastos">Ver Gastos</a></li>
                <li><a href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                <li><a href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                <li><a href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                <li><a href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                <li><a href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                <li><a href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
            </ul>

        <?php elseif ($nivel_usuario === 'registro'): ?>
            <!-- Opciones para usuarios no autenticados o registrados en proceso -->
            <p>Accede a la aplicación con una de las siguientes opciones:</p>
            <ul>
                <li><a href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                <li><a href="index.php?ctl=registro">Registro Administrador</a></li>
                <li><a href="index.php?ctl=registroInd">Registro Usuario</a></li>
            </ul>

        <?php else: ?>
            <p>Rol de usuario no identificado.</p>
        <?php endif; ?>

        <!-- Opción para cerrar sesión si está autenticado y tiene un rol válido -->
        <?php if ($nivel_usuario !== 'registro'): ?>
            <p><a href="index.php?ctl=salir">Cerrar Sesión</a></p>
        <?php endif; ?>

    <?php else: ?>
        <!-- Opciones para usuarios no autenticados -->
        <p>Accede a la aplicación con una de las siguientes opciones:</p>
        <ul>
            <li><a href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
            <li><a href="index.php?ctl=registro">Registro Administrador</a></li>
            <li><a href="index.php?ctl=registroInd">Registro Usuario</a></li>
        </ul>
    <?php endif; ?>
</div>