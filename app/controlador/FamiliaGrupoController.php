<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';


class FamiliaGrupoController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
    }
    // Crear una nueva familia
    public function crearFamilia()
    {
        try {
            error_log("Entrando en crearFamilia()");

            // Verificar permisos de acceso
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                error_log("Acceso denegado: El usuario no tiene nivel de superadmin");
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearFamilia'])) {
                error_log("Procesando formulario de creación de familia");

                // Verificación de CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $params['errores']['csrf'] = 'Token CSRF inválido, intente nuevamente.';
                    error_log("Token CSRF inválido en crearFamilia()");
                    $this->render('formCrearFamilia.php', $params);
                    return;
                }

                // Recoger y validar los datos del formulario
                $nombre_familia = recoge('nombre_familia');
                $password_familia = recoge('password_familia');
                $id_admin = recoge('id_admin');
                $errores = array();

                // Validar datos del formulario
                error_log("Validando datos del formulario: Nombre de familia: $nombre_familia, Admin ID: $id_admin");
                cUser($nombre_familia, "nombre_familia", $errores, 100, 1, true);
                cContrasenya($password_familia, $errores);

                $m = new GastosModelo();
                if ($m->obtenerFamiliaPorNombre($nombre_familia)) {
                    $errores['nombre_familia'] = 'La familia ya existe.';
                    error_log("Error: La familia ya existe.");
                }

                if (empty($errores)) {
                    $hashedPassword = encriptar($password_familia);

                    if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                        $idFamilia = $m->obtenerIdFamiliaPorNombre($nombre_familia);
                        $m->añadirAdministradorAFamilia($id_admin, $idFamilia);

                        error_log("Familia '{$nombre_familia}' creada correctamente con ID: $idFamilia");
                        unset($_SESSION['csrf_token']);
                        header('Location: index.php?ctl=listarFamilias');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo crear la familia.';
                        error_log("Error: Fallo al crear la familia '{$nombre_familia}' en la base de datos.");
                    }
                } else {
                    $params['errores'] = $errores;
                    error_log("Errores en la validación del formulario: " . print_r($errores, true));
                }

                $this->render('formCrearFamilia.php', $params);
            } else {
                error_log("Solicitud no es POST o no contiene bCrearFamilia");
                $this->render('formCrearFamilia.php');
            }
        } catch (Exception $e) {
            error_log("Excepción en crearFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al crear la familia.');
        }
    }

    // Crear un nuevo grupo
    public function crearGrupo()
    {
        try {
            error_log("Entrando en crearGrupo()");

            // Verificar permisos de acceso
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                error_log("Acceso denegado: El usuario no tiene nivel de superadmin");
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear grupos.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearGrupo'])) {
                error_log("Procesando formulario de creación de grupo");

                // Verificación de CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $params['errores']['csrf'] = 'Token CSRF inválido, intente nuevamente.';
                    error_log("Token CSRF inválido en crearGrupo()");
                    $this->render('formCrearGrupo.php', $params);
                    return;
                }

                // Recoger y validar los datos del formulario
                $nombre_grupo = recoge('nombre_grupo');
                $password_grupo = recoge('password_grupo');
                $id_admin = recoge('id_admin');
                $errores = array();

                // Validar datos del formulario
                error_log("Validando datos del formulario: Nombre de grupo: $nombre_grupo, Admin ID: $id_admin");
                cUser($nombre_grupo, "nombre_grupo", $errores, 100, 1, true);
                cContrasenya($password_grupo, $errores);

                $m = new GastosModelo();
                if ($m->obtenerGrupoPorNombre($nombre_grupo)) {
                    $errores['nombre_grupo'] = 'El grupo ya existe.';
                    error_log("Error: El grupo ya existe.");
                }

                if (empty($errores)) {
                    $hashedPassword = encriptar($password_grupo);

                    if ($m->insertarGrupo($nombre_grupo, $hashedPassword)) {
                        $idGrupo = $m->obtenerIdGrupoPorNombre($nombre_grupo);
                        $m->añadirAdministradorAGrupo($id_admin, $idGrupo);

                        error_log("Grupo '{$nombre_grupo}' creado correctamente con ID: $idGrupo");
                        unset($_SESSION['csrf_token']);
                        header('Location: index.php?ctl=listarGrupos');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo crear el grupo.';
                        error_log("Error: Fallo al crear el grupo '{$nombre_grupo}' en la base de datos.");
                    }
                } else {
                    $params['errores'] = $errores;
                    error_log("Errores en la validación del formulario: " . print_r($errores, true));
                }

                $this->render('formCrearGrupo.php', $params);
            } else {
                error_log("Solicitud no es POST o no contiene bCrearGrupo");
                $this->render('formCrearGrupo.php');
            }
        } catch (Exception $e) {
            error_log("Excepción en crearGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al crear el grupo.');
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







    public function crearVariasFamilias()
    {
        try {
            // Validar que el usuario tenga permisos de superadmin o admin
            if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])) {
                $this->redireccionarError('Acceso denegado. Solo superadmin o admin pueden crear múltiples familias.');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('Token CSRF inválido.');
                }

                // Recoger y validar los datos de cada familia
                $familias = $_POST['familias'] ?? [];
                $errores = [];
                foreach ($familias as $index => $familiaData) {
                    $nombreFamilia = htmlspecialchars($familiaData['nombre'], ENT_QUOTES, 'UTF-8');
                    $passwordFamilia = htmlspecialchars($familiaData['password'], ENT_QUOTES, 'UTF-8');
                    $idAdmin = $familiaData['id_admin'];

                    if (empty($nombreFamilia) || empty($passwordFamilia) || empty($idAdmin)) {
                        $errores[] = "Todos los campos son obligatorios para la familia {$index}.";
                        continue;
                    }

                    $hashedPassword = password_hash($passwordFamilia, PASSWORD_DEFAULT);
                    $m = new GastosModelo();

                    // Insertar familia y asignar administrador
                    if (!$m->insertarFamilia($nombreFamilia, $hashedPassword)) {
                        $errores[] = "Error al insertar la familia {$index}.";
                    } else {
                        $idFamilia = $m->obtenerIdFamiliaPorNombre($nombreFamilia);
                        $m->añadirAdministradorAFamilia($idAdmin, $idFamilia);
                    }
                }

                if (empty($errores)) {
                    $_SESSION['mensaje_exito'] = "Familias creadas correctamente.";
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $_SESSION['error_mensaje'] = implode("<br>", $errores);
                    $this->redireccionarError("Errores en la creación de familias: " . implode(", ", $errores));
                }
            } else {
                $this->mostrarFormularioCrearFamilia();
            }
        } catch (Exception $e) {
            error_log("Error en crearVariasFamilias(): " . $e->getMessage());
            $this->redireccionarError('Error al crear múltiples familias.');
        }
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

            // Obtener el ID del administrador principal de sesión
            $idAdmin = $_SESSION['usuario']['id'];
            $m = new GastosModelo();

            // Verificar si el límite de grupos ya ha sido alcanzado
            $gruposExistentes = $m->contarGruposPorAdmin($idAdmin);
            if ($gruposExistentes >= 10) {
                $params['errores'][] = "Ya has alcanzado el límite de 10 grupos.";
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
                return;
            }

            // Obtener datos del formulario y comenzar a insertar cada grupo
            $grupos = $_POST['grupos'] ?? [];
            $errores = [];
            foreach ($grupos as $key => $grupo) {
                if ($gruposExistentes + $key >= 10) {
                    $errores[] = "Solo puedes crear un máximo de 10 grupos.";
                    break;
                }

                $nombreGrupo = $grupo['nombre'];
                $passwordGrupo = $grupo['password'];
                $idAdminGrupo = $grupo['id_admin'];

                // Validaciones
                if (empty($nombreGrupo)) {
                    $errores[] = "El nombre del grupo es obligatorio para el grupo " . ($key + 1);
                }
                if (empty($passwordGrupo)) {
                    $errores[] = "La contraseña del grupo es obligatoria para el grupo " . ($key + 1);
                }
                if (empty($idAdminGrupo)) {
                    $errores[] = "Debe seleccionar un administrador para el grupo " . ($key + 1);
                }

                // Verificar si el nombre del grupo ya existe
                if ($this->modelo->obtenerGrupoPorNombre($nombreGrupo)) {
                    $errores[] = "El nombre del grupo '{$nombreGrupo}' ya está en uso. Por favor elige otro.";
                }

                // Insertar el grupo y asociar al administrador si no hay errores
                if (empty($errores)) {
                    try {
                        $this->modelo->insertarGrupo($nombreGrupo, $passwordGrupo);
                        $idGrupo = $this->modelo->getLastInsertId(); // Obtener el ID del nuevo grupo

                        // Asociar el administrador al grupo en `administradores_grupos`
                        if (!$this->modelo->añadirAdministradorAGrupo($idAdminGrupo, $idGrupo)) {
                            $errores[] = "Error al asignar el administrador al grupo " . ($key + 1);
                        }
                    } catch (Exception $e) {
                        $errores[] = "Error al crear el grupo " . ($key + 1) . ": " . $e->getMessage();
                    }
                }
            }

            // Redirigir o mostrar mensajes al usuario
            if (!empty($errores)) {
                $params['errores'] = $errores;
                require __DIR__ . '/../../web/templates/formCrearGrupo.php';
            } else {
                // Redirigir a listarGrupos si la creación es exitosa
                header("Location: index.php?ctl=listarGrupos");
                exit();
            }
        } else {
            header("Location: index.php?ctl=formCrearGrupo");
            exit();
        }
    }






    // Listar Familias con filtros de búsqueda
    public function listarFamilias()
    {
        try {
            $m = new GastosModelo();

            // Recoger filtros de búsqueda de ID, Nombre de la Familia, Administrador y Usuario
            $filtros = array(
                'id' => $_GET['id'] ?? null,
                'nombre_familia' => $_GET['nombre_familia'] ?? null,
                'administrador' => $_GET['administrador'] ?? null,
                'usuario' => $_GET['usuario'] ?? null,
            );

            // Obtener familias aplicando los filtros y obtener administradores y usuarios asociados
            $familias = $m->obtenerFamiliasConUsuariosYAdministradores($filtros);

            // Parámetros para pasar a la vista
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

            // Recoger filtros de búsqueda para ID, Nombre del Grupo, Administrador, y Usuario
            $filtros = array(
                'id' => $_GET['id'] ?? null,
                'nombre_grupo' => $_GET['nombre_grupo'] ?? null,
                'administrador' => $_GET['administrador'] ?? null,
                'usuario' => $_GET['usuario'] ?? null,
            );

            // Obtener grupos aplicando los filtros y obteniendo administradores y usuarios asociados
            $grupos = $m->obtenerGruposConUsuariosYAdministradores($filtros);

            // Parámetros para pasar a la vista
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


    private function redireccionarError($mensaje)
    {
        if ($_GET['ctl'] !== 'error') {
            $_SESSION['error_mensaje'] = $mensaje;
            header('Location: index.php?ctl=error');
            exit();
        }
    }


    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }
    public function editarFamilia()
    {
        $m = new GastosModelo();

        if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])) {
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
                $administradoresAsignados = $_POST['idAdmin'] ?? [];  // Lista de administradores seleccionados
                $usuariosAsignados = $_POST['usuarios'] ?? [];  // Lista de usuarios seleccionados

                if (empty($administradoresAsignados)) {
                    throw new Exception('Debe seleccionar al menos un administrador.');
                }

                if (!$m->actualizarNombreFamilia($idFamilia, $nombreFamilia)) {
                    throw new Exception('Error al actualizar el nombre de la familia.');
                }

                // Actualizar administradores de la familia
                if (!$m->actualizarAdministradoresFamilia($idFamilia, $administradoresAsignados)) {
                    throw new Exception('Error al asignar los administradores a la familia.');
                }

                // Actualizar usuarios asignados a la familia
                if (!$m->actualizarUsuariosFamilia($idFamilia, $usuariosAsignados)) {
                    throw new Exception('Error al asignar los usuarios a la familia.');
                }

                header('Location: index.php?ctl=listarFamilias');
                exit();
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
            $usuariosAsignados = $m->obtenerUsuariosPorFamilia($idFamilia);
            $administradoresAsignados = $m->obtenerAdministradoresPorFamilia($idFamilia);

            if (!$familia) {
                error_log("Error: No se encontró la familia con ID {$idFamilia}.");
                $_SESSION['error_mensaje'] = "No se encontró la familia.";
                header('Location: index.php?ctl=listarFamilias');
                exit();
            }

            $params = array(
                'idFamilia' => $idFamilia,
                'nombreFamilia' => $familia['nombre_familia'],
                'administradoresAsignados' => $administradoresAsignados,
                'usuarios' => $usuarios,
                'usuariosAsignados' => $usuariosAsignados,
                'csrf_token' => $_SESSION['csrf_token']
            );

            $this->render('formEditarFamilia.php', $params);
        }
    }


    // En FamiliaGrupoController.php
    public function mostrarFormularioCrearFamilia()
    {
        try {
            error_log("Accediendo a mostrarFormularioCrearFamilia()");

            // Validación de permisos para superadmin
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                error_log("Acceso denegado: El usuario no tiene nivel de superadmin");
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
                return;
            }

            // Generar token CSRF y cargar lista de administradores
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $m = new GastosModelo();
            $params = [
                'administradores' => $m->obtenerAdministradores(),
                'csrf_token' => $_SESSION['csrf_token']
            ];

            $this->render('formCrearFamilia.php', $params);
        } catch (Exception $e) {
            error_log("Error en mostrarFormularioCrearFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar el formulario de creación de familia.');
        }
    }
    // Método para eliminar una familia
    public function eliminarFamilia()
    {
        try {
            // Verificar que el usuario sea superadmin
            if (!esSuperadmin()) {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede eliminar familias.');
                return;
            }

            // Verificar si el ID de la familia está presente en la URL
            if (isset($_GET['id'])) {
                $idFamilia = $_GET['id'];
                $m = new GastosModelo();

                // Verificar si hay usuarios asociados a la familia
                $usuariosAsociados = $m->obtenerUsuariosPorFamilia($idFamilia);
                if (!empty($usuariosAsociados)) {
                    $this->redireccionarError('No se puede eliminar la familia. Hay usuarios asociados.');
                    return;
                }

                // Eliminar la familia
                if ($m->eliminarFamilia($idFamilia)) {
                    $_SESSION['mensaje_exito'] = "La familia ha sido eliminada correctamente.";
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
    public function eliminarGrupo()
    {
        try {
            // Verificar permisos de acceso: solo superadmin o admin pueden eliminar grupos
            if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])) {
                $this->redireccionarError('Acceso denegado. Solo superadmin o admin pueden eliminar grupos.');
                return;
            }

            // Obtener el ID del grupo desde los parámetros de la URL
            $idGrupo = isset($_GET['id']) ? (int)$_GET['id'] : null;
            error_log("Intentando eliminar el grupo con ID: $idGrupo");

            if (!$idGrupo) {
                error_log("Error: No se recibió un ID de grupo válido.");
                $this->redireccionarError("ID de grupo inválido.");
                return;
            }

            // Instancia del modelo para la gestión de grupos
            $m = new GastosModelo();

            // Comprobar si el grupo tiene usuarios asociados
            $usuariosAsociados = $m->obtenerUsuariosPorGrupo($idGrupo);
            error_log("Usuarios asociados al grupo $idGrupo: " . json_encode($usuariosAsociados));

            if (!empty($usuariosAsociados)) {
                // Si hay usuarios asociados, mostrar error y redirigir a una página de error o a la vista de grupos
                error_log("No se puede eliminar el grupo. Hay usuarios asociados.");
                $this->redireccionarError("No se puede eliminar el grupo. Hay usuarios asociados.");
                return;
            }

            // Si no hay usuarios asociados, eliminar el grupo
            if (!$m->eliminarGrupo($idGrupo)) {
                error_log("Error al intentar eliminar el grupo con ID: $idGrupo en la base de datos.");
                throw new Exception('Error al eliminar el grupo.');
            }

            // Confirmación de eliminación exitosa
            error_log("Grupo con ID $idGrupo eliminado correctamente.");
            header('Location: index.php?ctl=listarGrupos');
            exit();
        } catch (Exception $e) {
            error_log("Error en eliminarGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el grupo.');
        }
    }


    // Método para editar un grupo
    public function editarGrupo()
    {
        $m = new GastosModelo();

        if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])) {
            header("Location: index.php?ctl=error");
            exit();
        }

        $idGrupo = recoge('id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                $nombreGrupo = htmlspecialchars(recoge('nombre_grupo'), ENT_QUOTES, 'UTF-8');
                $administradoresAsignados = $_POST['idAdmin'] ?? [];  // Lista de administradores seleccionados
                $usuariosAsignados = $_POST['usuarios'] ?? [];  // Lista de usuarios seleccionados

                if (empty($administradoresAsignados)) {
                    throw new Exception('Debe seleccionar al menos un administrador.');
                }

                if (!$m->actualizarNombreGrupo($idGrupo, $nombreGrupo)) {
                    throw new Exception('Error al actualizar el nombre del grupo.');
                }

                // Actualizar administradores del grupo
                if (!$m->actualizarAdministradoresGrupo($idGrupo, $administradoresAsignados)) {
                    throw new Exception('Error al asignar los administradores al grupo.');
                }

                // Actualizar usuarios asignados al grupo
                if (!$m->actualizarUsuariosGrupo($idGrupo, $usuariosAsignados)) {
                    throw new Exception('Error al asignar los usuarios al grupo.');
                }

                header('Location: index.php?ctl=listarGrupos');
                exit();
            } catch (Exception $e) {
                error_log("Error al editar el grupo: " . $e->getMessage());
                $params['mensaje'] = 'Error al editar el grupo: ' . $e->getMessage();
            }
        } else {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $grupo = $m->consultarGrupoPorId($idGrupo);
            $usuarios = $m->obtenerUsuarios();
            $usuariosAsignados = $m->obtenerUsuariosPorGrupo($idGrupo);
            $administradoresAsignados = $m->obtenerAdministradoresPorGrupo($idGrupo);

            if (!$grupo) {
                error_log("Error: No se encontró el grupo con ID {$idGrupo}.");
                $_SESSION['error_mensaje'] = "No se encontró el grupo.";
                header('Location: index.php?ctl=listarGrupos');
                exit();
            }

            $params = array(
                'idGrupo' => $idGrupo,
                'nombreGrupo' => $grupo['nombre_grupo'],
                'administradoresAsignados' => $administradoresAsignados,
                'usuarios' => $usuarios,
                'usuariosAsignados' => $usuariosAsignados,
                'csrf_token' => $_SESSION['csrf_token']
            );

            $this->render('formEditarGrupo.php', $params);
        }
    }


    /*public function asignarUsuarioFamiliaGrupo()
{
    $conexion = $this->modelo->getConexion();
    try {
        // Verificar permisos de usuario
        if (!in_array($_SESSION['usuario']['nivel_usuario'], ['superadmin', 'admin'])) {
            throw new Exception('No tienes permisos para realizar esta acción.');
        }

        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Token CSRF inválido.');
        }

        // Recoger ID de usuario
        $idUsuario = recoge('idUsuario');
        $numFamilias = recoge('numFamilias');
        $numGrupos = recoge('numGrupos');
        $passwordGrupoFamilia = recoge('passwordGrupoFamilia');
        
        // Verificar existencia del usuario
        $m = new GastosModelo();
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        if (!$usuario) {
            throw new Exception('Usuario no encontrado.');
        }

        // Iniciar transacción
        $conexion->beginTransaction();

        // Creación de nuevas familias
        for ($i = 0; $i < $numFamilias; $i++) {
            $nombreFamilia = recoge('nombreFamilia')[$i];
            $passwordFamilia = recoge('passwordFamilia')[$i];

            if (!$m->crearFamilia($nombreFamilia, $passwordFamilia, $idUsuario)) {
                throw new Exception("Error al crear la familia: $nombreFamilia.");
            }
        }

        // Creación de nuevos grupos
        for ($i = 0; $i < $numGrupos; $i++) {
            $nombreGrupo = recoge('nombreGrupo')[$i];
            $passwordGrupo = recoge('passwordGrupo')[$i];

            if (!$m->crearGrupo($nombreGrupo, $passwordGrupo, $idUsuario)) {
                throw new Exception("Error al crear el grupo: $nombreGrupo.");
            }
        }

        // Asignar usuario a familias existentes
        $idFamilias = recoge('idFamilia');
        foreach ($idFamilias as $idFamilia) {
            if (!$m->verificarContraseñaFamilia($idFamilia, $passwordGrupoFamilia)) {
                throw new Exception("Contraseña incorrecta para la familia con ID: $idFamilia.");
            }
            if (!$m->asignarUsuarioAFamilia($idUsuario, $idFamilia)) {
                throw new Exception("Error al asignar el usuario a la familia con ID: $idFamilia.");
            }
        }

        // Asignar usuario a grupos existentes
        $idGrupos = recoge('idGrupo');
        foreach ($idGrupos as $idGrupo) {
            if (!$m->verificarContraseñaGrupo($idGrupo, $passwordGrupoFamilia)) {
                throw new Exception("Contraseña incorrecta para el grupo con ID: $idGrupo.");
            }
            if (!$m->asignarUsuarioAGrupo($idUsuario, $idGrupo)) {
                throw new Exception("Error al asignar el usuario al grupo con ID: $idGrupo.");
            }
        }

        // Confirmar transacción
        $conexion->commit();
        $_SESSION['mensaje_exito'] = 'Usuario asignado correctamente a familias y grupos.';
        header('Location: index.php?ctl=listarUsuarios');
        exit();
    } catch (Exception $e) {
        // Rollback en caso de error
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        error_log("Error en asignarUsuarioFamiliaGrupo(): " . $e->getMessage());
        $this->redireccionarError('Error al asignar el usuario: ' . $e->getMessage());
    }
}*/
    public function asignarUsuarioFamiliaGrupo()
    {
        try {
            // Registro inicial para depuración
            error_log("Entrando en asignarUsuarioFamiliaGrupo()");

            // Validación de token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token CSRF inválido.');
            }

            // Recoger datos del formulario
            $idUsuario = $_POST['idUsuario'] ?? null;
            $rolUsuario = $_POST['rolUsuario'] ?? 'usuario';
            $familiasExistentes = $_POST['idFamilia'] ?? [];
            $gruposExistentes = $_POST['idGrupo'] ?? [];
            $familiaPasswords = $_POST['familiaPassword'] ?? [];
            $grupoPasswords = $_POST['grupoPassword'] ?? [];

            // Inicializar modelo
            $m = new GastosModelo();

            // Verificar usuario seleccionado
            if (!$idUsuario || !$m->obtenerUsuarioPorId($idUsuario)) {
                throw new Exception("Usuario no válido o inexistente: $idUsuario");
            }
            error_log("Usuario válido encontrado: ID $idUsuario");

            // Asignar rol al usuario
            $m->actualizarRolUsuario($idUsuario, $rolUsuario);
            error_log("Rol asignado al usuario: $rolUsuario");

            // Procesar familias existentes
            foreach ($familiasExistentes as $idFamilia) {
                $password = $familiaPasswords[$idFamilia] ?? '';
                if ($m->verificarPasswordFamilia($idFamilia, $password)) {
                    $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                    if ($rolUsuario === 'admin') {
                        $m->asignarAdministradorAFamilia($idUsuario, $idFamilia);
                    }
                    error_log("Usuario $idUsuario asignado a la familia $idFamilia con rol $rolUsuario");
                } else {
                    error_log("Contraseña incorrecta para la familia ID $idFamilia");
                    $_SESSION['error'] = "Contraseña incorrecta para la familia seleccionada.";
                    return $this->formAsignarUsuario(); // Redirigir de vuelta al formulario
                }
            }

            // Procesar grupos existentes
            foreach ($gruposExistentes as $idGrupo) {
                $password = $grupoPasswords[$idGrupo] ?? '';
                if ($m->verificarPasswordGrupo($idGrupo, $password)) {
                    $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                    if ($rolUsuario === 'admin') {
                        $m->asignarAdministradorAGrupo($idUsuario, $idGrupo);
                    }
                    error_log("Usuario $idUsuario asignado al grupo $idGrupo con rol $rolUsuario");
                } else {
                    error_log("Contraseña incorrecta para el grupo ID $idGrupo");
                    $_SESSION['error'] = "Contraseña incorrecta para el grupo seleccionado.";
                    return $this->formAsignarUsuario(); // Redirigir de vuelta al formulario
                }
            }

            // Finalización y redirección con mensaje de éxito
            $_SESSION['mensaje_exito'] = 'Usuario asignado correctamente a familias y grupos seleccionados.';
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        } catch (Exception $e) {
            error_log("Error en asignarUsuarioFamiliaGrupo(): " . $e->getMessage());
            $this->redireccionarError("Error en la asignación del usuario: " . $e->getMessage());
        }
    }


    public function formAsignarUsuario($params = [])
    {
        try {
            // Instancia del modelo para obtener usuarios, familias y grupos
            $m = new GastosModelo();

            // Obtener listas de usuarios (incluyendo administradores), familias y grupos
            $usuarios = $m->obtenerUsuariosConAdministradores(); // Modificado para incluir administradores
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Generar un token CSRF si aún no está definido
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Agregar los datos de usuarios, familias, grupos y CSRF token al array de parámetros
            $params = array_merge($params, [
                'usuarios' => $usuarios,
                'familias' => $familias,
                'grupos' => $grupos,
                'csrf_token' => $_SESSION['csrf_token']
            ]);

            // Renderizar el formulario con los parámetros proporcionados
            $this->render('formAsignarUsuario.php', $params);
        } catch (Exception $e) {
            // Registrar el error y redirigir a una página de error en caso de fallo
            error_log("Error en formAsignarUsuario(): " . $e->getMessage());
            $this->redireccionarError("Error al cargar el formulario de asignación de usuario.");
        }
    }
}
