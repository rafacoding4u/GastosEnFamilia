<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FamiliaGrupoController
{
    // Formulario para crear una nueva familia
    public function formCrearFamilia()
    {
        if (!esSuperadmin()) {  // Usamos la función esSuperadmin() para verificar el rol
            $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
            return;
        }
        $this->render('formCrearFamilia.php');
    }

    // Crear una nueva familia
    public function crearFamilia()
    {
        if (!esSuperadmin()) {  // Verificamos nuevamente el rol de superadmin
            $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearFamilia'])) {
            $nombre_familia = recoge('nombre_familia');
            $password_familia = recoge('password_familia');

            $errores = array();
            cTexto($nombre_familia, "nombre_familia", $errores);
            cContrasenya($password_familia, $errores);  // Usamos cContrasenya para validar la contraseña

            if (empty($errores)) {
                $m = new GastosModelo();
                $hashedPassword = encriptar($password_familia);  // Usamos la función encriptar para unificar la lógica
                if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo crear la familia.';
                }
            } else {
                $params['errores'] = $errores;
            }

            $this->render('formCrearFamilia.php', $params);
        }
    }

    // Formulario para crear un nuevo grupo
    public function formCrearGrupo()
    {
        if (!esSuperadmin()) {  // Verificamos el rol
            $this->redireccionarError('Acceso denegado. Solo superadmin puede crear grupos.');
            return;
        }
        $this->render('formCrearGrupo.php');
    }

    // Crear un nuevo grupo
    public function crearGrupo()
    {
        if (!esSuperadmin()) {  // Verificamos el rol de superadmin
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
                $hashedPassword = encriptar($password_grupo);  // Usamos la función encriptar para unificar la lógica
                if ($m->insertarGrupo($nombre_grupo, $hashedPassword)) {
                    header('Location: index.php?ctl=listarGrupos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo crear el grupo.';
                }
            } else {
                $params['errores'] = $errores;
            }

            $this->render('formCrearGrupo.php', $params);
        }
    }

    // Listar Familias
    public function listarFamilias()
    {
        $m = new GastosModelo();
        $familias = $m->obtenerFamilias();

        $params = array(
            'familias' => $familias,
            'mensaje' => 'Lista de familias registradas'
        );

        $this->render('listarFamilias.php', $params);
    }

    // Listar Grupos
    public function listarGrupos()
    {
        $m = new GastosModelo();
        $grupos = $m->obtenerGrupos();

        $params = array(
            'grupos' => $grupos,
            'mensaje' => 'Lista de grupos registrados'
        );

        $this->render('listarGrupos.php', $params);
    }

    // Editar Familia
    public function editarFamilia()
    {
        $m = new GastosModelo();

        if (isset($_GET['id'])) {
            $familia = $m->obtenerFamiliaPorId($_GET['id']);
            if (!$familia) {
                $params['mensaje'] = 'Familia no encontrada.';
                $this->listarFamilias();
                return;
            }
        }

        $esAdmin = esSuperadmin();  // Asumimos que el superadmin tiene permiso para editar todas las familias

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
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar la familia.';
                }
            } else {
                $params['errores'] = $errores;
            }
        }

        $this->render('formEditarFamilia.php', $params);
    }

    // Editar Grupo
    public function editarGrupo()
    {
        $m = new GastosModelo();

        if (isset($_GET['id'])) {
            $grupo = $m->obtenerGrupoPorId($_GET['id']);
            if (!$grupo) {
                $params['mensaje'] = 'Grupo no encontrado.';
                $this->listarGrupos();
                return;
            }
        }

        $esAdmin = esSuperadmin();  // Verificamos si es superadmin

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
                    header('Location: index.php?ctl=listarGrupos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar el grupo.';
                }
            } else {
                $params['errores'] = $errores;
            }
        }

        $this->render('formEditarGrupo.php', $params);
    }

    // Eliminar Familia
    public function eliminarFamilia()
    {
        if (!esSuperadmin()) {  // Verificamos el rol
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
            header('Location: index.php?ctl=listarFamilias');
            exit();
        } else {
            $this->redireccionarError('Error al eliminar la familia.');
        }
    }

    // Eliminar Grupo
    public function eliminarGrupo()
    {
        if (!esSuperadmin()) {  // Verificamos el rol
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
            header('Location: index.php?ctl=listarGrupos');
            exit();
        } else {
            $this->redireccionarError('Error al eliminar el grupo.');
        }
    }

    // Método privado para renderizar vistas
    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    // Método privado para redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header("Location: index.php?ctl=error");
        exit();
    }
}
