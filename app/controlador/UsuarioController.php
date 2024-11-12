<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';
require_once 'app/modelo/AdminGestion.php';

class UsuarioController
{
    private $modelo;
    private $adminGestion;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
        $adminId = $_SESSION['usuario']['id'] ?? null;
        $this->adminGestion = new AdminGestion($adminId); // Asegúrate de que esta línea esté presente
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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Recogemos los datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono') ?: null;
                $fecha_nacimiento = recoge('fecha_nacimiento') ?: null;
                $contrasenya = recoge('contrasenya');
                $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
                $nivel_usuario = recoge('nivel_usuario');

                $idUser = $this->modelo->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);

                if (!$idUser) throw new Exception('Error al insertar el usuario.');
                error_log("Usuario creado con ID $idUser");

                // Asignación a familia y grupo usando AdminGestion
                $opcion_creacion = recoge('opcion_creacion');
                if (in_array($opcion_creacion, ['crear_familia', 'crear_ambos'])) {
                    $nombre_familia = recoge('nombre_nueva_familia');
                    $password_familia = recoge('password_nueva_familia');
                    if ($nombre_familia && $password_familia) {
                        $idFamilia = $this->modelo->insertarFamilia($nombre_familia, $password_familia);
                        if ($idFamilia) {
                            $this->adminGestion->asignarUsuarioAFamilia($idUser, $idFamilia);
                            $this->adminGestion->asignarAdministradorAFamilia($idUser, $idFamilia);
                        }
                    }
                }

                if (in_array($opcion_creacion, ['crear_grupo', 'crear_ambos'])) {
                    $nombre_grupo = recoge('nombre_nuevo_grupo');
                    $password_grupo = recoge('password_nuevo_grupo');
                    if ($nombre_grupo && $password_grupo) {
                        $idGrupo = $this->modelo->insertarGrupo($nombre_grupo, $password_grupo);
                        if ($idGrupo) {
                            $this->adminGestion->asignarUsuarioAGrupo($idUser, $idGrupo);
                            $this->adminGestion->asignarAdministradorAGrupo($idUser, $idGrupo);
                        }
                    }
                }

                // Redireccionar con mensaje de éxito
                $_SESSION['mensaje_exito'] = "Usuario creado con éxito.";
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            }

            $params = [
                'familias' => $this->modelo->obtenerFamilias(),
                'grupos' => $this->modelo->obtenerGrupos(),
                'csrf_token' => $_SESSION['csrf_token'],
            ];
            $this->render('formCrearUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en crearUsuario(): " . $e->getMessage());
            $this->render('formCrearUsuario.php', ['mensaje' => $e->getMessage()]);
        }
    }




    public function formCrearUsuario()
    {
        try {
            // Verificación del nivel de usuario y permisos según el rol
            $nivelUsuario = $_SESSION['usuario']['nivel_usuario'];
            $m = new GastosModelo();

            // Si el usuario es superadmin, tiene acceso completo para crear usuarios, familias y grupos sin límite
            if ($nivelUsuario === 'superadmin') {
                $familias = $m->obtenerFamilias();
                $grupos = $m->obtenerGrupos();
            } elseif ($nivelUsuario === 'admin') {
                // Para admin: obtén solo las familias y grupos que administra y aplica los límites de creación
                $adminGestion = new AdminGestion($_SESSION['usuario']['idUser']);
                $familias = $adminGestion->obtenerFamiliasAdministradas();
                $grupos = $adminGestion->obtenerGruposAdministrados();

                // Verificación de límite de creación
                if (count($familias) >= 10) {
                    $this->redireccionarError('Límite de familias alcanzado para este administrador.');
                    return;
                }
                if (count($grupos) >= 30) {
                    $this->redireccionarError('Límite de grupos alcanzado para este administrador.');
                    return;
                }
            } else {
                // Usuarios regulares no tienen permiso para crear usuarios
                $this->redireccionarError('Acceso denegado. No tienes permiso para crear usuarios.');
                return;
            }

            // Verificar que las familias y grupos se obtuvieron correctamente
            if (!$familias || !$grupos) {
                throw new Exception("No se pudieron cargar las familias o los grupos.");
            }
            error_log("Familias y grupos cargados exitosamente.");

            // Generar un token CSRF para evitar ataques de CSRF y almacenarlo en la sesión
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            error_log("Token CSRF generado para el formulario de creación de usuario.");

            // Pasar los datos a la vista del formulario de creación de usuario, excluyendo superadmin de opciones de rol
            $params = [
                'csrf_token' => $_SESSION['csrf_token'],
                'familias' => $familias,
                'grupos' => $grupos,
                'roles_disponibles' => ($nivelUsuario === 'superadmin') ? ['usuario', 'admin', 'superadmin'] : ['usuario', 'admin']
            ];

            // Cargar el formulario correspondiente (superadmin o admin)
            $vistaFormulario = ($nivelUsuario === 'superadmin') ? 'formCrearUsuario.php' : 'formCrearUsuarioAdmin.php';
            $this->render($vistaFormulario, $params);
        } catch (Exception $e) {
            error_log("Error en formCrearUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario de creación de usuario.');
        }
    }





    // Método para actualizar usuario
    public function actualizarUsuario()
    {
        $conexion = $this->modelo->getConexion();
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUser'])) {
                $idUser = $_POST['idUser'];

                // Recoger datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono') ?: null;
                $fecha_nacimiento = recoge('fecha_nacimiento') ?: null;
                $idFamilia = recoge('idFamilia') ?: null;
                $idGrupo = recoge('idGrupo') ?: null;

                // Actualizar usuario principal
                $this->modelo->actualizarUsuario($idUser, $nombre, $apellido, $alias, $email, $telefono, 'usuario', $fecha_nacimiento);

                // Usar AdminGestion para actualizar familias y grupos
                $this->adminGestion->eliminarFamiliasDeUsuario($idUser); // Cambiado a adminGestion
                $this->adminGestion->eliminarGruposDeUsuario($idUser);   // Cambiado a adminGestion

                if ($idFamilia) {
                    $this->adminGestion->asignarUsuarioAFamilia($idUser, $idFamilia); // Cambiado a adminGestion
                }
                if ($idGrupo) {
                    $this->adminGestion->asignarUsuarioAGrupo($idUser, $idGrupo); // Cambiado a adminGestion
                }

                $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente';
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            }
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en actualizarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

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
            if (!isset($_GET['idUser']) || !is_numeric($_GET['idUser'])) {
                throw new Exception("ID de usuario no proporcionado o no es válido.");
            }

            $idUser = (int)$_GET['idUser'];
            $m = new GastosModelo();

            // Obtener los datos del usuario a editar
            $usuario = $m->obtenerUsuarioPorId($idUser);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado con ID: $idUser");
            }

            // Verificar si el usuario es regular (si el admin solo puede editar usuarios regulares)
            if ($usuario['nivel_usuario'] === 'admin' && !$this->esSuperAdmin()) {
                throw new Exception("No tienes permisos para editar un administrador.");
            }

            // Obtener listas de familias y grupos para los desplegables
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Verificar que las listas de familias y grupos se hayan obtenido
            if ($familias === false || $grupos === false) {
                throw new Exception("Error al cargar familias o grupos.");
            }

            // Generar token CSRF para el formulario de edición
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            error_log("Token CSRF generado para el formulario de edición de usuario.");

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

            // Asignar nuevas familias al usuario (si se proporcionan en el formulario de edición)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['familias'])) {
                $familiasSeleccionadas = $_POST['familias'];
                $this->asignarFamiliasAUsuario($idUser, $familiasSeleccionadas);
                error_log("Familias asignadas exitosamente al usuario $idUser.");
            }
        } catch (Exception $e) {
            error_log("Error en editarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al cargar la edición del usuario: ' . $e->getMessage());
        }
    }

    // Eliminar usuario
    public function eliminarUsuario()
    {
        try {
            $idUser = $_GET['idUser'] ?? null;
            if (!$idUser) throw new Exception('ID de usuario no proporcionado.');

            $conexion = $this->modelo->getConexion();
            $conexion->beginTransaction();

            // Eliminar asociaciones de familias y grupos del usuario usando AdminGestion
            $this->adminGestion->eliminarFamiliasDeUsuario($idUser); // Cambiado a adminGestion
            $this->adminGestion->eliminarGruposDeUsuario($idUser);   // Cambiado a adminGestion

            // Eliminar usuario en sí
            $this->modelo->eliminarUsuarioPorId($idUser);

            $conexion->commit();
            $_SESSION['mensaje_exito'] = 'Usuario eliminado correctamente';
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en eliminarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
    // Método para verificar si el usuario actual es superadmin
    private function esSuperAdmin()
    {
        return isset($_SESSION['usuario']['nivel_usuario']) && $_SESSION['usuario']['nivel_usuario'] === 'superadmin';
    }

    // Método para asignar familias a un usuario
    private function asignarFamiliasAUsuario($idUser, $familiasSeleccionadas)
    {
        try {
            // Usar AdminGestion para eliminar y asignar familias
            $this->adminGestion->eliminarFamiliasDeUsuario($idUser); // Cambiado a adminGestion

            foreach ($familiasSeleccionadas as $idFamilia) {
                $this->adminGestion->asignarUsuarioAFamilia($idUser, $idFamilia); // Cambiado a adminGestion
            }
            error_log("Asignación de familias completada para el usuario $idUser.");
        } catch (Exception $e) {
            error_log("Error en asignarFamiliasAUsuario(): " . $e->getMessage());
            throw new Exception('Error al asignar familias al usuario.');
        }
    }
}
