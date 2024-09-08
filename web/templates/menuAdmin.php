<h3 class="text">Bienvenido <?php echo $_SESSION['nombreUsuario'] ?></h3>
<div class="container-fluid menu text-center p-3 my-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <a href="index.php?ctl=home" class="p-3">INICIO</a>
                <a href="index.php?ctl=listarProductos" class="p-3">PRODUCTOS</a>
                <a href="index.php?ctl=buscarPorNombre" class="p-3">BUSCAR POR NOMBRE</a>
                <a href="index.php?ctl=buscarPorCategoria" class="p-3">BUSCAR POR CATEGORÍA</a>
                <a href="index.php?ctl=buscarPorPrecio" class="p-3">BUSCAR POR PRECIO</a>
                <a href="index.php?ctl=insertarProducto" class="p-3">INSERTAR PRODUCTO</a>
                <a href="index.php?ctl=listarUsuarios" class="p-3">USUARIOS</a>
                <a href="index.php?ctl=salir"><button type="button" class="btn btn-secondary mt-3" style="width: 150px;">CERRAR SESIÓN</button></a>
            </div>
        </div>
    </div>
</div>





