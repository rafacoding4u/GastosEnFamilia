<div class="container p-4">
    <h2>Situación Financiera</h2>

    <!-- Selección de tipo de situación -->
    <form method="GET" action="index.php">
        <input type="hidden" name="ctl" value="verSituacion">

        <div class="form-group">
            <label for="tipo">Ver situación de:</label>
            <select name="tipo" id="tipo" class="form-control" onchange="this.form.submit()">
                <?php if ($_SESSION['nivel_usuario'] === 'superadmin'): ?>
                    <option value="global" <?= $tipo === 'global' ? 'selected' : '' ?>>Global</option>
                <?php endif; ?>
                <option value="familia" <?= $tipo === 'familia' ? 'selected' : '' ?>>Familia</option>
                <option value="grupo" <?= $tipo === 'grupo' ? 'selected' : '' ?>>Grupo</option>
                <option value="usuario" <?= $tipo === 'usuario' ? 'selected' : '' ?>>Usuario</option>
            </select>
        </div>

        <!-- Condicional para mostrar familias, grupos o usuarios según la selección -->
        <?php if ($tipo === 'familia' && !empty($familias)): ?>
            <div class="form-group">
                <label for="idSeleccionado">Selecciona una familia:</label>
                <select name="idSeleccionado" id="idSeleccionado" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?= $familia['idFamilia'] ?>" <?= $idSeleccionado == $familia['idFamilia'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($familia['nombre_familia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php elseif ($tipo === 'grupo' && !empty($grupos)): ?>
            <div class="form-group">
                <label for="idSeleccionado">Selecciona un grupo:</label>
                <select name="idSeleccionado" id="idSeleccionado" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= $grupo['idGrupo'] ?>" <?= $idSeleccionado == $grupo['idGrupo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php elseif ($tipo === 'usuario' && !empty($usuariosLista)): ?>
            <div class="form-group">
                <label for="idSeleccionado">Selecciona un usuario:</label>
                <select name="idSeleccionado" id="idSeleccionado" class="form-control" onchange="this.form.submit()">
                    <option value="todos" <?= $idSeleccionado == 'todos' ? 'selected' : '' ?>>Todos los usuarios</option>
                    <?php foreach ($usuariosLista as $usuario): ?>
                        <option value="<?= $usuario['idUser'] ?>" <?= $idSeleccionado == $usuario['idUser'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else: ?>
            <p>No hay datos disponibles para mostrar en esta sección.</p>
        <?php endif; ?>
    </form>

    <!-- Resumen Financiero -->
    <?php if (!empty($situacion)): ?>
        <h4>Resumen Financiero</h4>
        <p>Total Ingresos: <span class="bg-success text-white px-2"><?= number_format($situacion['totalIngresos'], 2, ',', '.') ?> €</span></p>
        <p>Total Gastos: <span class="bg-danger text-white px-2"><?= number_format($situacion['totalGastos'], 2, ',', '.') ?> €</span></p>
        <p>Saldo: 
            <span class="px-2" style="color: white; background-color: <?= $situacion['totalIngresos'] - $situacion['totalGastos'] > 0 ? 'green' : ($situacion['totalIngresos'] - $situacion['totalGastos'] < 0 ? 'red' : 'gray') ?>;">
                <?= number_format($situacion['totalIngresos'] - $situacion['totalGastos'], 2, ',', '.') ?> €
            </span>
        </p>
    <?php else: ?>
        <p>No hay datos financieros disponibles.</p>
    <?php endif; ?>

    <!-- Mostrar usuarios si los hay -->
    <?php if (isset($usuarios) && !empty($usuarios)): ?>
        <h4>Usuarios</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Total Ingresos</th>
                    <th>Total Gastos</th>
                    <th>Saldo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><span class="bg-success text-white px-2"><?= number_format($usuario['totalIngresos'] ?? 0, 2, ',', '.') ?> €</span></td>
                        <td><span class="bg-danger text-white px-2"><?= number_format($usuario['totalGastos'] ?? 0, 2, ',', '.') ?> €</span></td>
                        <td>
                            <span class="px-2" style="color: white; background-color: <?= ($usuario['saldo'] > 0) ? 'green' : ($usuario['saldo'] < 0 ? 'red' : 'gray') ?>;">
                                <?= number_format($usuario['saldo'] ?? 0, 2, ',', '.') ?> €
                            </span>
                        </td>
                        <td>
                            <!-- Control de visibilidad de acciones según el nivel de usuario -->
                            <?php if ($_SESSION['nivel_usuario'] !== 'superadmin' && $usuario['nivel_usuario'] !== 'superadmin'): ?>
                                <button class="btn btn-info toggle-details" data-id="<?= $usuario['idUser'] ?>">
                                    Mostrar detalles
                                </button>
                            <?php elseif ($_SESSION['nivel_usuario'] === 'superadmin'): ?>
                                <button class="btn btn-info toggle-details" data-id="<?= $usuario['idUser'] ?>">
                                    Mostrar detalles
                                </button>
                            <?php else: ?>
                                <button class="btn btn-info" disabled>No disponible</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="detalles-usuario" id="detallesUsuario<?= $usuario['idUser'] ?>" style="display: none;">
                        <td colspan="6">
                            <!-- Detalles de ingresos -->
                            <h5>Ingresos</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="col-concepto">Concepto</th>
                                        <th>Importe</th>
                                        <th>Fecha</th>
                                        <th>Origen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($usuario['detalles_ingresos']) && count($usuario['detalles_ingresos']) > 0): ?>
                                        <?php foreach ($usuario['detalles_ingresos'] as $ingreso): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ingreso['concepto']) ?></td>
                                                <td><?= number_format($ingreso['importe'], 2, ',', '.') ?> €</td>
                                                <td><?= htmlspecialchars($ingreso['fecha']) ?></td>
                                                <td><?= htmlspecialchars($ingreso['origen']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No hay ingresos registrados para este usuario.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Detalles de gastos -->
                            <h5>Gastos</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="col-concepto">Concepto</th>
                                        <th>Importe</th>
                                        <th>Fecha</th>
                                        <th>Origen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($usuario['detalles_gastos']) && count($usuario['detalles_gastos']) > 0): ?>
                                        <?php foreach ($usuario['detalles_gastos'] as $gasto): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($gasto['concepto']) ?></td>
                                                <td><?= number_format($gasto['importe'], 2, ',', '.') ?> €</td>
                                                <td><?= htmlspecialchars($gasto['fecha']) ?></td>
                                                <td><?= htmlspecialchars($gasto['origen']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No hay gastos registrados para este usuario.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- JavaScript para manejar el despliegue/ocultamiento de detalles -->
<script>
    document.querySelectorAll('.toggle-details').forEach(function(button) {
        button.addEventListener('click', function() {
            const userId = button.getAttribute('data-id');
            const detallesRow = document.getElementById('detallesUsuario' + userId);

            // Mostrar/ocultar la fila de detalles
            if (detallesRow.style.display === 'none' || detallesRow.style.display === '') {
                detallesRow.style.display = 'table-row'; // Mostrar detalles
                button.textContent = 'Ocultar detalles';
            } else {
                detallesRow.style.display = 'none'; // Ocultar detalles
                button.textContent = 'Mostrar detalles';
            }
        });
    });
</script>
