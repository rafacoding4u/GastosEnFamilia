<div class="container p-4">
    <h2>Lista de Ingresos</h2>

    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="verIngresos">

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
                        <option value="<?= htmlspecialchars($cat['idCategoria']) ?>" <?= (isset($categoriaSeleccionada) && $categoriaSeleccionada == $cat['idCategoria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombreCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label for="origen">Origen:</label>
                <select id="origen" name="origen" class="form-control">
                    <option value="">Todos</option>
                    <option value="banco" <?= (isset($origenSeleccionado) && $origenSeleccionado == 'banco') ? 'selected' : '' ?>>Banco</option>
                    <option value="efectivo" <?= (isset($origenSeleccionado) && $origenSeleccionado == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
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

    <!-- Botón para añadir un nuevo ingreso (visible para admin, superadmin, y usuario regular) -->
    <?php if (in_array($_SESSION['usuario']['nivel_usuario'], ['admin', 'superadmin', 'usuario'])): ?>
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
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr>
                        <td><?= htmlspecialchars($ingreso['nombreCategoria'] ?? 'Sin categoría') ?></td>
                        <td><?= number_format($ingreso['importe'] ?? 0, 2, ',', '.') ?> €</td>
                        <td><?= htmlspecialchars($ingreso['fecha'] ?? 'Sin fecha') ?></td>
                        <td><?= htmlspecialchars($ingreso['origen'] ?? 'No especificado') ?></td>
                        <td><?= htmlspecialchars($ingreso['concepto'] ?? 'Sin concepto') ?></td>

                        <!-- Asignado a -->
                        <td>
                            <?php
                            $tipoAsignacion = 'Individual';
                            if (strpos($ingreso['nombre_asociacion'], 'Familia') !== false) {
                                $tipoAsignacion = 'Familia';
                            } elseif (strpos($ingreso['nombre_asociacion'], 'Grupo') !== false) {
                                $tipoAsignacion = 'Grupo';
                            }
                            echo $tipoAsignacion;
                            ?>
                        </td>

                        <!-- Nombre -->
                        <td><?= htmlspecialchars($ingreso['nombre_asociacion'] ?? 'No especificado') ?></td>

                        <td>
                            <!-- Mostrar acciones según los permisos del usuario -->
                            <?php if ($_SESSION['usuario']['nivel_usuario'] === 'admin' || $_SESSION['usuario']['nivel_usuario'] === 'superadmin' || $_SESSION['usuario']['id'] === $ingreso['idUser']): ?>
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
</div>