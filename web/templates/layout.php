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

    <!-- Menú de navegación dinámico según el estado de autenticación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <?php 
            if (isset($_SESSION['usuario'])):  // Verificar si el usuario está autenticado
                if (isset($menu)) {
                    $menuPath = __DIR__ . '/' . $menu;
                    if (file_exists($menuPath)) {
                        include $menuPath;  // Incluir el archivo de menú según el rol
                    } else {
                        echo "<div class='alert alert-danger'>Error: El archivo del menú '{$menu}' no fue encontrado en '{$menuPath}'.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>Error: Menú no definido.</div>";
                }
            else:  // Menú básico para usuarios no autenticados
            ?>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=home">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=iniciarSesion">Iniciar Sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?ctl=registro">Registrarse</a></li>
                </ul>
            <?php endif; ?>
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
