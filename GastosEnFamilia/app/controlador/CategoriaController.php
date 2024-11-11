<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';
require_once 'app/controlador/CategoriaController.php';


class CategoriaController
{
    // Ver categorías de gastos
    public function verCategoriasGastos()
    {
        try {
            $m = new GastosModelo();

            // Obtener filtros de búsqueda desde la solicitud GET
            $filtros = [
                'idCategoria' => $_GET['idCategoria'] ?? null,
                'nombreCategoria' => $_GET['nombreCategoria'] ?? null,
                'creado_por_alias' => $_GET['creado_por_alias'] ?? null,
                'creado_por_id' => $_GET['creado_por_id'] ?? null,
                'creado_por_rol' => $_GET['creado_por_rol'] ?? null,
            ];

            $categorias = $m->obtenerCategoriasGastosConDetalles($filtros);

            $params = [
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de gastos'
            ];

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

            // Obtener filtros de búsqueda desde la solicitud GET
            $filtros = [
                'idCategoria' => $_GET['idCategoria'] ?? null,
                'nombreCategoria' => $_GET['nombreCategoria'] ?? null,
                'creado_por_alias' => $_GET['creado_por_alias'] ?? null,
                'creado_por_id' => $_GET['creado_por_id'] ?? null,
                'creado_por_rol' => $_GET['creado_por_rol'] ?? null,
            ];

            $categorias = $m->obtenerCategoriasIngresosConDetalles($filtros);

            $params = [
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de ingresos'
            ];

            $this->render('verCategoriasIngresos.php', $params);
        } catch (Exception $e) {
            error_log("Error en verCategoriasIngresos(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar las categorías de ingresos.');
        }
    }



    public function insertarCategoriaGasto()
    {
        try {
            // Verificar permisos de usuario
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            // Generar y verificar el token CSRF en la solicitud GET inicial
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Verificar que la solicitud sea POST y que se haya enviado el formulario correctamente
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaGasto'])) {
                // Verificar token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('Token CSRF inválido.');
                }

                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                $creadoPor = $_SESSION['usuario']['id'];

                // Validar el nombre de la categoría
                if (empty($nombreCategoria)) {
                    $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
                } elseif ($m->insertarCategoriaGasto($nombreCategoria, $creadoPor)) {
                    // Log de éxito
                    error_log("Categoría de gasto '{$nombreCategoria}' creada por el usuario con id {$creadoPor}.");
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    // Mensaje de error si la inserción falla
                    $params['mensaje'] = 'No se pudo insertar la categoría de gasto.';
                    error_log("Fallo al insertar categoría de gasto: '{$nombreCategoria}'.");
                }
            }

            // Mostrar la vista de categorías con el mensaje en caso de error
            $this->verCategoriasGastos();
        } catch (Exception $e) {
            // Log de errores para excepciones
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

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaIngreso'])) {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('Token CSRF inválido.');
                }

                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                $creadoPor = $_SESSION['usuario']['id'];

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
    public function editarCategoriaGasto()
    {
        try {
            // Verificar permisos de usuario
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            $m = new GastosModelo();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Procesar la solicitud de actualización
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');

                // Intentar actualizar el nombre de la categoría
                if ($m->actualizarCategoriaGasto($idCategoria, $nombreCategoria)) {
                    error_log("Categoría de gasto '{$nombreCategoria}' actualizada.");
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar la categoría de gasto.';
                    error_log("Fallo al actualizar categoría de gasto con ID: {$idCategoria}.");
                }
            } else {
                // Cargar el formulario de edición con los datos de la categoría
                if (isset($_GET['id'])) {
                    $categoria = $m->obtenerCategoriaGastoPorId($_GET['id']);

                    if ($categoria) {
                        $params = [
                            'categoria' => $categoria,
                            'mensaje' => 'Editar Categoría de Gasto',
                            'csrf_token' => $_SESSION['csrf_token'] = bin2hex(random_bytes(32)) // Generar token CSRF
                        ];
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



    // Editar categoría de ingreso
    public function editarCategoriaIngreso()
    {
        try {
            // Verifica permisos
            if (!esAdmin() && !esSuperadmin()) {
                $this->redireccionarError('Acceso denegado.');
                return;
            }

            $m = new GastosModelo();

            // Generar el token CSRF
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');

                if (empty($idCategoria) || empty($nombreCategoria)) {
                    $params['mensaje'] = 'El ID de la categoría y el nombre no pueden estar vacíos.';
                } else {
                    if ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
                        error_log("Categoría de ingreso '{$nombreCategoria}' actualizada.");
                        header('Location: index.php?ctl=verCategoriasIngresos');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
                        error_log("Fallo al actualizar la categoría de ingreso con ID: {$idCategoria}.");
                    }
                }
            } else {
                if (isset($_GET['id'])) {
                    $categoria = $m->obtenerCategoriaIngresoPorId($_GET['id']);

                    if ($categoria) {
                        $params = [
                            'categoria' => $categoria,
                            'mensaje' => 'Editar Categoría de Ingreso',
                            'csrf_token' => $_SESSION['csrf_token']
                        ];
                        $this->render('formEditarCategoriaIngreso.php', $params);
                    } else {
                        $this->redireccionarError('Categoría no válida.');
                    }
                } else {
                    $this->redireccionarError('Solicitud no válida.');
                }
            }
        } catch (Exception $e) {
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
