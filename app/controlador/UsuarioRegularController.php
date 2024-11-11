<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';
require_once 'app/modelo/UsuarioGestion.php'; // Incluir la clase auxiliar

class UsuarioRegularController
{
    private $usuarioGestion;
    private $userId;

    public function __construct()
    {
        $this->userId = $_SESSION['usuario']['id'];
        $this->usuarioGestion = new UsuarioGestion($this->userId); // Instancia UsuarioGestion
    }

    // Mostrar el resumen financiero personal
    public function mostrarResumenFinanciero()
    {
        try {
            $resumen = $this->usuarioGestion->obtenerResumenFinanciero();
            $params = [
                'resumen' => $resumen,
                'mensaje' => 'Resumen financiero personal'
            ];
            $this->render('resumenFinancieroUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en mostrarResumenFinanciero(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el resumen financiero.');
        }
    }

    // Listar gastos propios
    public function listarGastos()
    {
        try {
            $gastos = $this->usuarioGestion->listarGastosUsuario();
            $params = [
                'gastos' => $gastos,
                'mensaje' => 'Listado de tus gastos'
            ];
            $this->render('listarGastosUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarGastos(): " . $e->getMessage());
            $this->redireccionarError('Error al listar tus gastos.');
        }
    }

    // Agregar un gasto propio
    public function agregarGasto()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datosGasto = [
                    'descripcion' => recoge('descripcion'),
                    'cantidad' => recoge('cantidad'),
                    'fecha' => recoge('fecha'),
                    'categoria' => recoge('categoria')
                ];
                $this->usuarioGestion->agregarGasto($datosGasto);
                $_SESSION['mensaje_exito'] = "Gasto agregado exitosamente.";
                header('Location: index.php?ctl=listarGastos');
                exit();
            }

            // Mostrar formulario de agregar gasto
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formAgregarGastoUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en agregarGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al agregar el gasto.');
        }
    }

    // Editar un gasto propio
    public function editarGasto($idGasto)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nuevosDatos = [
                    'descripcion' => recoge('descripcion'),
                    'cantidad' => recoge('cantidad'),
                    'fecha' => recoge('fecha'),
                    'categoria' => recoge('categoria')
                ];
                $this->usuarioGestion->editarGasto($idGasto, $nuevosDatos);
                $_SESSION['mensaje_exito'] = "Gasto editado correctamente.";
                header('Location: index.php?ctl=listarGastos');
                exit();
            }

            // Obtener datos del gasto para editar
            $gasto = $this->usuarioGestion->obtenerGastoPorId($idGasto);
            if (!$gasto) {
                throw new Exception("Gasto no encontrado.");
            }

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'gasto' => $gasto,
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formEditarGastoUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el gasto.');
        }
    }

    // Eliminar un gasto propio
    public function eliminarGasto($idGasto)
    {
        try {
            $this->usuarioGestion->eliminarGasto($idGasto);
            $_SESSION['mensaje_exito'] = "Gasto eliminado correctamente.";
            header('Location: index.php?ctl=listarGastos');
            exit();
        } catch (Exception $e) {
            error_log("Error en eliminarGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el gasto.');
        }
    }

    // Funciones similares para ingresos
    public function listarIngresos()
    {
        try {
            $ingresos = $this->usuarioGestion->listarIngresosUsuario();
            $params = [
                'ingresos' => $ingresos,
                'mensaje' => 'Listado de tus ingresos'
            ];
            $this->render('listarIngresosUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarIngresos(): " . $e->getMessage());
            $this->redireccionarError('Error al listar tus ingresos.');
        }
    }

    public function agregarIngreso()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datosIngreso = [
                    'descripcion' => recoge('descripcion'),
                    'cantidad' => recoge('cantidad'),
                    'fecha' => recoge('fecha'),
                    'categoria' => recoge('categoria')
                ];
                $this->usuarioGestion->agregarIngreso($datosIngreso);
                $_SESSION['mensaje_exito'] = "Ingreso agregado exitosamente.";
                header('Location: index.php?ctl=listarIngresos');
                exit();
            }

            // Mostrar formulario de agregar ingreso
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formAgregarIngresoUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en agregarIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al agregar el ingreso.');
        }
    }

    public function editarIngreso($idIngreso)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nuevosDatos = [
                    'descripcion' => recoge('descripcion'),
                    'cantidad' => recoge('cantidad'),
                    'fecha' => recoge('fecha'),
                    'categoria' => recoge('categoria')
                ];
                $this->usuarioGestion->editarIngreso($idIngreso, $nuevosDatos);
                $_SESSION['mensaje_exito'] = "Ingreso editado correctamente.";
                header('Location: index.php?ctl=listarIngresos');
                exit();
            }

            // Obtener datos del ingreso para editar
            $ingreso = $this->usuarioGestion->obtenerIngresoPorId($idIngreso);
            if (!$ingreso) {
                throw new Exception("Ingreso no encontrado.");
            }

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'ingreso' => $ingreso,
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formEditarIngresoUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el ingreso.');
        }
    }

    public function eliminarIngreso($idIngreso)
    {
        try {
            $this->usuarioGestion->eliminarIngreso($idIngreso);
            $_SESSION['mensaje_exito'] = "Ingreso eliminado correctamente.";
            header('Location: index.php?ctl=listarIngresos');
            exit();
        } catch (Exception $e) {
            error_log("Error en eliminarIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el ingreso.');
        }
    }

    // Ver situación financiera individual
    public function verSituacionFinanciera()
    {
        try {
            $situacion = $this->usuarioGestion->obtenerSituacionFinanciera();
            $params = [
                'situacion' => $situacion,
                'mensaje' => 'Situación financiera personal'
            ];
            $this->render('situacionFinancieraUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en verSituacionFinanciera(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera.');
        }
    }

    // Gestión de presupuestos y metas financieras (propias)
    // Implementa métodos similares a los anteriores para presupuestos y metas

    // Renderizar vistas
    private function render($vista, $params = [])
    {
        try {
            extract($params);
            ob_start();
            require __DIR__ . "/../../web/templates/$vista";
            $contenido = ob_get_clean();
            require __DIR__ . "/../../web/templates/layout.php";
        } catch (Exception $e) {
            error_log("Error en render(): " . $e->getMessage());
            $this->redireccionarError('Error al renderizar la vista.');
        }
    }

    // Redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
}
