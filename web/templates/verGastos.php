<div class="container p-4">
    <h2>Lista de Gastos</h2>

    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="verGastos">

        <div class="row">
            <div class="col">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>" class="form-control">
            </div>
            <div class="col">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin" name="fechaFin" value="<?= htmlspecialchars($fechaFin ?? '') ?>" class="form-control">
            </div>
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
            <div class="col">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control">
                    <option value="">Todos</option>
                    <option value="banco" <?= ($origenSeleccionado == 'banco') ? 'selected' : '' ?>>Banco</option>
                    <option value="efectivo" <?= ($origenSeleccionado == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
                </select>
            </div>
            <div class="col">
                <label for="asignado">Asignado a:</label>
                <select id="asignado" name="asignado" class="form-control">
                    <option value="">Todos</option>
                    <option value="Familia" <?= (isset($asignadoSeleccionado) && $asignadoSeleccionado == 'Familia') ? 'selected' : '' ?>>Familia</option>
                    <option value="Grupo" <?= (isset($asignadoSeleccionado) && $asignadoSeleccionado == 'Grupo') ? 'selected' : '' ?>>Grupo</option>
                    <option value="Individual" <?= (isset($asignadoSeleccionado) && $asignadoSeleccionado == 'Individual') ? 'selected' : '' ?>>Individual</option>
                </select>
            </div>
            <div class="col">
                <label for="nombre">Nombre:</label>
                <select id="nombre" name="nombre" class="form-control">
                    <option value="">Todos</option>
                    <?php if (isset($nombresDisponibles) && is_array($nombresDisponibles)): ?>
                        <?php foreach ($nombresDisponibles as $nombre): ?>
                            <option value="<?= htmlspecialchars($nombre['nombre_asociacion'] ?? '') ?>" <?= (isset($nombreSeleccionado) && $nombreSeleccionado == $nombre['nombre_asociacion']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nombre['nombre_asociacion'] ?? 'No especificado') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Botón para añadir un nuevo gasto (visible para admin, superadmin, y usuario regular) -->
    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['admin', 'superadmin', 'usuario'])): ?>
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
                    <th style="width: 12%;">Importe</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Concepto</th>
                    <th style="width: 12%;">Asignado a:</th>
                    <th style="width: 18%;">Nombre</th>
                    <th style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                    <tr>
                        <td><?= htmlspecialchars($gasto['nombreCategoria'] ?? 'Sin categoría') ?></td>
                        <td><?= number_format($gasto['importe'] ?? 0, 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($gasto['fecha'] ?? 'Sin fecha') ?></td>
                        <td><?= htmlspecialchars($gasto['origen'] ?? 'No especificado') ?></td>
                        <td><?= htmlspecialchars($gasto['concepto'] ?? 'Sin concepto') ?></td>

                        <!-- Asignado a -->
                        <td>
                            <?php
                            $tipoAsignacion = 'Individual';
                            if (strpos($gasto['nombre_asociacion'], 'Familia') !== false) {
                                $tipoAsignacion = 'Familia';
                            } elseif (strpos($gasto['nombre_asociacion'], 'Grupo') !== false) {
                                $tipoAsignacion = 'Grupo';
                            }
                            echo $tipoAsignacion;
                            ?>
                        </td>

                        <!-- Nombre -->
                        <td><?= htmlspecialchars($gasto['nombre_asociacion'] ?? 'No especificado') ?></td>

                        <td>
                            <!-- Mostrar acciones según los permisos del usuario -->
                            <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin' || $_SESSION['usuario']['id'] === $gasto['idUser']): ?>
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
</div>