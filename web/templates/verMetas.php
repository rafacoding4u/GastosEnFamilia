<div class="container p-4">
    <h2>Metas Financieras</h2>

    <!-- Verificar si el usuario tiene permisos para crear nuevas metas -->
    <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
        <a href="index.php?ctl=formCrearMeta" class="btn btn-success mb-3">Añadir Nueva Meta</a>
    <?php endif; ?>

    <!-- Verificar si hay metas registradas -->
    <?php if (!empty($metas)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre de la Meta</th>
                    <th>Monto Objetivo (€)</th>
                    <th>Monto Alcanzado (€)</th>
                    <th>Fecha Límite</th>
                    <th>Progreso</th>
                    <th>Acciones</th>
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
                                $progreso = ($meta['montoObjetivo'] > 0) ? ($meta['montoAlcanzado'] / $meta['montoObjetivo']) * 100 : 0;
                                $color = ($progreso >= 100) ? 'green' : 'blue';
                            ?>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?= $progreso ?>%; background-color: <?= $color ?>;">
                                    <?= number_format($progreso, 2) ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
                                <a href="index.php?ctl=editarMeta&id=<?= htmlspecialchars($meta['idMeta']); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarMeta&id=<?= htmlspecialchars($meta['idMeta']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta meta?');">Eliminar</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay metas financieras registradas.</p>
    <?php endif; ?>
</div>
