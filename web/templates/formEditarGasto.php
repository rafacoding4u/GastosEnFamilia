<div class="container p-4">
    <h2>Editar Gasto</h2>

    <form action="index.php?ctl=actualizarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" method="POST">
        <div class="form-group">
            <label for="concepto">Concepto</label>
            <input type="text" class="form-control" name="concepto" value="<?= htmlspecialchars($gasto['concepto']) ?>" required>
        </div>
        <div class="form-group">
            <label for="importe">Importe</label>
            <input type="number" step="0.01" class="form-control" name="importe" value="<?= htmlspecialchars($gasto['importe']) ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?= htmlspecialchars($gasto['fecha']) ?>" required>
        </div>
        <div class="form-group">
            <label for="origen">Origen</label>
            <select class="form-control" name="origen" required>
                <option value="banco" <?= $gasto['origen'] == 'banco' ? 'selected' : '' ?>>Banco</option>
                <option value="efectivo" <?= $gasto['origen'] == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
            </select>
        </div>
        <div class="form-group">
            <label for="categoria">Categoría</label>
            <select class="form-control" name="categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>" <?= $gasto['idCategoria'] == $categoria['idCategoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="bEditarGasto" class="btn btn-primary">Guardar Cambios</button>
    </form>

    <a href="index.php?ctl=verGastos" class="btn btn-secondary mt-3">Cancelar</a>
</div>
