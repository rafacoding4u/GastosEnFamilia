<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Lista de Familias</h2>

    <?php if (!empty($familias)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Familia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($familias as $familia): ?>
                    <tr>
                        <td><?= htmlspecialchars($familia['idFamilia']) ?></td>
                        <td><?= htmlspecialchars($familia['nombre_familia']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay familias registradas.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
