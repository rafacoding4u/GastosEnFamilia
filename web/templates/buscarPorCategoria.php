<?php ob_start(); ?>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if (isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
        <?php endif; ?>
    </div>
</div>

<div class="col-md-12">
    <?php
    // array de errores
    if (!isset($errores)) {
        $errores = [];
    }
    foreach ($errores as $error) { ?>
        <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error . "<br>"; ?></span></b>
    <?php } ?>
</div>

<div class="container-fluid text-center">
    <div class="container">
        <form action="index.php?ctl=buscarPorCategoria" method="post">
            <p>Categoría del producto: 
                <select name="categoria">
                    <?php foreach ($params['categorias'] as $categoria) { ?>
                        <option value="<?php echo $categoria['nombreCategoria']; ?>"><?php echo $categoria['nombreCategoria']; ?></option>
                    <?php } ?>
                </select>
            <br></p>
            <input type="submit" name="buscarPorCategoria" value="Buscar"><br>
        </form>
    </div>
</div>

<?php if (isset($params['productos'])) { ?>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['productos'] as $producto) { ?>
                    <tr>
                        <td><?php echo $producto['nombre'] ?></td>
                        <td><?php echo $producto['nombreCategoria'] ?></td>
                        <td><?php echo $producto['precio'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>

