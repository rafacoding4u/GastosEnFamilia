<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos En Familia</title>
    <link rel="stylesheet" href="web/css/estilo.css">
</head>
<body>
    <header>
        <h1>Gastos En Familia</h1>
        <p>Aplicación para la gestión de finanzas familiares</p>
    </header>

   <!-- Menú según el rol del usuario -->
<nav>
    <?php 
    if (isset($menu)) {
        // Verificamos si el archivo de menú existe antes de incluirlo
        $menuPath = __DIR__ . '/' . $menu;
        if (file_exists($menuPath)) {
            include $menuPath;
        } else {
            echo "<p>Error: El archivo del menú '{$menu}' no fue encontrado en '{$menuPath}'.</p>";
        }
    } else {
        echo "<p>Error: Menú no definido.</p>";
    }
    ?>
</nav>



    <!-- Contenido principal -->
    <main>
        <?php 
        // Asegurarse de que la variable $contenido esté definida
        if (isset($contenido)) {
            echo $contenido;
        } else {
            echo "<p>Error: Contenido no encontrado</p>";
        }
        ?>
    </main>

    <!-- Pie de página -->
    <footer>
        <?php include __DIR__ . '/footer.php'; ?>
        <p>© 2024 Gastos En Familia. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
