<!-- Mostrar la lista de ingresos -->
<?php if (!empty($ingresos)): ?>
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
            <?php foreach ($ingresos as $ingreso): ?>
                <tr>
                    <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                    <td><?= htmlspecialchars($ingreso['importe']) ?> €</td>
                    <td><?= htmlspecialchars($ingreso['fecha']) ?></td>
                    <td><?= htmlspecialchars($ingreso['origen']) ?></td>
                    <td><?= htmlspecialchars($ingreso['nombreCategoria']) ?></td>
                    <td>
                        <a href="index.php?ctl=verDetalleIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-info btn-sm">Ver Detalle</a>
                        <a href="index.php?ctl=editarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="index.php?ctl=eliminarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este ingreso?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay ingresos registrados.</p>
<?php endif; ?>
