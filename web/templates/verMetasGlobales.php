<div class="container p-4">
    <h2>Metas Globales de Ahorro</h2>

    <!-- Mostrar mensaje si no hay metas definidas -->
    <?php if (empty($metas)): ?>
        <p>No hay metas de ahorro definidas actualmente.</p>
    <?php else: ?>
        <!-- Mostrar lista de metas si existen -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Meta</th>
                    <th>Monto Objetivo</th>
                    <th>Progreso</th>
                    <th>Fecha Límite</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metas as $meta): ?>
                    <tr>
                        <td><?= htmlspecialchars($meta['nombreMeta'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format($meta['montoObjetivo'], 2, ',', '.') ?> €</td>
                        <td>
                            <!-- Barra de progreso -->
                            <div class="progress">
                                <?php 
                                    $progreso = ($meta['montoObjetivo'] > 0) ? min(100, ($meta['montoActual'] / $meta['montoObjetivo']) * 100) : 0;
                                    $color = $progreso >= 100 ? 'bg-success' : 'bg-primary';
                                ?>
                                <div class="progress-bar <?= $color ?>" role="progressbar" style="width: <?= $progreso ?>%;" aria-valuenow="<?= $progreso ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?= number_format($progreso, 2) ?>%
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($meta['fechaLimite'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>