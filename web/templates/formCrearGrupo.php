<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Crear Nuevo Grupo</h2>

    <form action="index.php?ctl=crearGrupo" method="post">
        <div class="form-group">
            <label for="nombre_grupo">Nombre del Grupo</label>
            <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" required>
        </div>
        <div class="form-group">
            <label for="password_grupo">Contraseña del Grupo</label>
            <input type="password" class="form-control" id="password_grupo" name="password_grupo" required>
        </div>
        <button type="submit" name="bCrearGrupo" class="btn btn-primary">Crear Grupo</button>
    </form>
</div>

<?php include 'footer.php'; ?>

