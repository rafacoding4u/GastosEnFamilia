<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FamiliaGrupoController
{
    // Formulario para crear una nueva familia
    public function formCrearFamilia()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
                return;
            }
            $this->render('formCrearFamilia.php');
        } catch (Exception $e) {
            error_log("Error en formCrearFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario de creación de familia.');
        }
    }

    // Crear una nueva familia
    public function crearFamilia()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearFamilia'])) {
                $nombre_familia = recoge('nombre_familia');
                $password_familia = recoge('password_familia');

                $errores = array();
                cTexto($nombre_familia, "nombre_familia", $errores);
                cContrasenya($password_familia, $errores);

                if (empty($errores)) {
                    $m = new GastosModelo();
                    $hashedPassword = encriptar($password_familia);

                    if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                        error_log("Familia '{$nombre_familia}' creada correctamente.");
                        header('Location: index.php?ctl=listarFamilias');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo crear la familia.';
                        error_log("Fallo al crear la familia: '{$nombre_familia}'.");
                    }
                } else {
                    $params['errores'] = $errores;
                }

                $this->render('formCrearFamilia.php', $params);
            }
        } catch (Exception $e) {
            error_log("Error en crearFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al crear la familia.');
        }
    }

    // Formulario para crear un nuevo grupo
    public function formCrearGrupo()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear grupos.');
                return;
            }
            $this->render('formCrearGrupo.php');
        } catch (Exception $e) {
            error_log("Error en formCrearGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario de creación de grupo.');
        }
    }

    // Crear un nuevo grupo
    public function crearGrupo()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear grupos.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearGrupo'])) {
                $nombre_grupo = recoge('nombre_grupo');
                $password_grupo = recoge('password_grupo');

                $errores = array();
                cTexto($nombre_grupo, "nombre_grupo", $errores);
                cContrasenya($password_grupo, $errores);

                if (empty($errores)) {
                    $m = new GastosModelo();
                    $hashedPassword = encriptar($password_grupo);

                    if ($m->insertarGrupo($nombre_grupo, $hashedPassword)) {
                        error_log("Grupo '{$nombre_grupo}' creado correctamente.");
                        header('Location: index.php?ctl=listarGrupos');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo crear el grupo.';
                        error_log("Fallo al crear el grupo: '{$nombre_grupo}'.");
                    }
                } else {
                    $params['errores'] = $errores;
                }

                $this->render('formCrearGrupo.php', $params);
            }
        } catch (Exception $e) {
            error_log("Error en crearGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al crear el grupo.');
        }
    }

    // Listar Familias
    public function listarFamilias()
    {
        try {
            $m = new GastosModelo();
            $familias = $m->obtenerFamilias();

            $params = array(
                'familias' => $familias,
                'mensaje' => 'Lista de familias registradas'
            );

            $this->render('listarFamilias.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarFamilias(): " . $e->getMessage());
            $this->redireccionarError('Error al listar las familias.');
        }
    }

    // Listar Grupos
    public function listarGrupos()
    {
        try {
            $m = new GastosModelo();
            $grupos = $m->obtenerGrupos();

            $params = array(
                'grupos' => $grupos,
                'mensaje' => 'Lista de grupos registrados'
            );

            $this->render('listarGrupos.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarGrupos(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los grupos.');
        }
    }

    // Editar Familia
    public function editarFamilia()
    {
        try {
            $m = new GastosModelo();

            if (isset($_GET['id'])) {
                $familia = $m->obtenerFamiliaPorId($_GET['id']);
                if (!$familia) {
                    $params['mensaje'] = 'Familia no encontrada.';
                    $this->listarFamilias();
                    return;
                }
            }

            $esAdmin = esSuperadmin();

            if (!$esAdmin) {
                $administradores = $m->obtenerAdministradoresFamilia($familia['idFamilia']);
                foreach ($administradores as $admin) {
                    if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                        $esAdmin = true;
                        break;
                    }
                }

                if (!$esAdmin) {
                    $this->redireccionarError('No tienes permiso para editar esta familia.');
                    return;
                }
            }

            $params = array(
                'nombre_familia' => $familia['nombre_familia'],
                'idFamilia' => $familia['idFamilia']
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarFamilia'])) {
                $nombre_familia = recoge('nombre_familia');
                $errores = array();

                cTexto($nombre_familia, "nombre_familia", $errores);

                if (empty($errores)) {
                    if ($m->actualizarFamilia($familia['idFamilia'], $nombre_familia)) {
                        error_log("Familia '{$nombre_familia}' actualizada correctamente.");
                        header('Location: index.php?ctl=listarFamilias');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar la familia.';
                        error_log("Fallo al actualizar la familia con ID: {$familia['idFamilia']}.");
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            $this->render('formEditarFamilia.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al editar la familia.');
        }
    }

    // Editar Grupo
    public function editarGrupo()
    {
        try {
            $m = new GastosModelo();

            if (isset($_GET['id'])) {
                $grupo = $m->obtenerGrupoPorId($_GET['id']);
                if (!$grupo) {
                    $params['mensaje'] = 'Grupo no encontrado.';
                    $this->listarGrupos();
                    return;
                }
            }

            $esAdmin = esSuperadmin();

            if (!$esAdmin) {
                $administradores = $m->obtenerAdministradoresGrupo($grupo['idGrupo']);
                foreach ($administradores as $admin) {
                    if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                        $esAdmin = true;
                        break;
                    }
                }

                if (!$esAdmin) {
                    $this->redireccionarError('No tienes permiso para editar este grupo.');
                    return;
                }
            }

            $params = array(
                'nombre_grupo' => $grupo['nombre_grupo'],
                'idGrupo' => $grupo['idGrupo']
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGrupo'])) {
                $nombre_grupo = recoge('nombre_grupo');
                $errores = array();

                cTexto($nombre_grupo, "nombre_grupo", $errores);

                if (empty($errores)) {
                    if ($m->actualizarGrupo($grupo['idGrupo'], $nombre_grupo)) {
                        error_log("Grupo '{$nombre_grupo}' actualizado correctamente.");
                        header('Location: index.php?ctl=listarGrupos');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar el grupo.';
                        error_log("Fallo al actualizar el grupo con ID: {$grupo['idGrupo']}.");
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            $this->render('formEditarGrupo.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el grupo.');
        }
    }

    // Eliminar Familia
    public function eliminarFamilia()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede eliminar familias.');
                return;
            }

            $idFamilia = recoge('id');
            $m = new GastosModelo();

            $usuariosAsociados = $m->obtenerUsuariosPorFamilia($idFamilia);
            if (!empty($usuariosAsociados)) {
                $this->redireccionarError('No se puede eliminar la familia. Hay usuarios asociados.');
                return;
            }

            if ($m->eliminarFamilia($idFamilia)) {
                error_log("Familia con ID {$idFamilia} eliminada correctamente.");
                header('Location: index.php?ctl=listarFamilias');
                exit();
            } else {
                $this->redireccionarError('Error al eliminar la familia.');
                error_log("Fallo al eliminar la familia con ID: {$idFamilia}.");
            }
        } catch (Exception $e) {
            error_log("Error en eliminarFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar la familia.');
        }
    }

    // Eliminar Grupo
    public function eliminarGrupo()
    {
        try {
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede eliminar grupos.');
                return;
            }

            $idGrupo = recoge('id');
            $m = new GastosModelo();

            $usuariosAsociados = $m->obtenerUsuariosPorGrupo($idGrupo);
            if (!empty($usuariosAsociados)) {
                $this->redireccionarError('No se puede eliminar el grupo. Hay usuarios asociados.');
                return;
            }

            if ($m->eliminarGrupo($idGrupo)) {
                error_log("Grupo con ID {$idGrupo} eliminado correctamente.");
                header('Location: index.php?ctl=listarGrupos');
                exit();
            } else {
                $this->redireccionarError('Error al eliminar el grupo.');
                error_log("Fallo al eliminar el grupo con ID: {$idGrupo}.");
            }
        } catch (Exception $e) {
            error_log("Error en eliminarGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el grupo.');
        }
    }

    // Método privado para renderizar vistas
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

    // Método privado para redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header("Location: index.php?ctl=error");
        exit();
    }
}
