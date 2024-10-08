<form method="POST" action="index.php?ctl=guardarPreferencias">
    <h3>Preferencias de visualización</h3>

    <!-- Preferencias de resultados por página para gastos -->
    <label for="resultados_por_pagina_gastos">Resultados por página (Gastos):</label>
    <input type="number" name="resultados_por_pagina_gastos" id="resultados_por_pagina_gastos" value="<?= isset($resultados_por_pagina_gastos) ? $resultados_por_pagina_gastos : 10 ?>" required>

    <!-- Preferencias de resultados por página para ingresos -->
    <label for="resultados_por_pagina_ingresos">Resultados por página (Ingresos):</label>
    <input type="number" name="resultados_por_pagina_ingresos" id="resultados_por_pagina_ingresos" value="<?= isset($resultados_por_pagina_ingresos) ? $resultados_por_pagina_ingresos : 10 ?>" required>

    <button type="submit">Guardar</button>
</form>

