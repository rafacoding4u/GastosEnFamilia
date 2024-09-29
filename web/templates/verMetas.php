<div class="container p-4">
    <h2>Metas Financieras</h2>

    <?php if (!empty($metas)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre de la Meta</th>
                    <th>Monto Objetivo (€)</th>
                    <th>Monto Alcanzado (€)</th>
                    <th>Fecha Límite</th>
                    <th>Progreso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metas as $meta): ?>
                    <tr>
                        <td><?= htmlspecialchars($meta['nombreMeta']) ?></td>
                        <td><?= number_format($meta['montoObjetivo'], 2, ',', '.') ?> €</td>
                        <td><?= number_format($meta['montoAlcanzado'], 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($meta['fechaLimite']) ?></td>
                        <td>
                            <?php 
                                $progreso = ($meta['montoAlcanzado'] / $meta['montoObjetivo']) * 100;
                                $color = ($progreso >= 100) ? 'green' : 'blue';
                            ?>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?= $progreso ?>%; background-color: <?= $color ?>;">
                                    <?= number_format($progreso, 2) ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay metas financieras registradas.</p>
    <?php endif; ?>
</div>
