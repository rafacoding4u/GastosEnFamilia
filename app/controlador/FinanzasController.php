<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FinanzasController
{
    // Ver Gastos
    public function verGastos()
    {
        $m = new GastosModelo();

        // Obtener la preferencia de resultados por página para gastos
        $resultadosPorPagina = $m->obtenerPreferenciaUsuario('resultados_por_pagina_gastos', $_SESSION['usuario']['id']) ?? 10;

        // Parámetros de filtro
        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;
        $origen = recoge('origen') ?: null;

        // Parámetros de paginación
        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $offset = ($paginaActual - 1) * $resultadosPorPagina;

        // Obtener los gastos aplicando los filtros y la paginación
        $gastos = $m->obtenerGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen, $offset, $resultadosPorPagina);

        // Obtener el número total de gastos para la paginación
        $totalGastos = $m->contarGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen);
        $totalPaginas = ceil($totalGastos / $resultadosPorPagina);

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
            'origenSeleccionado' => $origen,
            'resultadosPorPagina' => $resultadosPorPagina
        );

        $this->render('verGastos.php', $params);
    }

    // Ver Ingresos
    public function verIngresos()
    {
        $m = new GastosModelo();

        // Obtener la preferencia de resultados por página para ingresos
        $resultadosPorPagina = $m->obtenerPreferenciaUsuario('resultados_por_pagina_ingresos', $_SESSION['usuario']['id']) ?? 10;

        // Parámetros de filtro
        $fechaInicio = recoge('fechaInicio') ?: null;
        $fechaFin = recoge('fechaFin') ?: null;
        $categoria = recoge('categoria') ?: null;

        // Parámetros de paginación
        $paginaActual = recoge('pagina') ? (int)recoge('pagina') : 1;
        $offset = ($paginaActual - 1) * $resultadosPorPagina;

        // Obtener los ingresos aplicando los filtros y la paginación
        $ingresos = $m->obtenerIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, null, $offset, $resultadosPorPagina);

        // Obtener el número total de ingresos para la paginación
        $totalIngresos = $m->contarIngresosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria);
        $totalPaginas = ceil($totalIngresos / $resultadosPorPagina);

        // Pasar las categorías a la vista
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

        // Obtener el total de ingresos y gastos
        $totalIngresos = $m->obtenerTotalIngresos($idUsuario);
        $totalGastos = $m->obtenerTotalGastos($idUsuario);

        // Calcular saldo
        $saldo = $totalIngresos - $totalGastos;

        // Preparar los parámetros para la vista
        $params = array(
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'saldo' => $saldo
        );

        // Renderizar la vista de situación financiera
        $this->render('verSituacionFinanciera.php', $params);
    }

    // Generar NewsLetter y almacenar el envío
    public function generarNewsLetter($idUser)
    {
        $m = new GastosModelo();
        
        // Obtener el resumen financiero
        $resumen = $m->obtenerResumenFinancieroUsuario($idUser);
        
        // Obtener un refrán aleatorio
        $refran = $m->obtenerRefranAleatorio();

        // Crear el contenido de la newsletter
        $contenido = "Resumen Financiero:\n";
        $contenido .= "Saldo Total: " . $resumen['saldo_total'] . "\n";
        $contenido .= "Gastos Totales: " . $resumen['gastos_totales'] . "\n";
        $contenido .= "Ingresos Totales: " . $resumen['ingresos_totales'] . "\n";
        $contenido .= "\nRefrán del día:\n";
        $contenido .= $refran['refran'] . "\n";

        // Insertar el envío en la tabla news_letter_envios
        $m->insertarNewsLetterEnvio($idUser, $refran['idRefran'], $resumen['saldo_total'], $resumen['gastos_totales'], $resumen['ingresos_totales']);

        return $contenido;
    }

    // Función para enviar NewsLetter por correo
    public function enviarNewsLetter($idUser, $email)
    {
        $m = new GastosModelo();

        // Obtener resumen financiero del usuario
        $resumen = $m->obtenerResumenFinancieroUsuario($idUser);

        // Obtener un refrán aleatorio
        $refran = $m->obtenerRefranAleatorio();

        // Preparar el contenido de la News Letter
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

        // Enviar correo al usuario
        $asunto = "Tu Resumen Financiero y un Refrán del Día";
        $this->enviarCorreo($email, $asunto, $contenido);
    }

    private function enviarCorreo($destinatario, $asunto, $contenido)
    {
        // Aquí puedes usar la función mail() de PHP o una librería como PHPMailer
        // para enviar el correo. En este ejemplo se usará mail().
        
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

            // Asegurarse de pasar idFamilia y idGrupo desde la sesión del usuario
            $idFamilia = $_SESSION['usuario']['idFamilia'];
            $idGrupo = $_SESSION['usuario']['idGrupo'];
            $idUsuario = $_SESSION['usuario']['id']; // El ID del usuario que está haciendo el gasto

            $m = new GastosModelo();
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

            // Obtener idFamilia y idGrupo desde la sesión del usuario
            $idFamilia = $_SESSION['usuario']['idFamilia'];
            $idGrupo = $_SESSION['usuario']['idGrupo'];
            $idUsuario = $_SESSION['usuario']['id']; // El ID del usuario que está haciendo el ingreso

            $m = new GastosModelo();
            if ($m->insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen, $idFamilia, $idGrupo)) {
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
