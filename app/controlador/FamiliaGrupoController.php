<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class FamiliaGrupoController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
    }

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

                // Verificar si la familia ya existe
                $m = new GastosModelo();
                if ($m->obtenerFamiliaPorNombre($nombre_familia)) {
                    $errores['nombre_familia'] = 'La familia ya existe.';
                }

                if (empty($errores)) {
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

                // Verificar si el grupo ya existe
                $m = new GastosModelo();
                if ($m->obtenerGrupoPorNombre($nombre_grupo)) {
                    $errores['nombre_grupo'] = 'El grupo ya existe.';
                }

                if (empty($errores)) {
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

                // Validar si existen usuarios asociados a la familia
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

            // Validar si existen usuarios asociados al grupo
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

    // Formulario para asignar un usuario a familia/grupo
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

    // Asignar usuario a una familia o grupo
    public function asignarUsuarioFamiliaGrupo()
    {
        $m = new GastosModelo(); // Asumiendo que GastosModelo es el que gestiona las familias y grupos.

        // Recoger los datos del formulario
        $idUser = recoge('idUser');
        $idFamilia = recoge('idFamilia') ?: null;
        $idGrupo = recoge('idGrupo') ?: null;

        // Validar que se haya seleccionado al menos una opción válida (familia o grupo)
        if (empty($idUser) || (empty($idFamilia) && empty($idGrupo))) {
            $params['mensaje'] = 'Debes seleccionar un usuario y al menos una familia o grupo para asignar.';
            $this->formAsignarUsuario($params);
            return;
        }

        // Intentar asignar el usuario al grupo/familia seleccionado
        try {
            // Procesar asignación a familia
            if ($idFamilia) {
                // Asignar el usuario a la familia
                $m->asignarUsuarioAFamilia($idUser, $idFamilia);
            }

            // Procesar asignación a grupo
            if ($idGrupo) {
                // Asignar el usuario al grupo
                $m->asignarUsuarioAGrupo($idUser, $idGrupo);
            }

            // Redirigir tras una asignación exitosa
            header('Location: index.php?ctl=listarGrupos'); // Redirigir a la vista de grupos
            exit();
        } catch (Exception $e) {
            $params['mensaje'] = 'Error al asignar usuario: ' . $e->getMessage();
            $this->formAsignarUsuario($params); // Mostrar el formulario con el mensaje de error
        }
    }


    // Método privado para generar un token CSRF
    private function generarTokenCSRF()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    public function crearVariasFamilias()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $params['errores'][] = "Token CSRF inválido.";
                require __DIR__ . '/../../web/templates/formCrearFamilia.php';
                return;
            }

            // Obtener el ID del administrador
            $idAdmin = $_SESSION['usuario']['id'];

            // Verificar cuántas familias ya tiene el administrador
            $m = new GastosModelo();
            $familiasExistentes = $m->contarFamiliasPorAdmin($idAdmin);

            // Verificar si ya ha alcanzado el límite
            if ($familiasExistentes >= 5) {
                $params['errores'][] = "Ya has alcanzado el límite de 5 familias.";
                require __DIR__ . '/../../web/templates/formCrearFamilia.php';
                return;
            }

            // Obtener los datos del formulario
            $familias = $_POST['familias'] ?? [];
            $errores = [];

            // Recorrer cada familia para validar e insertar
            foreach ($familias as $key => $familia) {
                if ($familiasExistentes + $key >= 5) {
                    $errores[] = "Solo puedes crear un máximo de 5 familias.";
                    break;
                }

                $nombreFamilia = $familia['nombre'];
                $passwordFamilia = $familia['password'];
                $idAdmin = $familia['id_admin'];

                // Validaciones simples
                if (empty($nombreFamilia)) {
                    $errores[] = "El nombre de la familia es obligatorio para la familia " . ($key + 1);
                }
                if (empty($passwordFamilia)) {
                    $errores[] = "La contraseña de la familia es obligatoria para la familia " . ($key + 1);
                }
                if (empty($idAdmin)) {
                    $errores[] = "Debe seleccionar un administrador para la familia " . ($key + 1);
                }

                // Insertar la familia si no hay errores
                if (empty($errores)) {
                    try {
                        $this->modelo->insertarFamilia($nombreFamilia, $passwordFamilia);
                        $idFamilia = $this->modelo->getLastInsertId();  // Obtener el ID de la familia recién creada
                        if (!$idFamilia) {
                            throw new Exception("No se pudo obtener el ID de la familia.");
                        }

                        $this->modelo->asignarUsuarioAFamilia($idAdmin, $idFamilia);
                        error_log("Familia '{$nombreFamilia}' creada y asignada correctamente.");
                    } catch (Exception $e) {
                        $errores[] = "Error al crear la familia " . ($key + 1) . ": " . $e->getMessage();
                        error_log("Error al crear la familia: " . $e->getMessage());
                    }
                }
            }

            // Verificar si hubo errores
            if (!empty($errores)) {
                $params['errores'] = $errores;
                require __DIR__ . '/../../web/templates/formCrearFamilia.php';
            } else {
                $params['mensaje'] = "Familias creadas exitosamente.";
                require __DIR__ . '/../../web/templates/formCrearFamilia.php';
            }
        } else {
            header("Location: index.php?ctl=formCrearFamilia");
        }
    }
    // Para verificar la contraseña de las familias
    public function verificarPasswordFamilia($idFamilia, $passwordIntroducido)
    {
        $m = new GastosModelo();
        $sql = "SELECT password FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $m->getConexion()->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($resultado && password_verify($passwordIntroducido, $resultado['password']));
    }


    public function crearVariosGrupos()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $params['errores'][] = "Token CSRF inválido.";
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
                return;
            }

            // Obtener el ID del administrador
            $idAdmin = $_SESSION['usuario']['id'];

            // Verificar cuántos grupos ya tiene el administrador
            $m = new GastosModelo();
            $gruposExistentes = $m->contarGruposPorAdmin($idAdmin);

            // Verificar si ya ha alcanzado el límite
            if ($gruposExistentes >= 10) {
                $params['errores'][] = "Ya has alcanzado el límite de 10 grupos.";
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
                return;
            }

            // Obtener los datos del formulario
            $grupos = $_POST['grupos'] ?? [];
            $errores = [];

            // Recorrer cada grupo para validar e insertar
            foreach ($grupos as $key => $grupo) {
                if ($gruposExistentes + $key >= 10) {
                    $errores[] = "Solo puedes crear un máximo de 10 grupos.";
                    break;
                }

                $nombreGrupo = $grupo['nombre'];
                $passwordGrupo = $grupo['password'];
                $idAdmin = $grupo['id_admin'];

                // Validaciones simples
                if (empty($nombreGrupo)) {
                    $errores[] = "El nombre del grupo es obligatorio para el grupo " . ($key + 1);
                }
                if (empty($passwordGrupo)) {
                    $errores[] = "La contraseña del grupo es obligatoria para el grupo " . ($key + 1);
                }
                if (empty($idAdmin)) {
                    $errores[] = "Debe seleccionar un administrador para el grupo " . ($key + 1);
                }

                // Insertar el grupo si no hay errores
                if (empty($errores)) {
                    try {
                        $this->modelo->insertarGrupo($nombreGrupo, $passwordGrupo);
                        $idGrupo = $this->modelo->getLastInsertId();  // Obtener el ID del grupo recién creado
                        $this->modelo->asignarUsuarioAGrupo($idAdmin, $idGrupo);
                    } catch (Exception $e) {
                        $errores[] = "Error al crear el grupo " . ($key + 1) . ": " . $e->getMessage();
                    }
                }
            }

            // Verificar si hubo errores
            if (!empty($errores)) {
                $params['errores'] = $errores;
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
            } else {
                $params['mensaje'] = "Grupos creados exitosamente.";
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
            }
        } else {
            header("Location: index.php?ctl=formCrearGrupo");
        }
    }
    public function formCrearFamiliaGrupoAdicionales()
    {
        try {
            // Validar si el usuario tiene el nivel adecuado para crear familias y grupos
            if ($_SESSION['usuario']['nivel_usuario'] !== 'admin') {
                $this->redireccionarError('Acceso denegado. Solo administradores pueden crear familias y grupos adicionales.');
                return;
            }

            // Verificar cuántas familias y grupos ya ha creado el usuario administrador
            $idAdmin = $_SESSION['usuario']['id'];
            $totalFamilias = $this->modelo->contarFamiliasPorAdmin($idAdmin);
            $totalGrupos = $this->modelo->contarGruposPorAdmin($idAdmin);

            // Limitar el número de familias y grupos que puede crear
            $familiasRestantes = 5 - $totalFamilias;
            $gruposRestantes = 10 - $totalGrupos;

            // Evitar que el usuario acceda si ya ha alcanzado los límites
            if ($familiasRestantes <= 0 && $gruposRestantes <= 0) {
                $this->redireccionarError('Ya has alcanzado el límite de familias y grupos que puedes crear.');
                return;
            }

            // Generar un token CSRF
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Pasar la información al formulario
            $params = [
                'familiasRestantes' => $familiasRestantes,
                'gruposRestantes' => $gruposRestantes,
                'csrf_token' => $_SESSION['csrf_token'],
            ];

            $this->render('formCrearFamiliaGrupoAdicionales.php', $params);
        } catch (Exception $e) {
            error_log("Error en formCrearFamiliaGrupoAdicionales(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario para crear familias y grupos adicionales.');
        }
    }

    // Procesar la creación de familias y grupos adicionales
    public function crearFamiliaGrupoAdicionales()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Verificar el token CSRF
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $this->redireccionarError('Token CSRF inválido.');
                    return;
                }

                $idAdmin = $_SESSION['usuario']['id'];

                // Validar los datos del formulario
                $errores = [];
                $familias = $_POST['familias'] ?? [];
                $grupos = $_POST['grupos'] ?? [];

                // Procesar familias
                foreach ($familias as $key => $familia) {
                    $nombreFamilia = htmlspecialchars($familia['nombre'], ENT_QUOTES, 'UTF-8');
                    $passwordFamilia = htmlspecialchars($familia['password'], ENT_QUOTES, 'UTF-8');

                    // Validar nombre y contraseña de la familia
                    if (empty($nombreFamilia) || empty($passwordFamilia)) {
                        $errores[] = "El nombre y la contraseña son obligatorios para la familia " . ($key + 1);
                        continue;
                    }

                    // Insertar la familia
                    if ($this->modelo->insertarFamilia($nombreFamilia, password_hash($passwordFamilia, PASSWORD_BCRYPT))) {
                        $idFamilia = $this->modelo->getLastInsertId();
                        $this->modelo->asignarUsuarioAFamilia($idAdmin, $idFamilia);
                        $this->modelo->asignarAdministradorAFamilia($idAdmin, $idFamilia);
                    } else {
                        $errores[] = "No se pudo crear la familia " . ($key + 1);
                    }
                }

                // Procesar grupos
                foreach ($grupos as $key => $grupo) {
                    $nombreGrupo = htmlspecialchars($grupo['nombre'], ENT_QUOTES, 'UTF-8');
                    $passwordGrupo = htmlspecialchars($grupo['password'], ENT_QUOTES, 'UTF-8');

                    // Validar nombre y contraseña del grupo
                    if (empty($nombreGrupo) || empty($passwordGrupo)) {
                        $errores[] = "El nombre y la contraseña son obligatorios para el grupo " . ($key + 1);
                        continue;
                    }

                    // Insertar el grupo
                    if ($this->modelo->insertarGrupo($nombreGrupo, password_hash($passwordGrupo, PASSWORD_BCRYPT))) {
                        $idGrupo = $this->modelo->getLastInsertId();
                        $this->modelo->asignarUsuarioAGrupo($idAdmin, $idGrupo);
                        $this->modelo->asignarAdministradorAGrupo($idAdmin, $idGrupo);
                    } else {
                        $errores[] = "No se pudo crear el grupo " . ($key + 1);
                    }
                }

                if (empty($errores)) {
                    $_SESSION['mensaje_exito'] = 'Familias y grupos creados exitosamente.';
                    header('Location: index.php?ctl=inicio');
                    exit();
                } else {
                    $params['errores'] = $errores;
                    $this->render('formCrearFamiliaGrupoAdicionales.php', $params);
                }
            }
        } catch (Exception $e) {
            error_log("Error en crearFamiliaGrupoAdicionales(): " . $e->getMessage());
            $this->redireccionarError('Error al crear familias y grupos adicionales.');
        }
    }
}
