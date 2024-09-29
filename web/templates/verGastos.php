<!-- Filtros de búsqueda -->
<form method="GET" action="index.php">
    <input type="hidden" name="ctl" value="FinanzasController&action=verGastos">

    <div class="row">
        <!-- Filtro de fecha -->
        <div class="col">
            <label for="fechaInicio">Desde:</label>
            <input type="date" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio) ?>" class="form-control">
        </div>
        <div class="col">
            <label for="fechaFin">Hasta:</label>
            <input type="date" id="fechaFin" name="fechaFin" value="<?= htmlspecialchars($fechaFin) ?>" class="form-control">
        </div>

        <!-- Filtro de categoría -->
        <div class="col">
            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" class="form-control">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['idCategoria']) ?>" <?= ($categoriaSeleccionada == $cat['idCategoria']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filtro de origen -->
        <div class="col">
            <label for="origen">Origen:</label>
            <select id="origen" name="origen" class="form-control">
                <option value="">Todos</option>
                <option value="banco" <?= ($origenSeleccionado == 'banco') ? 'selected' : '' ?>>Banco</option>
                <option value="efectivo" <?= ($origenSeleccionado == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
            </select>
        </div>

        <!-- Botón de enviar -->
        <div class="col">
            <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
        </div>
    </div>
</form>

<!-- Botón para añadir un nuevo gasto -->
<div class="mt-3 mb-3">
    <a href="index.php?ctl=FinanzasController&action=formInsertarGasto" class="btn btn-success">Añadir Gasto</a>
</div>

<!-- Mostrar la lista de gastos -->
<?php if (!empty($gastos)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Origen</th>
                <th>Concepto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gastos as $gasto): ?>
                <tr>
                    <td><?= htmlspecialchars($gasto['nombreCategoria']) ?></td>
                    <td><?= number_format($gasto['importe'], 2, ',', '.') ?> €</td>
                    <td><?= htmlspecialchars($gasto['fecha']) ?></td>
                    <td><?= htmlspecialchars($gasto['origen']) ?></td>
                    <td><?= htmlspecialchars($gasto['concepto']) ?></td>
                    <td>
                        <a href="index.php?ctl=FinanzasController&action=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="index.php?ctl=FinanzasController&action=eliminarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este gasto?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay gastos registrados.</p>
<?php endif; ?>

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?ctl=FinanzasController&action=verGastos&pagina=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
