<?php ob_start() ?>

<div class="container">
    <form name="formBusquedaNombre" action="index.php?ctl=buscarPorNombre" method="POST">
        <table>
            <tr>
                <td>Nombre del producto:</td>
                <td><input type="text" name="nombre" value="<?php echo $params['nombre'] ?>"></td>
                <td><input type="submit" name="buscarPorNombre" value="Buscar"></td>
            </tr>
        </table>
    </form>
</div>

<?php if (isset($params['mensaje'])) {
    echo $params['mensaje'];
}
if (count($params['productos']) > 0) : ?>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <p></p>
            </div>
            <div class="col-md-4">
                <table border="1" cellpadding="10">
                    <tr align="center">
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                    </tr>
                    <?php foreach ($params['productos'] as $producto) : ?>
                        <tr align="center">
                            <td><?php echo $producto['nombre']; ?></td>
                            <td><?php echo $producto['categoria'] ?></td>
                            <td><?php echo $producto['precio'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="col-md-4">
                <p></p>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>

