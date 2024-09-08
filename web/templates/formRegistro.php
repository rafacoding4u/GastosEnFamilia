<?php ob_start() ?>

<div class="container text-center p-4">
    <div class="col-md-12" id="cabecera">
        <h1 class="h1Inicio">REGISTRARSE</h1>
    </div>
</div>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if (isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
        <?php endif; ?>
    </div>
    <div class="col-md-12">
        <?php foreach ($errores as $error) { ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error . "<br>"; ?></span></b>
        <?php } ?>
    </div>
</div>

<div class="container text-center p-1">
<form action="index.php?ctl=registro" method="post" name="formRegistro">
    <p>* <input type="text" name="nombre" value="<?php echo $params['nombre'] ?>" placeholder="Nombre"> <br></p>
    <p>* <input type="text" name="apellido" value="<?php echo $params['apellido'] ?>" placeholder="Apellido"><br></p>
    <p>* <input type="text" name="nombreUsuario" value="<?php echo $params['nombreUsuario'] ?>" placeholder="Nombre de usuario"><br></p>
    <p>* <input type="password" name="contrasenya" value="<?php echo $params['contrasenya'] ?>" placeholder="Contraseña"><br></p>
    <p><small>La contraseña debe contener al menos 1 letra mayúscula, 1 número y tener un tamaño mínimo de 8 caracteres.</small></p>
    <p>* <select name="nivel_usuario">
        <option value="1">Usuario</option>
        <option value="2">Administrador</option>
    </select></p>
    <input type="submit" name="bRegistro" value="Aceptar"><br>
</form>
</div>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>



