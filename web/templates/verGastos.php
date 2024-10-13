<div class="container p-4">
    <h2>Lista de Gastos</h2>

    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="verGastos">

        <div class="row">
            <!-- Filtro de fecha -->
            <div class="col">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>" class="form-control">
            </div>
            <div class="col">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin" name="fechaFin" value="<?= htmlspecialchars($fechaFin ?? '') ?>" class="form-control">
            </div>

            <!-- Filtro de categoría -->
            <div class="col">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['idCategoria'] ?? '') ?>" <?= ($categoriaSeleccionada == $cat['idCategoria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombreCategoria'] ?? 'Sin categoría') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtro de origen -->
            <div class="col">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control">
                    <option value="">Todos</option>
                    <option value="banco" <?= ($origenSeleccionado == 'banco') ? 'selected' : '' ?>>Banco</option>
                    <option value="efectivo" <?= ($origenSeleccionado == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
                </select>
            </div>

            <!-- Botón de enviar -->
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Botón para añadir un nuevo gasto (solo visible para usuarios con permiso) -->
    <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
        <div class="mt-3 mb-3">
            <a href="index.php?ctl=formInsertarGasto" class="btn btn-success">Añadir Gasto</a>
        </div>
    <?php endif; ?>

    <!-- Mostrar la lista de gastos -->
    <?php if (!empty($gastos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Importe</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Concepto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                    <tr>
                        <td><?= htmlspecialchars($gasto['nombreCategoria'] ?? 'Sin categoría') ?></td>
                        <td><?= number_format($gasto['importe'], 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($gasto['fecha'] ?? '') ?></td>
                        <td><?= htmlspecialchars($gasto['origen'] ?? '') ?></td>
                        <td><?= htmlspecialchars($gasto['concepto'] ?? '') ?></td>
                        <td>
                            <!-- Mostrar acciones según los permisos del usuario -->
                            <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin' || $_SESSION['idUser'] === $gasto['idUsuario']): ?>
                                <a href="index.php?ctl=editarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarGasto&id=<?= htmlspecialchars($gasto['idGasto']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este gasto?')">Eliminar</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay gastos registrados.</p>
    <?php endif; ?>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?ctl=verGastos&pagina=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
