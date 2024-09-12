<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos En Familia</title>
    <link rel="stylesheet" href="web/css/estilo.css">
</head>
<body>

    <!-- Cabecera -->
    <header>
        <h1>Gastos En Familia</h1>
        <p>Aplicación para la gestión de finanzas familiares</p>
    </header>

    <!-- Menú según el rol del usuario -->
    <nav>
        <?php if (isset($_SESSION['nivel_usuario'])): ?>
            <?php include __DIR__ . '/' . $menu; ?>
        <?php endif; ?>
    </nav>

    <!-- Contenido principal -->
    <main>
        <?php if (isset($params['mensaje_exito'])): ?>
            <div class="success">
                <p><?php echo $params['mensaje_exito']; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($params['mensaje_error'])): ?>
            <div class="error">
                <p><?php echo $params['mensaje_error']; ?></p>
            </div>
        <?php endif; ?>

        <!-- Aquí se insertará el contenido de la vista individual -->
        <?php echo $contenido; ?>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 Gastos En Familia. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
