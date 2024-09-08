<?php ob_start(); ?>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if (isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
        <?php endif; ?>
    </div>
</div>

<div class="col-md-12">
    <?php if (isset($errores)) : ?>
        <?php foreach ($errores as $error) { ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error . "<br>"; ?></span></b>
        <?php } ?>
    <?php endif; ?>
</div>

<div class="container-fluid text-center">
    <div class="container">
        <?php
        // Genera un token 
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        ?>
        <form action="index.php?ctl=insertarProducto" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <p>* <input type="text" name="nombre" placeholder="Nombre" required><br></p>
            <p>* 
                <select name="categoria" required>
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($params['categorias'] as $categoria) { ?>
                        <option value="<?php echo $categoria['idCategoria']; ?>"><?php echo $categoria['nombreCategoria']; ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>* <input type="number" step="0.01" name="precio" placeholder="Precio" required><br></p>
            <input type="submit" name="bInsertarProducto" value="Aceptar"><br>
        </form>
    </div>
</div>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>
