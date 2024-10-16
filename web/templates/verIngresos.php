<div class="container p-4">
    <h2>Lista de Ingresos</h2>

    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="FinanzasController&action=verIngresos">

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
                        <option value="<?= htmlspecialchars($cat['idCategoria']) ?>" <?= (isset($categoriaSeleccionada) && $categoriaSeleccionada == $cat['idCategoria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtro de origen -->
            <div class="col">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control">
                    <option value="">Todos</option>
                    <option value="banco" <?= (isset($origenSeleccionado) && $origenSeleccionado == 'banco') ? 'selected' : '' ?>>Banco</option>
                    <option value="efectivo" <?= (isset($origenSeleccionado) && $origenSeleccionado == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
                </select>
            </div>

            <!-- Botón de enviar -->
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Botón para añadir un nuevo ingreso (solo para usuarios con permiso) -->
    <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
        <div class="mt-3 mb-3">
            <a href="index.php?ctl=formInsertarIngreso" class="btn btn-success">Añadir Ingreso</a>
        </div>
    <?php endif; ?>

    <!-- Mostrar la lista de ingresos -->
    <?php if (!empty($ingresos)): ?>
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
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr>
                        <!-- Encontrar el nombre de la categoría basado en idCategoria -->
                        <td>
                            <?php
                            $nombreCategoria = 'Sin categoría';
                            foreach ($categorias as $categoria) {
                                if ($categoria['idCategoria'] == $ingreso['idCategoria']) {
                                    $nombreCategoria = htmlspecialchars($categoria['nombreCategoria']);
                                    break;
                                }
                            }
                            echo $nombreCategoria;
                            ?>
                        </td>
                        <td><?= number_format($ingreso['importe'], 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($ingreso['fecha']) ?></td>
                        <td><?= htmlspecialchars($ingreso['origen']) ?></td>
                        <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                        <td>
                            <!-- Acciones de edición y eliminación, solo disponibles para admin y superadmin -->
                            <?php if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin'): ?>
                                <a href="index.php?ctl=editarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?ctl=eliminarIngreso&id=<?= htmlspecialchars($ingreso['idIngreso']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este ingreso?')">Eliminar</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>No permitido</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay ingresos registrados.</p>
    <?php endif; ?>

    <!-- Paginación -->
    <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?ctl=verIngresos&pagina=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
