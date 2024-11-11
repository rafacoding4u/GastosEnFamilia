<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Las Cuentas Claras'; ?></title>
    <link rel="stylesheet" href="web/css/estilo.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Las Cuentas Claras</h1>
        <nav class="navbar navbar-expand-md navbar-dark bg-primary">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=inicio">Inicio</a></li>

                            <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                                <!-- Menú completo para SuperAdmin -->
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarGrupos">Gestionar Grupos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Categorías de Gastos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Categorías de Ingresos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=formAsignarUsuario">Asignar Roles</a></li>

                            <?php elseif ($_SESSION['usuario']['nivel_usuario'] === 'admin'): ?>
                                <?php
                                require_once 'app/modelo/classModelo.php';
                                $modelo = new GastosModelo();
                                $familiasAdmin = $modelo->obtenerFamiliasAdmin($_SESSION['usuario']['id']);
                                $gruposAdmin = $modelo->obtenerGruposAdmin($_SESSION['usuario']['id']);
                                ?>
                                <!-- Menú limitado para Admin -->
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarUsuariosAdmin">Gestionar Usuarios (Propios)</a></li>

                                <?php if (count($familiasAdmin) < 5): ?>
                                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=gestionarFamiliasAdmin">Gestionar Familias</a></li>
                                <?php endif; ?>

                                <?php if (count($gruposAdmin) < 5): ?>
                                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=gestionarGruposAdmin">Gestionar Grupos</a></li>
                                <?php endif; ?>

                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Categorías de Gastos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Categorías de Ingresos</a></li>

                            <?php elseif ($_SESSION['usuario']['nivel_usuario'] === 'usuario'): ?>
                                <!-- Menú para Usuario Regular -->
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verResumenFinanciero">Resumen Financiero</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarGastosUsuario">Mis Gastos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarIngresosUsuario">Mis Ingresos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasGastos">Categorías de Gastos</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?ctl=verCategoriasIngresos">Categorías de Ingresos</a></li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="index.php?ctl=salir" onclick="return confirm('¿Seguro que deseas cerrar sesión?');">Cerrar Sesión</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=registro">Registro</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <?php echo $contenido; ?>
    </main>

    <footer class="footer bg-light text-center py-3">
        <?php include __DIR__ . '/footer.php'; ?>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>