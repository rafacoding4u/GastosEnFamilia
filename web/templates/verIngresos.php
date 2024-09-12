<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Ingresos</h2>

    <?php if (isset($ingresos) && count($ingresos) > 0): ?>
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
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr>
                        <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                        <td><?= htmlspecialchars($ingreso['cantidad']) ?></td>
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
