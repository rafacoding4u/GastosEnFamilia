<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class CategoriaController
{
    // Ver categorías de gastos
    public function verCategoriasGastos()
    {
        $m = new GastosModelo();
        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'categorias' => $categorias,
            'mensaje' => 'Gestión de categorías de gastos'
        );

        $this->render('verCategoriasGastos.php', $params);
    }

    // Ver categorías de ingresos
    public function verCategoriasIngresos()
    {
        $m = new GastosModelo();
        $categorias = $m->obtenerCategoriasIngresos();

        $params = array(
            'categorias' => $categorias,
            'mensaje' => 'Gestión de categorías de ingresos'
        );

        $this->render('verCategoriasIngresos.php', $params);
    }

    // Insertar nueva categoría de gasto
    public function insertarCategoriaGasto()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaGasto'])) {
            $nombreCategoria = recoge('nombreCategoria');
            $m = new GastosModelo();

            $nivelUsuario = $_SESSION['usuario']['nivel_usuario']; // Nivel del usuario que crea la categoría

            if (empty($nombreCategoria)) {
                $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
            } elseif ($m->insertarCategoriaGasto($nombreCategoria, $nivelUsuario)) {
                header('Location: index.php?ctl=verCategoriasGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar la categoría de gasto.';
            }
        }

        $this->verCategoriasGastos();
    }

    // Insertar nueva categoría de ingreso
    public function insertarCategoriaIngreso()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaIngreso'])) {
            $nombreCategoria = recoge('nombreCategoria');
            $m = new GastosModelo();

            if ($m->insertarCategoriaIngreso($nombreCategoria)) {
                header('Location: index.php?ctl=verCategoriasIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar la categoría de ingreso.';
            }
        }

        $this->verCategoriasIngresos();
    }

    // Editar categoría de gasto
    public function editarCategoriaGasto()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCategoria = recoge('idCategoria');
            $nombreCategoria = recoge('nombreCategoria');
            $m = new GastosModelo();

            if ($m->actualizarCategoriaGasto($idCategoria, $nombreCategoria)) {
                header('Location: index.php?ctl=verCategoriasGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar la categoría de gasto.';
            }
        }

        $this->verCategoriasGastos();
    }

    // Editar categoría de ingreso
    public function editarCategoriaIngreso()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
            $idCategoria = recoge('idCategoria');
            $nombreCategoria = recoge('nombreCategoria');

            $m = new GastosModelo();
            if ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
                header('Location: index.php?ctl=verCategoriasIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
            }
        } else {
            if (isset($_GET['id'])) {
                $m = new GastosModelo();
                $categoria = $m->obtenerCategoriaIngresoPorId($_GET['id']);
                if (!$categoria) {
                    $this->redireccionarError('Categoría no encontrada.');
                    return;
                }
                $params['categoria'] = $categoria;
                $this->render('formEditarCategoriaIngreso.php', $params);
            } else {
                $this->redireccionarError('Categoría no válida.');
            }
        }
    }

    // Eliminar categoría de gasto
    public function eliminarCategoriaGasto()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar categorías.');
            return;
        }

        if (isset($_GET['id'])) {
            $m = new GastosModelo();

            // Verificar si la categoría está en uso antes de eliminarla
            if ($m->categoriaEnUso($_GET['id'], 'gastos')) {
                $params['mensaje'] = 'No se puede eliminar la categoría porque está en uso.';
                $this->verCategoriasGastos();
                return;
            }

            if ($m->eliminarCategoriaGasto($_GET['id'])) {
                header('Location: index.php?ctl=verCategoriasGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo eliminar la categoría de gasto.';
            }
        }
    }

    // Eliminar categoría de ingreso
    public function eliminarCategoriaIngreso()
    {
        if (!esAdmin() && !esSuperadmin()) {
            $this->redireccionarError('Acceso denegado.');
            return;
        }

        if (isset($_GET['id'])) {
            $m = new GastosModelo();

            // Verificar si la categoría está en uso antes de eliminarla
            if ($m->categoriaIngresoEnUso($_GET['id'])) {
                $this->redireccionarError('No se puede eliminar la categoría porque está en uso.');
                return;
            }

            if ($m->eliminarCategoriaIngreso($_GET['id'])) {
                header('Location: index.php?ctl=verCategoriasIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo eliminar la categoría de ingreso.';
            }
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
        extract($params); 
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }
}
