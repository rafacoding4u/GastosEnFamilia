<div class="container p-4">
    <h2>Historial de Finanzas</h2>

    <!-- Formulario de filtros -->
    <form action="index.php?ctl=verHistorialFinanzas" method="GET">
        <div class="row">
            <!-- Filtro por fecha -->
            <div class="col-md-4">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin" name="fechaFin" class="form-control" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
            </div>

            <!-- Filtro por categoría -->
            <div class="col-md-4">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>" <?= isset($categoriaSeleccionada) && $categoriaSeleccionada == $categoria['idCategoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Filtro por tipo de transacción -->
            <div class="col-md-4">
                <label for="tipoTransaccion">Tipo de Transacción:</label>
                <select id="tipoTransaccion" name="tipoTransaccion" class="form-control">
                    <option value="">Todos</option>
                    <option value="ingreso" <?= isset($tipoTransaccion) && $tipoTransaccion == 'ingreso' ? 'selected' : '' ?>>Ingresos</option>
                    <option value="gasto" <?= isset($tipoTransaccion) && $tipoTransaccion == 'gasto' ? 'selected' : '' ?>>Gastos</option>
                </select>
            </div>

            <!-- Botón de envío -->
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Mostrar historial de transacciones -->
    <?php if (!empty($transacciones)): ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacciones as $transaccion): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaccion['fecha']) ?></td>
                        <td><?= htmlspecialchars($transaccion['tipoTransaccion']) == 'ingreso' ? 'Ingreso' : 'Gasto' ?></td>
                        <td><?= htmlspecialchars($transaccion['nombreCategoria']) ?></td>
                        <td><?= htmlspecialchars($transaccion['concepto']) ?></td>
                        <td><?= number_format($transaccion['importe'], 2, ',', '.') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron transacciones en el historial.</p>
    <?php endif; ?>
</div>
