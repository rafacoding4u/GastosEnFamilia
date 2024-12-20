<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos En Familia</title>
    <link rel="stylesheet" href="web/css/estilo.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Gastos En Familia</h1>
        <p>Aplicación para la gestión de finanzas familiares</p>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?php $nivel_usuario = $_SESSION['usuario']['nivel_usuario'] ?? null; ?>

                        <?php if ($nivel_usuario === 'superadmin'): ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=actualizarUsuario">Actualizar Usuario</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verGrupos">Gestionar Grupos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verAuditoria">Ver Auditoría</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verPresupuestos">Gestionar Presupuestos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verMetasGlobales">Ver Metas Globales</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formAsignarUsuario">Asignar Usuario a Familia o Grupo</a></li>

                        <?php elseif ($nivel_usuario === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verGrupos">Gestionar Grupos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formCrearFamiliaGrupoAdicionales">Crear Familias/Grupos adicionales</a></li>

                        <?php elseif ($nivel_usuario === 'usuario'): ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Gestionar Categorías de Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Gestionar Categorías de Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verPresupuestos">Ver Presupuestos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verMetas">Ver Metas Financieras</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formCrearFamiliaGrupoAdicionales">Crear Familias/Grupos adicionales</a></li>

                        <?php elseif ($nivel_usuario === 'registro'): ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=registro">Registro</a></li>
                        <?php endif; ?>

                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=registro">Registro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <?php if (isset($_SESSION['notificaciones']) && !empty($_SESSION['notificaciones'])): ?>
            <div class="alert alert-info">
                <?php foreach ($_SESSION['notificaciones'] as $notificacion): ?>
                    <p><?php echo $notificacion['mensaje']; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($contenido)) {
            echo $contenido;
        } ?>
    </main>

    <footer class="footer bg-light text-center py-3">
        <?php include __DIR__ . '/footer.php'; ?>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>