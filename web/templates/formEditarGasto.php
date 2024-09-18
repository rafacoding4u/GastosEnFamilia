<?php include 'layout.php'; ?>

<div class="container p-4">
    <h2>Editar Gasto</h2>

    <form action="index.php?ctl=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" method="POST">
        <div class="form-group">
            <label for="concepto">Concepto</label>
            <input type="text" class="form-control" name="concepto" value="<?= htmlspecialchars($gasto['concepto']) ?>">
        </div>
        <div class="form-group">
            <label for="importe">Importe</label>
            <input type="number" step="0.01" class="form-control" name="importe" value="<?= htmlspecialchars($gasto['importe']) ?>">
        </div>
        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?= htmlspecialchars($gasto['fecha']) ?>">
        </div>
        <div class="form-group">
            <label for="origen">Origen</label>
            <select class="form-control" name="origen">
                <option value="banco" <?= $gasto['origen'] == 'banco' ? 'selected' : '' ?>>Banco</option>
                <option value="efectivo" <?= $gasto['origen'] == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
            </select>
        </div>
        <div class="form-group">
            <label for="categoria">Categoría</label>
            <select class="form-control" name="categoria">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['idCategoria'] ?>" <?= $gasto['idCategoria'] == $categoria['idCategoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="bEditarGasto" class="btn btn-primary">Guardar Cambios</button>
    </form>

    <a href="index.php?ctl=verGastos" class="btn btn-secondary mt-3">Cancelar</a>
</div>

<?php include 'footer.php'; ?>
