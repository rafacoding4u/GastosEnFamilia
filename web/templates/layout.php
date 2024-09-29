<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos En Familia</title>
    <!-- Carga de CSS local y Bootstrap para mejorar el diseño -->
    <link rel="stylesheet" href="web/css/estilo.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Encabezado de la página -->
    <header class="bg-primary text-white text-center py-3">
        <h1>Gastos En Familia</h1>
        <p>Aplicación para la gestión de finanzas familiares</p>
    </header>

    <!-- Menú de navegación siempre visible según el rol del usuario -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=inicio">Inicio</a></li>
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?php if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin'): ?>
                            <!-- Opciones del superadministrador -->
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarUsuarios">Gestionar Usuarios</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarFamilias">Gestionar Familias</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=listarGrupos">Gestionar Grupos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verAuditoria">Ver Auditoría</a></li>
                        <?php elseif ($_SESSION['usuario']['nivel_usuario'] === 'admin'): ?>
                            <!-- Opciones del administrador -->
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verGastos">Ver Gastos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verIngresos">Ver Ingresos</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formCrearFamilia">Añadir Nueva Familia</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formCrearGrupo">Añadir Nuevo Grupo</a></li>
                        <?php else: ?>
                            <!-- Opciones del usuario normal -->
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formInsertarIngreso">Añadir Ingreso</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=formInsertarGasto">Añadir Gasto</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?ctl=verSituacion">Ver Situación Financiera</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=salir">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <!-- Opciones para usuarios no autenticados -->
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=home">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?ctl=registro">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal de la página -->
    <main class="container py-4">
        <?php 
        if (isset($contenido)) {
            echo $contenido;  // Mostrar el contenido dinámico
        } else {
            echo "<div class='alert alert-danger'>Error: Contenido no disponible.</div>";
        }
        ?>
    </main>

    <!-- Pie de página -->
    <footer class="footer bg-light text-center py-3">
        <?php include __DIR__ . '/footer.php'; ?>
    </footer>

    <!-- Scripts opcionales para Bootstrap y funcionalidades JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
