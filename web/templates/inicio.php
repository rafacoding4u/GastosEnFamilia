<?php ob_start() ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12 text-center">
            <!-- Mostrar la fecha actual -->
            <h3 class="text-center mb-4"><b><?php echo $params['fecha'] ?></b></h3>

            <!-- Mostrar mensaje de bienvenida -->
            <h1 class="display-4"><b><?php echo $params['mensaje'] ?></b></h1>

            <!-- Mostrar mensaje secundario -->
            <h4 class="text-muted mt-3"><?php echo $params['mensaje2'] ?></h4>
            
            <!-- Botones de acción -->
            <div class="mt-4">
                <a href="index.php?ctl=iniciarSesion" class="btn btn-primary btn-lg mx-2">Iniciar Sesión</a>
                <a href="index.php?ctl=registro" class="btn btn-secondary btn-lg mx-2">Registrarse</a>
            </div>
        </div>
    </div>
</div>

<?php $contenido = ob_get_clean() ?>
<?php include 'layout.php' ?>

