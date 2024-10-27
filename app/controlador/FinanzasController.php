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

        // Validar pertenencia del usuario a la familia o grupo para obtener los gastos
        if (!$m->usuarioPerteneceAFamiliaOGrupo($_SESSION['usuario']['id'])) {
            $this->redireccionarError('No tienes permiso para ver estos gastos.');
            return;
        }

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

        // Validar pertenencia del usuario a la familia o grupo para obtener los ingresos
        if (!$m->usuarioPerteneceAFamiliaOGrupo($_SESSION['usuario']['id'])) {
            $this->redireccionarError('No tienes permiso para ver estos ingresos.');
            return;
        }

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

    // Ver situación financiera del usuario (detallada)
    public function verSituacionFinanciera()
    {
        $m = new GastosModelo();
        $idUser = $_SESSION['usuario']['id'];

        $totalIngresos = $m->obtenerTotalIngresos($idUser) ?? 0;
        $totalGastos = $m->obtenerTotalGastos($idUser) ?? 0;

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
        $m = new GastosModelo();

        // Obtener familias y grupos a los que pertenece el usuario
        $familias = $m->obtenerFamiliasPorUsuario($_SESSION['usuario']['id']);
        $grupos = $m->obtenerGruposPorUsuario($_SESSION['usuario']['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarGasto'])) {
            // Validación del token CSRF
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Error CSRF");
            }

            // Recolección y sanitización de los datos del formulario
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $origen = recoge('origen');
            $categoria = recoge('idCategoria');
            $fecha = recoge('fecha');
            $asignacion = recoge('asignacion');

            // Inicializar valores de asignación
            $idFamilia = null;
            $idGrupo = null;

            // Determinar asignación a familia, grupo o individual
            if (strpos($asignacion, 'familia_') === 0) {
                $idFamilia = str_replace('familia_', '', $asignacion);
            } elseif (strpos($asignacion, 'grupo_') === 0) {
                $idGrupo = str_replace('grupo_', '', $asignacion);
            }

            // Intentar insertar el gasto
            if ($m->insertarGasto($_SESSION['usuario']['id'], $importe, $categoria, $concepto, $origen, $fecha, $idFamilia, $idGrupo)) {
                header('Location: index.php?ctl=verGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el gasto.';
            }
        }

        // Preparar parámetros para el formulario
        $params = [
            'categorias' => $m->obtenerCategoriasGastos(),
            'familias' => $familias,
            'grupos' => $grupos,
            'csrf_token' => bin2hex(random_bytes(32))
        ];

        // Guardar el token en la sesión
        $_SESSION['csrf_token'] = $params['csrf_token'];

        $this->render('formInsertarGasto.php', $params);
    }



    // Insertar Ingreso
    public function insertarIngreso()
    {
        $m = new GastosModelo();

        // Obtener familias y grupos del usuario o inicializar como vacíos
        $familias = $m->obtenerFamiliasPorUsuario($_SESSION['usuario']['id']) ?? [];
        $grupos = $m->obtenerGruposPorUsuario($_SESSION['usuario']['id']) ?? [];

        // Generar y guardar el token CSRF si no está configurado ya en la sesión
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Preparar parámetros para el formulario
        $params = [
            'categorias' => $m->obtenerCategoriasIngresos(),
            'familias' => $familias,
            'grupos' => $grupos,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        // Comprobar si el formulario ha sido enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarIngreso'])) {
            // Validación del token CSRF
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Error CSRF");
            }

            // Recolectar y sanitizar datos
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $origen = recoge('origen');
            $categoria = recoge('idCategoria');
            $fecha = recoge('fecha');
            $asignacion = recoge('asignacion');

            // Determinar la asignación
            $idFamilia = null;
            $idGrupo = null;
            if (strpos($asignacion, 'familia_') === 0) {
                $idFamilia = str_replace('familia_', '', $asignacion);
            } elseif (strpos($asignacion, 'grupo_') === 0) {
                $idGrupo = str_replace('grupo_', '', $asignacion);
            }

            // Insertar el ingreso
            if ($m->insertarIngreso($_SESSION['usuario']['id'], $importe, $categoria, $concepto, $origen, $fecha, $idFamilia, $idGrupo)) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el ingreso.';
            }
        }

        // Renderizar el formulario de ingreso con parámetros correctos
        $this->render('formInsertarIngreso.php', $params);
    }

    // Formulario para insertar ingreso
    public function formInsertarIngreso($params = array())
    {
        $m = new GastosModelo();

        // Generar un token CSRF y guardarlo en la sesión
        $csrf_token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        // Obtener categorías, familias y grupos del usuario
        $params['categorias'] = $m->obtenerCategoriasIngresos();
        $params['familias'] = $m->obtenerFamiliasPorUsuario($_SESSION['usuario']['id']) ?? []; // Array vacío si no hay familias
        $params['grupos'] = $m->obtenerGruposPorUsuario($_SESSION['usuario']['id']) ?? [];     // Array vacío si no hay grupos
        $params['csrf_token'] = $csrf_token;
        var_dump($params['familias']);
        var_dump($params['grupos']);


        // Renderizar el formulario con los parámetros
        $this->render('formInsertarIngreso.php', $params);
    }

    // Editar Gasto
    public function editarGasto()
    {
        $m = new GastosModelo();
        $gasto = null; // Inicializamos $gasto para evitar errores de variable no asignada

        if (isset($_GET['id'])) {
            $gasto = $m->obtenerGastoPorId($_GET['id']);

            // Verificar si el gasto se encontró
            if (!$gasto) {
                // Redirigir si el gasto no existe
                header('Location: index.php?ctl=verGastos');
                exit();
            }
        }

        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gasto' => $gasto,
            'categorias' => $categorias,
            'csrf_token' => $_SESSION['csrf_token'] // Incluimos el token CSRF en los parámetros
        );

        // Proceso de actualización del gasto
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGasto'])) {
            // Validación CSRF
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Error CSRF");
            }

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
        $ingreso = null;

        if (isset($_GET['id'])) {
            $ingreso = $m->obtenerIngresoPorId($_GET['id']);

            if (!$ingreso) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            }
        }

        $categorias = $m->obtenerCategoriasIngresos();

        // Generar un CSRF token y añadirlo a los parámetros
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Genera un token aleatorio si no existe
        }

        $params = array(
            'ingreso' => $ingreso,
            'categorias' => $categorias,
            'csrf_token' => $_SESSION['csrf_token'] // Asegura que el token esté disponible en los parámetros
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarIngreso'])) {
            // Validación del CSRF token
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Error CSRF: el token es inválido.");
            }

            // Recoger y sanitizar los valores del formulario
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $fecha = recoge('fecha');
            $origen = recoge('origen');
            $categoria = recoge('idCategoria');

            if ($m->actualizarIngreso($ingreso['idIngreso'], $concepto, $importe, $fecha, $origen, $categoria)) {
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

    // Render y redireccionamiento de errores
    private function render($vista, $params = array())
    {
        try {
            extract($params);
            ob_start();
            require __DIR__ . '/../../web/templates/' . $vista;
            $contenido = ob_get_clean();
            require __DIR__ . '/../../web/templates/layout.php';
        } catch (Exception $e) {
            error_log("Error en render(): " . $e->getMessage());
            $this->redireccionarError('Error al renderizar la vista.');
        }
    }
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }

    // Página de Inicio - Mostrar situación financiera general del usuario
    public function inicio()
    {
        $m = new GastosModelo();
        $idUser = $_SESSION['usuario']['id'];

        // Obtener los ingresos, gastos y calcular el saldo del usuario
        $totalIngresos = $m->obtenerTotalIngresos($idUser) ?? 0;
        $totalGastos = $m->obtenerTotalGastos($idUser) ?? 0;
        $saldo = $totalIngresos - $totalGastos;

        // Parámetros a pasar a la vista
        $params = array(
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'saldo' => $saldo
        );

        // Renderizar la vista de inicio
        $this->render('inicio.php', $params);
    }
}
