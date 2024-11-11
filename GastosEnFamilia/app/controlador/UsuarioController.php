<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class UsuarioController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
    }

    // Verificar si el usuario es admin
    private function esAdmin()
    {
        return $_SESSION['usuario']['nivel_usuario'] === 'admin';
    }

    // Verificar si el usuario pertenece a una familia o grupo o si es SuperAdmin
    private function perteneceAFamiliaOGrupo($idUser)
    {
        // Permitir acceso completo a SuperAdmin
        if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') {
            error_log("Permisos verificados: Usuario SuperAdmin, acceso completo a familias y grupos.");
            return true;
        }

        // Comprobación para usuarios Admin o regular
        $m = new GastosModelo();
        $usuario = $m->obtenerUsuarioPorId($idUser);

        // Verificar si el usuario pertenece a la misma familia o grupo que el admin
        $enFamilia = $m->usuarioYaEnFamilia($idUser, $_SESSION['usuario']['idFamilia']);
        $enGrupo = $m->usuarioYaEnGrupo($idUser, $_SESSION['usuario']['idGrupo']);
        $accesoPermitido = ($usuario && ($enFamilia || $enGrupo));

        error_log("Permisos verificados: Usuario $idUser " . ($accesoPermitido ? "pertenece a la misma familia o grupo" : "no pertenece a la misma familia o grupo"));

        return $accesoPermitido;
    }

    // Redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        if ($_GET['ctl'] !== 'error') {
            $_SESSION['error_mensaje'] = $mensaje;
            header('Location: index.php?ctl=error');
            exit();
        }
    }


    public function listarUsuarios()
    {
        try {
            $m = new GastosModelo();

            // Verificación de nivel de usuario
            if ($_SESSION['usuario']['nivel_usuario'] === 'admin') {
                $idAdmin = $_SESSION['usuario']['id'];
                // Obtener usuarios gestionados por el admin con sus familias y grupos y aplicando filtros si existen
                $usuarios = $m->obtenerUsuariosGestionadosConRoles($idAdmin, $_GET);
                $mensaje = 'Lista de usuarios gestionados por el administrador';
            } elseif ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') {
                // Obtener todos los usuarios con sus familias y grupos completos aplicando filtros si existen
                $usuarios = $m->obtenerUsuariosConFamiliasYGrupos($_GET);
                $mensaje = 'Lista de todos los usuarios registrados';
            } else {
                $this->redireccionarError('No tienes permiso para ver la lista de usuarios.');
                return;
            }

            $params = [
                'usuarios' => $usuarios,
                'mensaje' => $mensaje,
            ];

            $this->render('listarUsuarios.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarUsuarios(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los usuarios.');
        }
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
    public function crearUsuario()
    {
        try {
            error_log("Iniciando proceso de creación de usuario...");

            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                throw new Exception('No tienes permisos para crear un usuario.');
            }

            $m = new GastosModelo();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono') ?: null;
                $fecha_nacimiento = recoge('fecha_nacimiento') ?: null;
                $contrasenya = recoge('contrasenya');
                $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
                $nivel_usuario = recoge('nivel_usuario'); // Recoger nivel de usuario seleccionado

                // Generar y encriptar la contraseña premium
                $passwordPremium = bin2hex(random_bytes(4));
                $hashedPasswordPremium = password_hash($passwordPremium, PASSWORD_BCRYPT);

                $idUser = $m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);
                if (!$idUser) throw new Exception('Error al insertar el usuario.');
                error_log("Usuario creado con ID $idUser");

                // Actualizar la contraseña premium
                $m->actualizarPasswordPremium($idUser, $hashedPasswordPremium);

                // Lógica para la creación de familias y grupos
                $opcion_creacion = recoge('opcion_creacion');
                if (in_array($opcion_creacion, ['crear_familia', 'crear_ambos'])) {
                    $nombre_familia = recoge('nombre_nueva_familia');
                    $password_familia = recoge('password_nueva_familia');
                    if ($nombre_familia && $password_familia) {
                        if ($m->insertarFamilia($nombre_familia, $password_familia)) {
                            $idFamilia = $m->obtenerUltimoId();
                            $m->asignarUsuarioAFamilia($idUser, $idFamilia);
                            $m->asignarAdministradorAFamilia($idUser, $idFamilia);
                            error_log("Usuario $idUser asignado como administrador a la familia $idFamilia");
                        }
                    }
                }

                if (in_array($opcion_creacion, ['crear_grupo', 'crear_ambos'])) {
                    $nombre_grupo = recoge('nombre_nuevo_grupo');
                    $password_grupo = recoge('password_nuevo_grupo');
                    if ($nombre_grupo && $password_grupo) {
                        if ($m->insertarGrupo($nombre_grupo, $password_grupo)) {
                            $idGrupo = $m->obtenerUltimoId();
                            $m->asignarUsuarioAGrupo($idUser, $idGrupo);
                            $m->asignarAdministradorAGrupo($idUser, $idGrupo);
                            error_log("Usuario $idUser asignado como administrador al grupo $idGrupo");
                        }
                    }
                }

                $m->actualizarUsuarioNivel($idUser, $nivel_usuario);

                $_SESSION['mensaje_exito'] = "Usuario creado con éxito. Contraseña premium generada: <strong>$passwordPremium</strong>";
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            }

            $params = [
                'familias' => $m->obtenerFamilias(),
                'grupos' => $m->obtenerGrupos(),
                'csrf_token' => $_SESSION['csrf_token'],
            ];
            $this->render('formCrearUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en crearUsuario(): " . $e->getMessage());
            $params = [
                'mensaje' => 'Error al crear el usuario: ' . $e->getMessage(),
                'familias' => $m->obtenerFamilias(),
                'grupos' => $m->obtenerGrupos(),
            ];
            $this->render('formCrearUsuario.php', $params);
        }
    }




    public function formCrearUsuario()
    {
        try {
            // Verificación del nivel de usuario
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear usuarios.');
                return;
            }

            // Inicialización del modelo
            $m = new GastosModelo();
            error_log("Cargando familias y grupos para el formulario de creación de usuario...");

            // Obtener las familias y grupos registrados
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Verificar que las familias y grupos se obtuvieron correctamente
            if (!$familias || !$grupos) {
                throw new Exception("No se pudieron cargar las familias o los grupos.");
            }
            error_log("Familias y grupos cargados exitosamente.");

            // Generar un token CSRF para evitar ataques de CSRF y almacenarlo en la sesión
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            error_log("Token CSRF generado para el formulario de creación de usuario.");

            // Pasar los datos a la vista del formulario de creación de usuario
            $params = [
                'csrf_token' => $_SESSION['csrf_token'],
                'familias' => $familias,
                'grupos' => $grupos
            ];

            $this->render('formCrearUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en formCrearUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario de creación de usuario.');
        }
    }




    public function actualizarUsuario()
    {
        $conexion = $this->modelo->getConexion();
        try {
            error_log("Entrando en actualizarUsuario()");

            // Validar nivel de usuario y permisos
            if (
                !isset($_SESSION['usuario']['nivel_usuario']) ||
                ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' &&
                    !($this->esAdmin() && isset($_GET['idUser']) && $this->perteneceAFamiliaOGrupo($_GET['idUser'])))
            ) {
                error_log("Permiso denegado: El usuario no tiene permisos para actualizar este usuario.");
                throw new Exception('No tienes permisos para actualizar este usuario.');
            }

            $m = new GastosModelo();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUser'])) {
                $idUser = $_POST['idUser'];
                error_log("Procesando actualización para el usuario con ID: $idUser");

                // Verificar token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    error_log("Token CSRF inválido para la actualización del usuario con ID: $idUser");
                    throw new Exception('Token CSRF inválido.');
                }

                // Recoger y validar los datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono') ?? ''; // Opcional
                $fecha_nacimiento = recoge('fecha_nacimiento') ?? ''; // Opcional
                $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
                $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
                $nivel_usuario = ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : 'usuario';

                $errores = [];
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);

                // Validación del teléfono solo si no está vacío
                if (!empty($telefono) && !preg_match('/^\d{9}$/', $telefono)) {
                    $errores['telefono'] = "El número de teléfono debe tener 9 dígitos.";
                }

                // Validación de la fecha de nacimiento solo si no está vacía
                if (!empty($fecha_nacimiento) && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha_nacimiento)) {
                    $errores['fecha_nacimiento'] = "La fecha debe tener el formato dd/mm/yyyy.";
                }

                // Validar existencia de familia y grupo
                if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
                    error_log("Error: La familia con ID $idFamilia no existe.");
                    $errores['familia'] = 'La familia seleccionada no existe.';
                }
                if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
                    error_log("Error: El grupo con ID $idGrupo no existe.");
                    $errores['grupo'] = 'El grupo seleccionado no existe.';
                }

                if (empty($errores)) {
                    $conexion->beginTransaction();
                    error_log("Iniciando transacción para la actualización del usuario con ID: $idUser");

                    // Actualizar datos de usuario
                    if (!$m->actualizarUsuario($idUser, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario)) {
                        error_log("Error al actualizar los datos del usuario con ID: $idUser");
                        throw new Exception('Error al actualizar los datos del usuario.');
                    }

                    // Actualizar familia y grupo
                    if ($idFamilia) {
                        $sqlFamilia = "UPDATE usuarios_familias SET idFamilia = :idFamilia WHERE idUser = :idUser";
                        $stmtFamilia = $conexion->prepare($sqlFamilia);
                        $stmtFamilia->execute([':idFamilia' => $idFamilia, ':idUser' => $idUser]);
                    }
                    if ($idGrupo) {
                        $sqlGrupo = "UPDATE usuarios_grupos SET idGrupo = :idGrupo WHERE idUser = :idUser";
                        $stmtGrupo = $conexion->prepare($sqlGrupo);
                        $stmtGrupo->execute([':idGrupo' => $idGrupo, ':idUser' => $idUser]);
                    }

                    $conexion->commit();
                    $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente';
                    header('Location: index.php?ctl=listarUsuarios');
                    exit();
                } else {
                    $params['errores'] = $errores;
                    $params['usuario'] = compact('nombre', 'apellido', 'alias', 'email', 'telefono', 'fecha_nacimiento', 'nivel_usuario', 'idFamilia', 'idGrupo');
                }
            } else {
                throw new Exception('Método de solicitud no permitido o ID de usuario no proporcionado.');
            }

            // Preparar datos para la vista de edición
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();
            $params['familias'] = $familias;
            $params['grupos'] = $grupos;
            $params['csrf_token'] = $_SESSION['csrf_token'];

            $this->render('formEditarUsuario.php', $params);
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en actualizarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    // editar usuario último
    public function editarUsuario()
    {
        $conexion = $this->modelo->getConexion();
        try {
            error_log("Entrando en editarUsuario()");

            // Validar nivel de usuario y permisos
            if (
                !isset($_SESSION['usuario']['nivel_usuario']) ||
                ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' &&
                    !($this->esAdmin() && isset($_GET['idUser']) && $this->perteneceAFamiliaOGrupo($_GET['idUser'])))
            ) {
                error_log("Permiso denegado: El usuario no tiene permisos para editar este usuario.");
                throw new Exception('No tienes permisos para editar este usuario.');
            }

            // Verificar si se ha pasado el ID del usuario a editar
            if (!isset($_GET['idUser'])) {
                throw new Exception("ID de usuario no proporcionado.");
            }

            $idUser = $_GET['idUser'];
            $m = new GastosModelo();

            // Obtener los datos del usuario a editar
            $usuario = $m->obtenerUsuarioPorId($idUser);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado con ID: $idUser");
            }

            // Obtener listas de familias y grupos para los desplegables
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Generar token CSRF para el formulario de edición
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Pasar los datos a la vista del formulario de edición
            $params = [
                'csrf_token' => $_SESSION['csrf_token'],
                'familias' => $familias,
                'grupos' => $grupos,
                'usuario' => $usuario,
                'idUser' => $idUser
            ];

            // Renderizar el formulario de edición
            $this->render('formEditarUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar la edición del usuario: ' . $e->getMessage());
        }
    }


    // Eliminar usuario
    public function eliminarUsuario()
    {
        $conexion = $this->modelo->getConexion();
        try {
            // Verificación de permisos
            if (
                $_SESSION['usuario']['nivel_usuario'] !== 'superadmin' &&
                !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['idUser']))
            ) {
                throw new Exception('No tienes permisos para eliminar este usuario.');
            }

            // Obtener ID del usuario a eliminar
            $idUser = isset($_GET['idUser']) ? (int)$_GET['idUser'] : null;

            if (!$idUser) {
                throw new Exception('ID de usuario no proporcionado.');
            }

            $m = new GastosModelo();

            // Verificar si el usuario existe
            $usuario = $m->obtenerUsuarioPorId($idUser);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            // Iniciar transacción
            $conexion->beginTransaction();

            // Eliminar registros de gastos e ingresos asociados al usuario
            if (!$m->eliminarGastosPorUsuario($idUser) || !$m->eliminarIngresosPorUsuario($idUser)) {
                throw new Exception('Error al eliminar los registros de gastos o ingresos del usuario.');
            }

            // Eliminar el usuario
            if (!$m->eliminarUsuarioPorId($idUser)) {
                throw new Exception('Error al eliminar el usuario.');
            }

            // Confirmar transacción
            $conexion->commit();

            // Redirección después de eliminación exitosa con mensaje de éxito
            $_SESSION['mensaje_exito'] = 'Usuario eliminado correctamente';
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        } catch (Exception $e) {
            // Rollback en caso de error
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en eliminarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
