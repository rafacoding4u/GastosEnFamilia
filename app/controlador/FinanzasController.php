<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FinanzasController
{
    // Ver Gastos
    public function verGastos()
    {
        $m = new GastosModelo();

        $resultadosPorPagina = $m->obtenerPreferenciaUsuario('resultados_por_pagina_gastos', $_SESSION['usuario']['id']) ?? 10;

        if ($resultadosPorPagina <= 0) {
            $resultadosPorPagina = 10;
        }

        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;
        $origen = recoge('origen') ?: null;

        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $offset = ($paginaActual - 1) * $resultadosPorPagina;

        $gastos = $m->obtenerGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen, $offset, $resultadosPorPagina);

        $totalGastos = $m->contarGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen);

        $totalPaginas = ($resultadosPorPagina > 0) ? ceil($totalGastos / $resultadosPorPagina) : 1;

        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gastos' => $gastos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'categoriaSeleccionada' => $categoria,
            'origenSeleccionado' => $origen,
            'resultadosPorPagina' => $resultadosPorPagina
        );

        $this->render('verGastos.php', $params);
    }

    // Ver Metas Globales
    public function verMetasGlobales()
    {
        $m = new GastosModelo();

        // Obtiene las metas globales del modelo
        $metasGlobales = $m->obtenerMetasGlobales();

        // Parámetros a pasar a la vista
        $params = array(
            'metasGlobales' => $metasGlobales
        );

        // Renderiza la vista 'verMetasGlobales.php' con los parámetros
        $this->render('verMetasGlobales.php', $params);
    }

    // Ver Ingresos
    public function verIngresos()
    {
        $m = new GastosModelo();

        $resultadosPorPagina = $m->obtenerPreferenciaUsuario('resultados_por_pagina_ingresos', $_SESSION['usuario']['id']) ?? 10;

        if ($resultadosPorPagina <= 0) {
            $resultadosPorPagina = 10;
        }

        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;

        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $offset = ($paginaActual - 1) * $resultadosPorPagina;

        $ingresos = $m->obtenerIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, null, $offset, $resultadosPorPagina);

        $totalIngresos = $m->contarIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria);

        $totalPaginas = ($resultadosPorPagina > 0) ? ceil($totalIngresos / $resultadosPorPagina) : 1;

        $categorias = $m->obtenerCategoriasIngresos();

        $params = array(
            'ingresos' => $ingresos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'categoriaSeleccionada' => $categoria,
            'resultadosPorPagina' => $resultadosPorPagina
        );

        $this->render('verIngresos.php', $params);
    }

    // Ver situación financiera del usuario
    public function verSituacionFinanciera()
    {
        $m = new GastosModelo();
        $idUsuario = $_SESSION['usuario']['id'];

        $totalIngresos = $m->obtenerTotalIngresos($idUsuario);
        $totalGastos = $m->obtenerTotalGastos($idUsuario);

        $saldo = $totalIngresos - $totalGastos;

        $params = array(
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'saldo' => $saldo
        );

        $this->render('verSituacionFinanciera.php', $params);
    }

    // Generar NewsLetter y almacenar el envío
    public function generarNewsLetter($idUser)
    {
        $m = new GastosModelo();

        $resumen = $m->obtenerResumenFinancieroUsuario($idUser);
        $refran = $m->obtenerRefranAleatorio();

        $contenido = "Resumen Financiero:\n";
        $contenido .= "Saldo Total: " . $resumen['saldo_total'] . "\n";
        $contenido .= "Gastos Totales: " . $resumen['gastos_totales'] . "\n";
        $contenido .= "Ingresos Totales: " . $resumen['ingresos_totales'] . "\n";
        $contenido .= "\nRefrán del día:\n";
        $contenido .= $refran['refran'] . "\n";

        $m->insertarNewsLetterEnvio($idUser, $refran['idRefran'], $resumen['saldo_total'], $resumen['gastos_totales'], $resumen['ingresos_totales']);

        return $contenido;
    }

    // Función para enviar NewsLetter por correo
    public function enviarNewsLetter($idUser, $email)
    {
        $m = new GastosModelo();

        $resumen = $m->obtenerResumenFinancieroUsuario($idUser);
        $refran = $m->obtenerRefranAleatorio();

        $contenido = "
            <h1>Resumen Financiero</h1>
            <p>Estimado usuario, aquí está tu resumen financiero:</p>
            <ul>
                <li><strong>Saldo total:</strong> {$resumen['saldo_total']}</li>
                <li><strong>Gastos totales:</strong> {$resumen['gastos_totales']}</li>
                <li><strong>Ingresos totales:</strong> {$resumen['ingresos_totales']}</li>
            </ul>
            <p>Y como siempre, un refrán para ti:</p>
            <blockquote>{$refran['refran']}</blockquote>
            <p>Gracias por usar nuestra aplicación.</p>
        ";

        $asunto = "Tu Resumen Financiero y un Refrán del Día";
        $this->enviarCorreo($email, $asunto, $contenido);
    }

    private function enviarCorreo($destinatario, $asunto, $contenido)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: admin@tuaplicacion.com" . "\r\n";

        mail($destinatario, $asunto, $contenido, $headers);
    }

    // Insertar Gasto
    public function insertarGasto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarGasto'])) {
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $idFamilia = $_SESSION['usuario']['idFamilia'] ?: null;
            $idGrupo = $_SESSION['usuario']['idGrupo'] ?: null;
            $idUsuario = $_SESSION['usuario']['id'];

            $m = new GastosModelo();

            if (!$idFamilia && !$idGrupo) {
                $params['mensaje'] = 'El usuario no está asociado a una familia o grupo.';
                $this->formInsertarGasto($params);
                return;
            }

            if ($m->insertarGasto($idUsuario, $monto, $categoria, $concepto, $origen, $idFamilia, $idGrupo)) {
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

            if (empty($monto) || empty($categoria) || empty($concepto) || empty($origen)) {
                $params['mensaje'] = 'Todos los campos son obligatorios.';
                $this->formInsertarIngreso($params);
                return;
            }

            $idFamilia = $_SESSION['usuario']['idFamilia'] ?: null;
            $idGrupo = $_SESSION['usuario']['idGrupo'] ?: null;
            $idUsuario = $_SESSION['usuario']['id'];

            $m = new GastosModelo();

            try {
                if ($m->insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen, $idFamilia, $idGrupo)) {
                    header('Location: index.php?ctl=verIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo insertar el ingreso.';
                }
            } catch (Exception $e) {
                $params['mensaje'] = 'Error al insertar ingreso: ' . $e->getMessage();
            }
        }

        $this->formInsertarIngreso();
    }

    // Formulario para insertar gasto
    public function formInsertarGasto($params = array())
    {
        $m = new GastosModelo();
        $params['categorias'] = $m->obtenerCategoriasGastos();

        $this->render('formInsertarGasto.php', $params);
    }

    // Formulario para insertar ingreso
    public function formInsertarIngreso($params = array())
    {
        $m = new GastosModelo();
        $params['categorias'] = $m->obtenerCategoriasIngresos();

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

        if (isset($_GET['id'])) {
            $ingreso = $m->obtenerIngresoPorId($_GET['id']);

            if (!$ingreso) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            }
        }

        $categorias = $m->obtenerCategoriasIngresos();

        $params = array(
            'ingreso' => $ingreso,
            'categorias' => $categorias
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarIngreso'])) {
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $origen = recoge('origen');
            $categoria = recoge('categoria');

            if ($m->actualizarIngreso($ingreso['idIngreso'], $concepto, $importe, $origen, $categoria)) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar el ingreso. Inténtalo de nuevo.';
            }
        }

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
                $this->verGastos();
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

    // Formulario para asignar usuario a familia o grupo
    public function formAsignarUsuario()
    {
        $m = new GastosModelo();
        
        // Obtiene las familias y grupos disponibles
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
        $usuarios = $m->obtenerUsuarios();

        // Parámetros a pasar a la vista
        $params = array(
            'familias' => $familias,
            'grupos' => $grupos,
            'usuarios' => $usuarios
        );

        // Renderiza la vista con el formulario para asignar usuario
        $this->render('formAsignarUsuario.php', $params);
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
