<div class="container p-4">
    <h2>Registro de Auditoría</h2>

    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="verAuditoria">
        <div class="row">
            <div class="col">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio" name="fechaInicio" class="form-control">
            </div>
            <div class="col">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin" name="fechaFin" class="form-control">
            </div>
            <div class="col">
                <label for="usuario">Usuario:</label>
                <select id="usuario" name="usuario" class="form-control">
                    <option value="">Todos</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['idUser']) ?>">
                            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla con el registro de auditoría -->
    <?php if (!empty($auditoria)): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditoria as $registro): ?>
                    <tr>
                        <td><?= htmlspecialchars($registro['fecha']) ?></td>
                        <td><?= htmlspecialchars($registro['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($registro['accion']) ?></td>
                        <td><?= htmlspecialchars($registro['detalles']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron registros de auditoría.</p>
    <?php endif; ?>
</div>
