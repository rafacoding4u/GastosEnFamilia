<?php ob_start() ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h2>Registro de Usuario</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($params['mensaje'])) : ?>
                        <div class="alert alert-info">
                            <?php echo $params['mensaje'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errores)) : ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errores as $error) {
                                echo $error . "<br>";
                            } ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?ctl=registro" method="post">
                        <div class="form-group mb-3">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $params['nombre'] ?>" placeholder="Nombre">
                        </div>
                        <div class="form-group mb-3">
                            <label for="apellido">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $params['apellido'] ?>" placeholder="Apellido">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombreUsuario">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" value="<?php echo $params['nombreUsuario'] ?>" placeholder="Nombre de usuario">
                        </div>
                        <div class="form-group mb-3">
                            <label for="contrasenya">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenya" name="contrasenya" value="<?php echo $params['contrasenya'] ?>" placeholder="Contraseña">
                            <small class="form-text text-muted">La contraseña debe contener al menos 1 letra mayúscula, 1 número y tener un tamaño mínimo de 8 caracteres.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nivel_usuario">Nivel de Usuario</label>
                            <select class="form-select" id="nivel_usuario" name="nivel_usuario">
                                <option value="1" <?php if ($params['nivel_usuario'] == 1) echo 'selected'; ?>>Usuario</option>
                                <option value="2" <?php if ($params['nivel_usuario'] == 2) echo 'selected'; ?>>Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="bRegistro">Aceptar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $contenido = ob_get_clean() ?>
<?php include 'layout.php' ?>
