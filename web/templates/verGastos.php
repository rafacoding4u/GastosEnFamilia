<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Gastos</h2>

    <!-- Mostrar un mensaje informativo si hay algún mensaje -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <!-- Verificar si hay gastos para mostrar -->
    <?php if (isset($params['gastos']) && count($params['gastos']) > 0): ?>
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
                <?php foreach ($params['gastos'] as $gasto): ?>
                    <tr>
                        <td><?= htmlspecialchars($gasto['nombreCategoria']) ?></td> <!-- Mostrar el nombre de la categoría -->
                        <td><?= htmlspecialchars($gasto['concepto']) ?></td>
                        <td><?= number_format($gasto['importe'], 2, ',', '.') ?> €</td> <!-- Formato de importe -->
                        <td>
                            <?= htmlspecialchars($gasto['origen']) === 'banco' ? '🏦 Banco' : '💵 Efectivo' ?> <!-- Representar el origen con íconos -->
                        </td>
                        <td><?= htmlspecialchars($gasto['fecha']) ?></td> <!-- Fecha del gasto -->
                        <td>
                            <!-- Botones para editar o eliminar el gasto -->
                            <a href="index.php?ctl=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?ctl=eliminarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este gasto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay gastos registrados.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
