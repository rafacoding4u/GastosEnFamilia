<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FinanzasController
{
    // Ver Gastos
    public function verGastos()
    {
        $m = new GastosModelo();

        // Parámetros de filtro
        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;
        $origen = recoge('origen') ?: null;

        // Parámetros de paginación
        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $registrosPorPagina = 10;
        $offset = ($paginaActual - 1) * $registrosPorPagina;

        // Obtener los gastos aplicando los filtros y la paginación
        $gastos = $m->obtenerGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen, $offset, $registrosPorPagina);

        // Obtener el número total de gastos para la paginación
        $totalGastos = $m->contarGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen);
        $totalPaginas = ceil($totalGastos / $registrosPorPagina);

        // Pasar las categorías a la vista
        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gastos' => $gastos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'categoriaSeleccionada' => $categoria,
            'origenSeleccionado' => $origen
        );

        $this->render('verGastos.php', $params);
    }

    // Ver Ingresos
    public function verIngresos()
    {
        $m = new GastosModelo();

        // Parámetros de filtro
        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;

        // Parámetros de paginación
        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $registrosPorPagina = 10;
        $offset = ($paginaActual - 1) * $registrosPorPagina;

        // Obtener los ingresos aplicando los filtros y la paginación
        $ingresos = $m->obtenerIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, null, $offset, $registrosPorPagina);

        // Obtener el número total de ingresos para la paginación
        $totalIngresos = $m->contarIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria);
        $totalPaginas = ceil($totalIngresos / $registrosPorPagina);

        // Pasar las categorías a la vista
        $categorias = $m->obtenerCategoriasIngresos();

        $params = array(
            'ingresos' => $ingresos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'categoriaSeleccionada' => $categoria
        );

        $this->render('verIngresos.php', $params);
    }

    // Insertar Gasto
    public function insertarGasto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarGasto'])) {
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarGasto($_SESSION['usuario']['id'], $monto, $categoria, $concepto, $origen)) {
                header('Location: index.php?ctl=verGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el gasto.';
            }
        }
        $this->formInsertarGasto();
    }

    // Insertar Ingreso
    public function insertarIngreso()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarIngreso'])) {
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarIngreso($_SESSION['usuario']['id'], $monto, $categoria, $concepto, $origen)) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el ingreso.';
            }
        }
        $this->formInsertarIngreso();
    }

    // Formulario para insertar gasto
    public function formInsertarGasto()
    {
        $m = new GastosModelo();
        $params = array(
            'categorias' => $m->obtenerCategoriasGastos(),
            'mensaje' => ''
        );
        $this->render('formInsertarGasto.php', $params);
    }

    // Formulario para insertar ingreso
    public function formInsertarIngreso()
    {
        $m = new GastosModelo();
        $params = array(
            'categorias' => $m->obtenerCategoriasIngresos(),
            'mensaje' => ''
        );
        $this->render('formInsertarIngreso.php', $params);
    }

    // Editar Gasto
    public function editarGasto()
    {
        $m = new GastosModelo();

        if (isset($_GET['id'])) {
            $gasto = $m->obtenerGastoPorId($_GET['id']);

            if (!$gasto) {
                header('Location: index.php?ctl=verGastos');
                exit();
            }
        }

        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gasto' => $gasto,
            'categorias' => $categorias
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGasto'])) {
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $fecha = recoge('fecha');
            $origen = recoge('origen');
            $categoria = recoge('categoria');

            if ($m->actualizarGasto($gasto['idGasto'], $concepto, $importe, $fecha, $origen, $categoria)) {
                header('Location: index.php?ctl=verGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar el gasto. Inténtalo de nuevo.';
            }
        }

        $this->render('formEditarGasto.php', $params);
    }

    // Editar Ingreso
public function editarIngreso()
{
    $m = new GastosModelo();

    // Validar si se proporciona un ID válido
    if (isset($_GET['id'])) {
        $ingreso = $m->obtenerIngresoPorId($_GET['id']);

        // Si el ingreso no existe, redirigir
        if (!$ingreso) {
            header('Location: index.php?ctl=verIngresos');
            exit();
        }
    } else {
        // Si no se proporciona un ID, redirigir a la lista de ingresos
        header('Location: index.php?ctl=verIngresos');
        exit();
    }

    // Obtener las categorías de ingresos disponibles
    $categorias = $m->obtenerCategoriasIngresos();

    $params = array(
        'ingreso' => $ingreso,
        'categorias' => $categorias
    );

    // Verificar si se ha enviado el formulario para editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarIngreso'])) {
        $concepto = recoge('concepto');
        $importe = recoge('importe');
        $fecha = recoge('fecha');
        $origen = recoge('origen');
        $categoria = recoge('idCategoria'); // Asegurarse de recoger correctamente 'idCategoria'

        // Validar si el ID de categoría es válido
        if (empty($categoria)) {
            $params['mensaje'] = 'La categoría es obligatoria.';
        } else {
            // Actualizar el ingreso si los datos son válidos
            if ($m->actualizarIngreso($ingreso['idIngreso'], $importe, $categoria, $concepto, $origen)) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar el ingreso. Inténtalo de nuevo.';
            }
        }
    }

    // Renderizar el formulario de edición con los parámetros
    $this->render('formEditarIngreso.php', $params);
}


    // Eliminar Gasto
    public function eliminarGasto()
    {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            if ($m->eliminarGasto($_GET['id'])) {
                header('Location: index.php?ctl=verGastos');
            } else {
                $params['mensaje'] = 'No se pudo eliminar el gasto.';
                $this

->verGastos();
            }
        }
    }

    // Eliminar Ingreso
    public function eliminarIngreso()
    {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            if ($m->eliminarIngreso($_GET['id'])) {
                header('Location: index.php?ctl=verIngresos');
            } else {
                $params['mensaje'] = 'No se pudo eliminar el ingreso.';
                $this->verIngresos();
            }
        }
    }

    // Método para renderizar vistas
    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }
}
