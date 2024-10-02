<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class CategoriaController
{
    // Ver categorías de gastos
    // Ver categorías de gastos
    public function verCategoriasGastos()
    {
        try {
            $m = new GastosModelo();
            $categorias = $m->obtenerCategoriasGastos();

            // Verificar si cada categoría está en uso por algún gasto
            foreach ($categorias as &$categoria) {
                $categoria['enUso'] = $m->categoriaEnUso($categoria['idCategoria'], 'gastos');
                $categoria['creadaPorUsuario'] = ($categoria['creado_por'] == $_SESSION['usuario']['id']);
            }

            $params = array(
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de gastos'
            );

            $this->render('verCategoriasGastos.php', $params);
        } catch (Exception $e) {
            error_log("Error en verCategoriasGastos(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar las categorías de gastos.');
        }
    }



    // Ver categorías de ingresos
    public function verCategoriasIngresos()
    {
        try {
            $m = new GastosModelo();
            $categorias = $m->obtenerCategoriasIngresos();

            // Verificar si cada categoría está en uso por algún ingreso
            foreach ($categorias as &$categoria) {
                // Verificar si la categoría está en uso
                $categoria['enUso'] = $m->categoriaIngresoEnUso($categoria['idCategoria']);
                // Verificar si fue creada por el usuario actual
                $categoria['creadaPorUsuario'] = ($categoria['creado_por'] == $_SESSION['usuario']['id']);
            }

            $params = array(
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de ingresos'
            );

            $this->render('verCategoriasIngresos.php', $params);
        } catch (Exception $e) {
            error_log("Error en verCategoriasIngresos(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar las categorías de ingresos.');
        }
    }



    // Insertar nueva categoría de gasto
    public function insertarCategoriaGasto()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            // Generar el token CSRF
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaGasto'])) {
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                $creadoPor = $_SESSION['usuario']['id']; // Guardamos el id del usuario que crea la categoría

                if (empty($nombreCategoria)) {
                    $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
                } elseif ($m->insertarCategoriaGasto($nombreCategoria, $creadoPor)) {
                    error_log("Categoría de gasto '{$nombreCategoria}' creada por el usuario con id {$creadoPor}.");
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo insertar la categoría de gasto.';
                    error_log("Fallo al insertar categoría de gasto: '{$nombreCategoria}'.");
                }
            }

            $this->verCategoriasGastos();
        } catch (Exception $e) {
            error_log("Error en insertarCategoriaGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al insertar la categoría de gasto.');
        }
    }

    // Insertar nueva categoría de ingreso
    public function insertarCategoriaIngreso()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            // Generar el token CSRF
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaIngreso'])) {
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                $creadoPor = $_SESSION['usuario']['id']; // Guardamos el id del usuario que crea la categoría

                if (empty($nombreCategoria)) {
                    $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
                } elseif ($m->insertarCategoriaIngreso($nombreCategoria, $creadoPor)) {
                    error_log("Categoría de ingreso '{$nombreCategoria}' creada por el usuario con id {$creadoPor}.");
                    header('Location: index.php?ctl=verCategoriasIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo insertar la categoría de ingreso.';
                    error_log("Fallo al insertar categoría de ingreso: '{$nombreCategoria}'.");
                }
            }

            $this->verCategoriasIngresos();
        } catch (Exception $e) {
            error_log("Error en insertarCategoriaIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al insertar la categoría de ingreso.');
        }
    }

    // Editar categoría de gasto
    // Editar categoría de gasto
    public function editarCategoriaGasto()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();

                if ($m->actualizarCategoriaGasto($idCategoria, $nombreCategoria)) {
                    error_log("Categoría de gasto '{$nombreCategoria}' actualizada.");
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar la categoría de gasto.';
                    error_log("Fallo al actualizar categoría de gasto con ID: {$idCategoria}.");
                }
            } else {
                if (isset($_GET['id'])) {
                    $m = new GastosModelo();
                    $categoria = $m->obtenerCategoriaGastoPorId($_GET['id']);

                    if ($categoria) {
                        $params = array(
                            'categoria' => $categoria,
                            'mensaje' => 'Editar Categoría de Gasto',
                            'csrf_token' => $_SESSION['csrf_token']
                        );
                        $this->render('formEditarCategoriaGasto.php', $params);
                    } else {
                        $this->redireccionarError('Categoría no encontrada.');
                    }
                } else {
                    $this->redireccionarError('Solicitud no válida.');
                }
            }
        } catch (Exception $e) {
            error_log("Error en editarCategoriaGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar la categoría de gasto.');
        }
    }
    public function actualizarCategoriaIngreso()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');
                $csrf_token = recoge('csrf_token');

                // Validar token CSRF
                if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
                    $this->redireccionarError('Token CSRF no válido.');
                    return;
                }

                if (empty($idCategoria) || empty($nombreCategoria)) {
                    $params['mensaje'] = 'El ID de la categoría y el nombre no pueden estar vacíos.';
                } else {
                    $m = new GastosModelo();
                    if ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
                        error_log("Categoría de ingreso '{$nombreCategoria}' actualizada.");
                        header('Location: index.php?ctl=verCategoriasIngresos');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
                        error_log("Fallo al actualizar la categoría de ingreso con ID: {$idCategoria}.");
                    }
                }
            }

            $this->verCategoriasIngresos();
        } catch (Exception $e) {
            error_log("Error en actualizarCategoriaIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar la categoría de ingreso.');
        }
    }


    // Editar categoría de ingreso
    public function editarCategoriaIngreso()
    {
        try {
            // Verificar que el usuario tiene los permisos adecuados
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            $m = new GastosModelo();

            // Generar el token CSRF
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Si el método es POST, significa que estamos intentando actualizar la categoría
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
                // Recoger los datos enviados por el formulario
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');

                // Validar que el ID y el nombre de la categoría no estén vacíos
                if (empty($idCategoria) || empty($nombreCategoria)) {
                    $params['mensaje'] = 'El ID de la categoría y el nombre no pueden estar vacíos.';
                } else {
                    // Intentar actualizar la categoría
                    if ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
                        error_log("Categoría de ingreso '{$nombreCategoria}' actualizada.");
                        header('Location: index.php?ctl=verCategoriasIngresos');
                        exit();
                    } else {
                        // Si ocurre un error en la actualización
                        $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
                        error_log("Fallo al actualizar la categoría de ingreso con ID: {$idCategoria}.");
                    }
                }
            } else {
                // Si el método no es POST, es GET y queremos mostrar los datos de la categoría
                if (isset($_GET['id'])) {
                    $categoria = $m->obtenerCategoriaIngresoPorId($_GET['id']);

                    // Comprobar si la categoría existe
                    if (!$categoria) {
                        $this->redireccionarError('Categoría no encontrada.');
                        return;
                    }

                    // Preparar los datos para mostrarlos en la vista de edición
                    $params['categoria'] = $categoria;
                    $this->render('formEditarCategoriaIngreso.php', $params);
                } else {
                    $this->redireccionarError('Categoría no válida.');
                }
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y mostrar un error
            error_log("Error en editarCategoriaIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar la categoría de ingreso.');
        }
    }

    // Eliminar categoría de gasto
    public function eliminarCategoriaGasto()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar categorías.');
                return;
            }

            if (isset($_GET['id'])) {
                $m = new GastosModelo();

                // Verificar si la categoría está en uso antes de eliminarla
                if ($m->categoriaEnUso($_GET['id'], 'gastos')) {
                    $params['mensaje'] = 'No se puede eliminar la categoría porque está en uso.';
                    error_log("Intento de eliminar categoría de gasto en uso, ID: {$_GET['id']}.");
                    $this->verCategoriasGastos();
                    return;
                }

                if ($m->eliminarCategoriaGasto($_GET['id'])) {
                    error_log("Categoría de gasto con ID: {$_GET['id']} eliminada.");
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo eliminar la categoría de gasto.';
                    error_log("Fallo al eliminar categoría de gasto con ID: {$_GET['id']}.");
                }
            }
        } catch (Exception $e) {
            error_log("Error en eliminarCategoriaGasto(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar la categoría de gasto.');
        }
    }

    // Eliminar categoría de ingreso
    public function eliminarCategoriaIngreso()
    {
        try {
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            if (isset($_GET['id'])) {
                $m = new GastosModelo();

                // Verificar si la categoría está en uso antes de eliminarla
                if ($m->categoriaIngresoEnUso($_GET['id'])) {
                    error_log("Intento de eliminar categoría de ingreso en uso, ID: {$_GET['id']}.");
                    $this->redireccionarError('No se puede eliminar la categoría porque está en uso.');
                    return;
                }

                if ($m->eliminarCategoriaIngreso($_GET['id'])) {
                    error_log("Categoría de ingreso con ID: {$_GET['id']} eliminada.");
                    header('Location: index.php?ctl=verCategoriasIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo eliminar la categoría de ingreso.';
                    error_log("Fallo al eliminar categoría de ingreso con ID: {$_GET['id']}.");
                }
            }
        } catch (Exception $e) {
            error_log("Error en eliminarCategoriaIngreso(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar la categoría de ingreso.');
        }
    }



    // Método de redireccionamiento en caso de errores
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }

    // Renderizar vistas
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
}
