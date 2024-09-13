<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos En Familia</title>
    <!-- Carga de CSS local y, opcionalmente, Bootstrap para mejorar el diseño -->
    <link rel="stylesheet" href="web/css/estilo.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Encabezado de la página -->
    <header class="bg-primary text-white text-center py-3">
        <h1>Gastos En Familia</h1>
        <p>Aplicación para la gestión de finanzas familiares</p>
    </header>

    <!-- Menú de navegación dinámico según el rol del usuario -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <?php 
            if (isset($menu)) {
                // Verificamos si el archivo de menú existe antes de incluirlo
                $menuPath = __DIR__ . '/' . $menu;
                echo "<p class='text-muted small'>DEBUG: Cargando menú desde {$menuPath}</p>"; // Depuración
                if (file_exists($menuPath)) {
                    include $menuPath;
                } else {
                    echo "<div class='alert alert-danger'>Error: El archivo del menú '{$menu}' no fue encontrado en '{$menuPath}'.</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>Error: Menú no definido.</div>";
            }
            ?>
        </div>
    </nav>

    <!-- Contenido principal de la página -->
    <main class="container py-4">
        <?php 
        // Asegurarse de que la variable $contenido esté definida
        if (isset($contenido)) {
            echo $contenido;
        } else {
            echo "<div class='alert alert-danger'>Error: Contenido no disponible.</div>";
        }
        ?>
    </main>

    <!-- Pie de página -->
    <footer class="footer bg-light text-center py-3">
        <?php include __DIR__ . '/footer.php'; ?>
        <p class="text-muted">© 2024 Gastos En Familia. Todos los derechos reservados.</p>
    </footer>

    <!-- Scripts opcionales para Bootstrap y funcionalidades JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
