<div class="container p-4">
    <h2>Generar Reporte Financiero</h2>

    <!-- Formulario para generar reportes -->
    <form action="index.php?ctl=generarReporte" method="POST">
        <!-- Filtro por rango de fechas -->
        <div class="form-group">
            <label for="fechaInicio">Fecha de Inicio:</label>
            <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fechaFin">Fecha de Fin:</label>
            <input type="date" id="fechaFin" name="fechaFin" class="form-control" required>
        </div>

        <!-- Filtro por categoría -->
        <div class="form-group">
            <label for="idCategoria">Categoría:</label>
            <select id="idCategoria" name="idCategoria" class="form-control">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
                        <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filtro por tipo de transacción (ingreso o gasto) -->
        <div class="form-group">
            <label for="tipoTransaccion">Tipo de Transacción:</label>
            <select id="tipoTransaccion" name="tipoTransaccion" class="form-control">
                <option value="">Ambos</option>
                <option value="ingreso">Ingresos</option>
                <option value="gasto">Gastos</option>
            </select>
        </div>

        <!-- Filtro por usuario, familia o grupo (opcional según el rol del usuario) -->
        <?php if ($_SESSION['nivel_usuario'] === 'superadmin' || $_SESSION['nivel_usuario'] === 'admin'): ?>
            <div class="form-group">
                <label for="tipoFiltro">Filtrar por:</label>
                <select id="tipoFiltro" name="tipoFiltro" class="form-control">
                    <option value="">Sin filtro</option>
                    <option value="usuario">Usuario</option>
                    <option value="familia">Familia</option>
                    <option value="grupo">Grupo</option>
                </select>
            </div>
        <?php endif; ?>

        <!-- Selección de usuario, familia o grupo -->
        <div id="filtroUsuario" class="form-group" style="display:none;">
            <label for="idUsuario">Seleccionar Usuario:</label>
            <select id="idUsuario" name="idUsuario" class="form-control">
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= htmlspecialchars($usuario['idUser']) ?>">
                        <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="filtroFamilia" class="form-group" style="display:none;">
            <label for="idFamilia">Seleccionar Familia:</label>
            <select id="idFamilia" name="idFamilia" class="form-control">
                <?php foreach ($familias as $familia): ?>
                    <option value="<?= htmlspecialchars($familia['idFamilia']) ?>">
                        <?= htmlspecialchars($familia['nombre_familia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="filtroGrupo" class="form-group" style="display:none;">
            <label for="idGrupo">Seleccionar Grupo:</label>
            <select id="idGrupo" name="idGrupo" class="form-control">
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?= htmlspecialchars($grupo['idGrupo']) ?>">
                        <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botón para generar el reporte -->
        <button type="submit" class="btn btn-primary mt-3">Generar Reporte</button>
    </form>
</div>

<!-- JavaScript para mostrar u ocultar los filtros según el tipo de filtro seleccionado -->
<script>
    document.getElementById('tipoFiltro').addEventListener('change', function() {
        var filtro = this.value;
        document.getElementById('filtroUsuario').style.display = (filtro === 'usuario') ? 'block' : 'none';
        document.getElementById('filtroFamilia').style.display = (filtro === 'familia') ? 'block' : 'none';
        document.getElementById('filtroGrupo').style.display = (filtro === 'grupo') ? 'block' : 'none';
    });
</script>
