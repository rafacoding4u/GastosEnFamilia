<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Añadir Gasto</h2>

    <form action="index.php?ctl=insertarGasto" method="post">
        <div class="form-group">
            <label for="concepto">Concepto:</label>
            <input type="text" id="concepto" name="concepto" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" required>
        </div>

        <button type="submit" name="bInsertarGasto" class="btn btn-primary mt-3">Añadir Gasto</button>

        <?php if (isset($params['mensaje'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($params['mensaje']) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include 'footer.php'; ?>

