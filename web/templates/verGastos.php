<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Gastos</h2>

    <?php if (isset($gastos) && count($gastos) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                    <tr>
                        <td><?= htmlspecialchars($gasto['concepto']) ?></td>
                        <td><?= htmlspecialchars($gasto['cantidad']) ?></td>
                        <td><?= htmlspecialchars($gasto['fecha']) ?></td>
                        <td>
                            <a href="index.php?ctl=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?ctl=eliminarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-danger">Eliminar</a>
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

