<?php ob_start(); ?>

<div class="container-fluid">
    <div class="container text-center py-2">
        <?php if (isset($_SESSION['mensaje_exito_producto'])) : ?>
            <b><span style="color: rgba(119, 200, 119, 1);"><?php echo $_SESSION['mensaje_exito_producto']; ?></span></b>
            <?php unset($_SESSION['mensaje_exito_producto']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensaje_error_producto'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $_SESSION['mensaje_error_producto']; ?></span></b>
            <?php unset($_SESSION['mensaje_error_producto']); ?>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Listado de Productos</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <?php if ($_SESSION['nivel_usuario'] != 0): ?>
                        <th>Acción</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['productos'] as $producto) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombreCategoria'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format($producto['precio'], 2); ?></td>
                        <?php if ($_SESSION['nivel_usuario'] != 0): ?>
                            <td>
                                <a href="javascript:confirmDelete('index.php?ctl=eliminarProducto&idProducto=<?php echo $producto['idProducto']; ?>')">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        window.location.href = url;
    }
}
</script>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>

