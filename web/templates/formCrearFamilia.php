<div class="container p-4">
    <h2>Crear Nueva Familia</h2>

    <form action="index.php?ctl=crearFamilia" method="post">
        <div class="form-group">
            <label for="nombre_familia">Nombre de la Familia</label>
            <input type="text" class="form-control" id="nombre_familia" name="nombre_familia" required>
        </div>
        <div class="form-group">
            <label for="password_familia">Contraseña de la Familia</label>
            <input type="password" class="form-control" id="password_familia" name="password_familia" required>
        </div>
        <button type="submit" name="bCrearFamilia" class="btn btn-primary">Crear Familia</button>
    </form>
</div>
