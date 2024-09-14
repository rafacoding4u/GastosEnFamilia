<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Ingresos</h2>

    <!-- Mostrar un mensaje informativo si hay algún mensaje -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Verificar si hay ingresos para mostrar -->
    <?php if (isset($params['ingresos']) && count($params['ingresos']) > 0): ?>
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
                <?php foreach ($params['ingresos'] as $ingreso): ?>
                    <tr>
                        <td><?= htmlspecialchars($ingreso['nombreCategoria']) ?></td> <!-- Mostrar el nombre de la categoría -->
                        <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                        <td><?= number_format($ingreso['importe'], 2, ',', '.') ?> €</td> <!-- Formato de importe -->
                        <td>
                            <?= htmlspecialchars($ingreso['origen']) === 'banco' ? '🏦 Banco' : '💵 Efectivo' ?> <!-- Representar el origen con íconos -->
                        </td>
                        <td><?= htmlspecialchars($ingreso['fecha']) ?></td> <!-- Fecha del ingreso -->
                        <td>
                            <!-- Botones para editar o eliminar el ingreso -->
                            <a href="index.php?ctl=editarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?ctl=eliminarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este ingreso?')">Eliminar</a>
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
