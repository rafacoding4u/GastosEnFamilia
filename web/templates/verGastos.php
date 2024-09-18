<!-- Mostrar la lista de gastos -->
<?php if (!empty($gastos)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Origen</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gastos as $gasto): ?>
                <tr>
                    <td><?= htmlspecialchars($gasto['concepto']) ?></td>
                    <td><?= htmlspecialchars($gasto['importe']) ?> €</td>
                    <td><?= htmlspecialchars($gasto['fecha']) ?></td>
                    <td><?= htmlspecialchars($gasto['origen']) ?></td>
                    <td><?= htmlspecialchars($gasto['nombreCategoria']) ?></td>
                    <td>
                        <a href="index.php?ctl=verDetalleGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-info btn-sm">Ver Detalle</a>
                        <a href="index.php?ctl=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="index.php?ctl=eliminarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este gasto?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay gastos registrados.</p>
<?php endif; ?>
