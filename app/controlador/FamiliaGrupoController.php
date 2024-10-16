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

            // Obtener los usuarios administradores registrados
            $m = new GastosModelo();
            $administradores = $m->obtenerAdministradores(); // Método que obtiene administradores

            // Generar un token CSRF y almacenarlo en la sesión
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Enviar la lista de administradores y el token CSRF a la vista
            $params = [
                'administradores' => $administradores,
                'csrf_token' => $_SESSION['csrf_token']
            ];

            $this->render('formCrearFamilia.php', $params);
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
                // Verificar el token CSRF
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $params['errores']['csrf'] = 'Token CSRF inválido, intente nuevamente.';
                    $this->render('formCrearFamilia.php', $params);
                    return;
                }

                // Recoger los datos del formulario
                $nombre_familia = recoge('nombre_familia');
                $password_familia = recoge('password_familia');
                $id_admin = recoge('id_admin');

                $errores = array();

                // Validar nombre de familia y contraseña
                cUser($nombre_familia, "nombre_familia", $errores, 100, 1, true);
                cContrasenya($password_familia, $errores);

                if (empty($errores)) {
                    $m = new GastosModelo();
                    $hashedPassword = encriptar($password_familia);

                    if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                        $idFamilia = $m->obtenerIdFamiliaPorNombre($nombre_familia);
                        $m->añadirAdministradorAFamilia($id_admin, $idFamilia);

                        error_log("Familia '{$nombre_familia}' creada correctamente.");
                        unset($_SESSION['csrf_token']);
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

            // Obtener los usuarios administradores registrados
            $m = new GastosModelo();
            $administradores = $m->obtenerAdministradores();

            // Generar un token CSRF y almacenarlo en la sesión
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Pasar los administradores y el token CSRF a la vista
            $params = [
                'administradores' => $administradores,
                'csrf_token' => $_SESSION['csrf_token']
            ];

            $this->render('formCrearGrupo.php', $params);
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

                // Validar nombre de grupo y contraseña
                cUser($nombre_grupo, "nombre_grupo", $errores, 100, 1, true);
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
        $m = new GastosModelo();

        if ($_SESSION['nivel_usuario'] < 2) {
            header("Location: index.php?ctl=error");
            exit();
        }

        $idFamilia = recoge('id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                $nombreFamilia = htmlspecialchars(recoge('nombre_familia'), ENT_QUOTES, 'UTF-8');
                $idAdmin = recoge('idAdmin');

                if (empty($idAdmin)) {
                    throw new Exception('Debe seleccionar un administrador.');
                }

                if ($m->actualizarFamilia($idFamilia, $nombreFamilia, $idAdmin)) {
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $params['mensaje'] = 'Error al actualizar la familia. Inténtelo de nuevo.';
                }
            } catch (Exception $e) {
                error_log("Error al editar la familia: " . $e->getMessage());
                $params['mensaje'] = 'Error al editar la familia: ' . $e->getMessage();
            }
        } else {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $familia = $m->consultarFamiliaPorId($idFamilia);
            $usuarios = $m->obtenerUsuarios();

            if (!$familia) {
                error_log("Error: No se encontró la familia con ID {$idFamilia}.");
                $_SESSION['error_mensaje'] = "No se encontró la familia.";
                header('Location: index.php?ctl=listarFamilias');
                exit();
            }

            $params = array(
                'idFamilia' => $idFamilia,
                'nombreFamilia' => $familia['nombre_familia'],
                'idAdmin' => $familia['idAdmin'],
                'usuarios' => $usuarios,
                'csrf_token' => $_SESSION['csrf_token']
            );

            $this->render('formEditarFamilia.php', $params);
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

            $administradoresDisponibles = $m->obtenerAdministradores();

            $csrf_token = generarTokenCSRF();

            $params = array(
                'nombre_grupo' => $grupo['nombre_grupo'],
                'idGrupo' => $grupo['idGrupo'],
                'administradores' => $administradoresDisponibles,
                'idAdminActual' => $grupo['idAdmin'],
                'csrf_token' => $csrf_token
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGrupo'])) {
                $nombre_grupo = recoge('nombre_grupo');
                $id_admin = recoge('id_admin');
                $errores = array();

                cUser($nombre_grupo, "nombre_grupo", $errores);

                if (empty($id_admin)) {
                    $errores[] = "Debes seleccionar un administrador válido.";
                }

                if (empty($errores)) {
                    if ($m->actualizarGrupo($grupo['idGrupo'], $nombre_grupo, $id_admin)) {
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

            if (isset($_GET['id'])) {
                $idFamilia = $_GET['id'];
                $m = new GastosModelo();

                $usuariosAsociados = $m->obtenerUsuariosPorFamilia($idFamilia);
                if (!empty($usuariosAsociados)) {
                    $this->redireccionarError('No se puede eliminar la familia. Hay usuarios asociados.');
                    return;
                }

                if ($m->eliminarFamilia($idFamilia)) {
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $this->redireccionarError('Error al eliminar la familia.');
                }
            } else {
                $this->redireccionarError('Familia no encontrada.');
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

    // Métodos privados de renderización y redirección
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
        header("Location: index.php?ctl=error");
        exit();
    }
    public function formAsignarUsuario($params = array())
{
    // Instanciamos el modelo para acceder a los datos
    $m = new GastosModelo();

    // Obtenemos la lista de familias y grupos para poder asignar
    $familias = $m->obtenerFamilias();
    $grupos = $m->obtenerGrupos();
    $usuarios = $m->obtenerUsuarios();

    // Verificamos si hay mensajes de error o éxito en los parámetros pasados
    if (isset($params['mensaje'])) {
        $mensaje = $params['mensaje'];
    } else {
        $mensaje = null;
    }

    // Parámetros que se pasarán a la vista
    $params = array(
        'familias' => $familias,
        'grupos' => $grupos,
        'usuarios' => $usuarios,
        'mensaje' => $mensaje,
    );

    // Renderizamos la vista del formulario asignar usuario a familia/grupo
    $this->render('formAsignarUsuario.php', $params);
}
public function asignarUsuarioFamiliaGrupo()
{
    $m = new GastosModelo(); // Asumiendo que GastosModelo es el que gestiona las familias y grupos.

    // Recoger los datos del formulario
    $idUsuario = recoge('idUsuario');
    $idFamilia = recoge('idFamilia') ?: null;
    $idGrupo = recoge('idGrupo') ?: null;

    // Validar que se haya seleccionado al menos una opción válida (familia o grupo)
    if (empty($idUsuario) || (empty($idFamilia) && empty($idGrupo))) {
        $params['mensaje'] = 'Debes seleccionar un usuario y al menos una familia o grupo para asignar.';
        $this->formAsignarUsuario($params);
        return;
    }

    // Intentar asignar el usuario al grupo/familia seleccionado
    try {
        if ($idFamilia) {
            $asignadoFamilia = $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
        }
        if ($idGrupo) {
            $asignadoGrupo = $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
        }

        if (!empty($asignadoFamilia) || !empty($asignadoGrupo)) {
            // Redirigir a la vista que corresponda luego de la asignación exitosa
            header('Location: index.php?ctl=listarFamilias'); // Puedes cambiar la ruta según lo que desees mostrar.
            exit();
        } else {
            $params['mensaje'] = 'No se pudo asignar el usuario a la familia o grupo.';
        }
    } catch (Exception $e) {
        $params['mensaje'] = 'Error al asignar usuario: ' . $e->getMessage();
    }

    // Si no se redirige, mostrar el formulario nuevamente con mensaje de error
    $this->formAsignarUsuario($params);
}


}
