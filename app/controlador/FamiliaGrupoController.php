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
            $administradores = $m->obtenerAdministradores();  // Aquí asumimos que existe un método para obtener los administradores

            // Enviar la lista de administradores a la vista
            $params = [
                'administradores' => $administradores
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
            $nombre_familia = recoge('nombre_familia');
            $password_familia = recoge('password_familia');
            $id_admin = recoge('id_admin'); // Recoger el administrador seleccionado

            $errores = array();
            cTexto($nombre_familia, "nombre_familia", $errores);
            cContrasenya($password_familia, $errores);

            if (empty($errores)) {
                $m = new GastosModelo();
                $hashedPassword = encriptar($password_familia);

                if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                    // Obtener el ID de la familia recién creada
                    $idFamilia = $m->obtenerIdFamiliaPorNombre($nombre_familia);

                    // Asignar el administrador seleccionado a la nueva familia
                    $m->añadirAdministradorAFamilia($id_admin, $idFamilia);

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

            $usuarios = $m->obtenerUsuarios(); // Obtener los usuarios para seleccionar el administrador

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarFamilia'])) {
                $nombre_familia = recoge('nombre_familia');
                $idAdmin = recoge('idAdmin'); // Recoger el nuevo administrador seleccionado
                $errores = array();

                cTexto($nombre_familia, "nombre_familia", $errores);

                if (empty($errores)) {
                    if ($m->actualizarFamilia($familia['idFamilia'], $nombre_familia)) {
                        $m->añadirAdministradorAFamilia($idAdmin, $familia['idFamilia']);

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

            $params = array(
                'nombre_familia' => $familia['nombre_familia'],
                'idFamilia' => $familia['idFamilia'],
                'usuarios' => $usuarios,
                'idAdmin' => $familia['idAdmin'] ?? null
            );

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

                // Verifica si hay usuarios asociados a la familia
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
    public function formAsignarUsuario()
    {
        // Permitir acceso a admin y superadmin
        if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' && $_SESSION['usuario']['nivel_usuario'] !== 'admin') {
            header('Location: index.php?ctl=inicio');
            exit();
        }

        // Instanciar el modelo
        $m = new GastosModelo();

        // Obtener datos de usuarios, familias y grupos
        $usuarios = $m->obtenerUsuarios();
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();

        // Definir los parámetros que se enviarán a la vista
        $params = array(
            'usuarios' => $usuarios,
            'familias' => $familias,
            'grupos' => $grupos
        );

        // Renderizar la vista 'formAsignarUsuario.php' con los datos obtenidos
        $this->render('formAsignarUsuario.php', $params);
    }


    // Eliminar administrador de una familia
    public function eliminarAdministradorDeFamilia()
    {
        $idAdmin = recoge('idAdmin'); // ID del administrador
        $idFamilia = recoge('idFamilia'); // ID de la familia
        $m = new GastosModelo();

        // Verificar si el usuario es superadmin o si es un administrador válido
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $administradores = $m->obtenerAdministradoresFamilia($idFamilia);
            $esAdmin = false;

            foreach ($administradores as $admin) {
                if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                    $esAdmin = true;
                    break;
                }
            }

            if (!$esAdmin) {
                $this->redireccionarError('No tienes permiso para eliminar administradores de esta familia.');
                return;
            }
        }

        // Proceder a eliminar el administrador
        if ($m->eliminarAdministradorDeFamilia($idAdmin, $idFamilia)) {
            header('Location: index.php?ctl=verFamilia&id=' . $idFamilia);
            exit();
        } else {
            $this->redireccionarError('Error al eliminar el administrador de la familia.');
        }
    }
    // Eliminar administrador de un grupo
    public function eliminarAdministradorDeGrupo()
    {
        $idAdmin = recoge('idAdmin'); // ID del administrador
        $idGrupo = recoge('idGrupo'); // ID del grupo
        $m = new GastosModelo();

        // Verificar si el usuario es superadmin o si es un administrador válido
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $administradores = $m->obtenerAdministradoresGrupo($idGrupo);
            $esAdmin = false;

            foreach ($administradores as $admin) {
                if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                    $esAdmin = true;
                    break;
                }
            }

            if (!$esAdmin) {
                $this->redireccionarError('No tienes permiso para eliminar administradores de este grupo.');
                return;
            }
        }

        // Proceder a eliminar el administrador
        if ($m->eliminarAdministradorDeGrupo($idAdmin, $idGrupo)) {
            header('Location: index.php?ctl=verGrupo&id=' . $idGrupo);
            exit();
        } else {
            $this->redireccionarError('Error al eliminar el administrador del grupo.');
        }
    }

    // Asignar administrador a una familia
    public function asignarAdministradorAFamilia()
    {
        $idFamilia = recoge('idFamilia');
        $idAdmin = recoge('idAdmin'); // ID del usuario que se convertirá en administrador
        $m = new GastosModelo();

        // Verificar si el usuario es superadmin
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo superadmin puede asignar administradores.');
            return;
        }

        // Verificar si el usuario ya es administrador de la familia
        $administradores = $m->obtenerAdministradoresFamilia($idFamilia);
        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $idAdmin) {
                $this->redireccionarError('El usuario ya es administrador de esta familia.');
                return;
            }
        }

        // Asignar al usuario como administrador de la familia
        if ($m->añadirAdministradorAFamilia($idAdmin, $idFamilia)) {
            header('Location: index.php?ctl=verFamilia&id=' . $idFamilia);
            exit();
        } else {
            $this->redireccionarError('Error al asignar el administrador a la familia.');
        }
    }

    // Asignar administrador a un grupo
    public function asignarAdministradorAGrupo()
    {
        $idGrupo = recoge('idGrupo');
        $idAdmin = recoge('idAdmin'); // ID del usuario que se convertirá en administrador
        $m = new GastosModelo();

        // Verificar si el usuario es superadmin
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo superadmin puede asignar administradores.');
            return;
        }

        // Verificar si el usuario ya es administrador del grupo
        $administradores = $m->obtenerAdministradoresGrupo($idGrupo);
        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $idAdmin) {
                $this->redireccionarError('El usuario ya es administrador de este grupo.');
                return;
            }
        }

        // Asignar al usuario como administrador del grupo
        if ($m->añadirAdministradorAGrupo($idAdmin, $idGrupo)) {
            header('Location: index.php?ctl=verGrupo&id=' . $idGrupo);
            exit();
        } else {
            $this->redireccionarError('Error al asignar el administrador al grupo.');
        }
    }
    // Asignar usuarios normales a familias o grupos
    public function asignarUsuarioFamiliaGrupo()
    {
        // Registro de depuración al entrar en el método
        error_log("DEBUG: Entrando en asignarUsuarioFamiliaGrupo");

        // Instanciamos el modelo para realizar las operaciones
        $m = new GastosModelo();

        // Recogemos los datos del formulario
        $idUsuario = recoge('idUsuario');
        $tipoVinculo = recoge('tipoVinculo');
        $passwordGrupoFamilia = recoge('passwordGrupoFamilia');

        // Registro de depuración de los valores recogidos
        error_log("DEBUG: ID Usuario -> $idUsuario, Tipo de Vínculo -> $tipoVinculo");

        // Verificamos si el tipo de vínculo es 'familia' o 'grupo' y gestionamos en consecuencia
        if ($tipoVinculo === 'familia') {
            // Recogemos el ID de la familia
            $idFamilia = recoge('idFamilia');
            error_log("DEBUG: ID Familia -> $idFamilia");

            // Verificamos la contraseña de la familia usando el método del modelo
            if (!$m->verificarPasswordFamilia($idFamilia, $passwordGrupoFamilia)) {
                // Registro en caso de error con la contraseña
                error_log("ERROR: Contraseña incorrecta para la familia $idFamilia");
                $this->redireccionarError('La contraseña de la familia es incorrecta.');
                return;
            }

            // Intentamos asignar el usuario a la familia
            if ($m->asignarUsuarioAFamilia($idUsuario, $idFamilia)) {
                // Redirigimos a la vista de familias en caso de éxito
                header('Location: index.php?ctl=listarFamilias');
                exit();
            } else {
                // Registro en caso de error al asignar usuario
                error_log("ERROR: No se pudo asignar el usuario a la familia.");
                $this->redireccionarError('Error al asignar el usuario a la familia.');
            }
        } elseif ($tipoVinculo === 'grupo') {
            // Recogemos el ID del grupo
            $idGrupo = recoge('idGrupo');
            error_log("DEBUG: ID Grupo -> $idGrupo");

            // Verificamos la contraseña del grupo usando el método del modelo
            if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoFamilia)) {
                // Registro en caso de error con la contraseña
                error_log("ERROR: Contraseña incorrecta para el grupo $idGrupo");
                $this->redireccionarError('La contraseña del grupo es incorrecta.');
                return;
            }

            // Intentamos asignar el usuario al grupo
            if ($m->asignarUsuarioAGrupo($idUsuario, $idGrupo)) {
                // Redirigimos a la vista de grupos en caso de éxito
                header('Location: index.php?ctl=verGrupos');
                exit();
            } else {
                // Registro en caso de error al asignar usuario
                error_log("ERROR: No se pudo asignar el usuario al grupo.");
                $this->redireccionarError('Error al asignar el usuario al grupo.');
            }
        } else {
            // Registro en caso de tipo de vínculo no válido
            error_log("ERROR: Tipo de vínculo no válido -> $tipoVinculo");
            $this->redireccionarError('Tipo de vínculo no válido.');
        }
    }
}
