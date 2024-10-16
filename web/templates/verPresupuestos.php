<div class="container p-4">
    <h2>Presupuestos</h2>

    <!-- Mostrar mensaje de éxito o error, si existe -->
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Mostrar los presupuestos creados -->
    <?php if (!empty($presupuestos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Presupuesto</th>
                    <th>Categoría</th>
                    <th>Límite Mensual (€)</th>
                    <th>Gasto Actual (€)</th>
                    <th>Restante (€)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presupuestos as $presupuesto): ?>
                    <tr>
                        <td><?= htmlspecialchars($presupuesto['nombrePresupuesto']) ?></td>
                        <td><?= htmlspecialchars($presupuesto['nombreCategoria']) ?></td>
                        <td><?= number_format($presupuesto['cantidadLimite'], 2, ',', '.') ?> €</td>
                        <td><?= number_format($presupuesto['gastoActual'], 2, ',', '.') ?> €</td>
                        <td>
                            <?php $restante = $presupuesto['cantidadLimite'] - $presupuesto['gastoActual']; ?>
                            <span style="color: <?= $restante >= 0 ? 'green' : 'red' ?>">
                                <?= number_format($restante, 2, ',', '.') ?> €
                            </span>
                        </td>
                        <td>
                            <!-- Solo mostrar botones de edición y eliminación a admin o superadmin -->
                            <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
                                <a href="index.php?ctl=editarPresupuesto&id=<?= htmlspecialchars($presupuesto['idPresupuesto']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarPresupuesto&id=<?= htmlspecialchars($presupuesto['idPresupuesto']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este presupuesto?')">Eliminar</a>
                            <?php else: ?>
                                <span class="text-muted">Sin acciones</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay presupuestos establecidos actualmente.</p>
    <?php endif; ?>
</div>