<?php ob_start() ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h2>Iniciar Sesión</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($params['mensaje'])) : ?>
                        <div class="alert alert-danger">
                            <?php echo $params['mensaje'] ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?ctl=iniciarSesion" method="post">
                        <div class="form-group mb-3">
                            <label for="nombreUsuario">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" placeholder="Nombre de usuario">
                        </div>
                        <div class="form-group mb-3">
                            <label for="contrasenya">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenya" name="contrasenya" placeholder="Contraseña">
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="bIniciarSesion">Aceptar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $contenido = ob_get_clean() ?>
<?php include 'layout.php' ?>
