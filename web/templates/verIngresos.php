<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Ingresos</h2>

    <?php if (isset($ingresos) && count($ingresos) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Concepto</th>
                    <th>Importe (€)</th>
                    <th>Origen</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr>
                        <td><?= htmlspecialchars($ingreso['nombreCategoria']) ?></td> <!-- Mostrar el nombre de la categoría -->
                        <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                        <td><?= number_format($ingreso['importe'], 2, ',', '.') ?> €</td> <!-- Formato de importe -->
                        <td>
                            <?= htmlspecialchars($ingreso['origen']) === 'banco' ? '🏦 Banco' : '💵 Efectivo' ?> <!-- Representar el origen con íconos -->
                        </td>
                        <td><?= htmlspecialchars($ingreso['fecha']) ?></td>
                        <td>
                            <a href="index.php?ctl=editarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?ctl=eliminarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay ingresos registrados.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

