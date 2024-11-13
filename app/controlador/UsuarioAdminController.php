<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';
require_once 'app/modelo/AdminGestion.php';  // Incluir la clase auxiliar

class UsuarioAdminController
{
    private $gestionAdmin;
    private $adminId;

    public function __construct()
    {
        $this->adminId = $_SESSION['usuario']['id'];
        $this->gestionAdmin = new AdminGestion($this->adminId);  // Instancia AdminGestion
    }

    // Listar solo los usuarios gestionados por el Admin
    public function listarUsuariosAdmin()
    {
        try {
            $filtros = [
                'nombre' => $_GET['nombre'] ?? '',
                'apellido' => $_GET['apellido'] ?? '',
                'alias' => $_GET['alias'] ?? '',
                'email' => $_GET['email'] ?? '',
                'nivel_usuario' => $_GET['nivel_usuario'] ?? '',
            ];

            $usuarios = $this->gestionAdmin->obtenerUsuariosGestionados($this->adminId, $filtros);

            $params = [
                'usuarios' => $usuarios,
                'mensaje' => 'Lista de usuarios bajo su administración'
            ];

            $this->render('listarUsuariosAdmin.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarUsuariosAdmin(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los usuarios.');
        }
    }


    // Crear usuario bajo los límites de familia/grupo
    public function crearUsuario()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar y recoger datos de usuario desde el formulario
                $datosUsuario = [
                    'nombre' => recoge('nombre'),
                    'apellido' => recoge('apellido'),
                    'alias' => recoge('alias'),
                    'email' => recoge('email'),
                    'telefono' => recoge('telefono'),
                    'fecha_nacimiento' => recoge('fecha_nacimiento'),
                    'contrasenya' => password_hash(recoge('contrasenya'), PASSWORD_BCRYPT),
                    'nivel_usuario' => recoge('nivel_usuario'),
                    'idFamilia' => recoge('idFamilia'),
                    'idGrupo' => recoge('idGrupo')
                ];

                // Validar y aplicar límites en familia o grupo
                $this->gestionAdmin->crearUsuario($datosUsuario);
                $_SESSION['mensaje_exito'] = "Usuario creado exitosamente.";
                header('Location: index.php?ctl=listarUsuariosAdmin');
                exit();
            }

            // Datos para la vista de creación de usuario
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'familias' => $this->gestionAdmin->obtenerFamiliasAdministradas(),
                'grupos' => $this->gestionAdmin->obtenerGruposAdministrados(),
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formCrearUsuarioAdmin.php', $params);
        } catch (Exception $e) {
            error_log("Error en crearUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al crear el usuario.');
        }
    }

    // Editar usuario regular gestionado por el Admin
    public function editarUsuarioRegular()
    {
        try {
            $idUser = $_GET['idUser'] ?? null;
            if (!$idUser) {
                throw new Exception('ID de usuario no especificado.');
            }

            // Validar acceso del admin al usuario regular
            $usuario = $this->validarAccesoUsuarioRegular($idUser);

            // Cargar listas de familias y grupos que gestiona el admin
            $familias = $this->gestionAdmin->obtenerFamiliasAdministradas();
            $grupos = $this->gestionAdmin->obtenerGruposAdministrados();

            // Verificar contenido de las listas de familias y grupos
            error_log("Contenido de familias: " . json_encode($familias));
            error_log("Contenido de grupos: " . json_encode($grupos));

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nuevosDatos = [
                    'nombre' => recoge('nombre'),
                    'apellido' => recoge('apellido'),
                    'alias' => recoge('alias'),
                    'email' => recoge('email'),
                    'telefono' => recoge('telefono'),
                    'fecha_nacimiento' => recoge('fecha_nacimiento'),
                    'idFamilia' => $_POST['idFamilia'] ?? [],
                    'idGrupo' => $_POST['idGrupo'] ?? [],
                    'nivel_usuario' => recoge('nivel_usuario')
                ];
                $this->gestionAdmin->editarUsuarioRegular($idUser, $nuevosDatos);
                $_SESSION['mensaje_exito'] = "Usuario editado correctamente.";
                header('Location: index.php?ctl=listarUsuariosAdmin');
                exit();
            }

            // Preparar datos para la vista, incluyendo familias y grupos
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'usuario' => $usuario,
                'familias' => $familias,
                'grupos' => $grupos,
                'csrf_token' => $_SESSION['csrf_token']
            ];
            $this->render('formEditarUsuarioAdmin.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarUsuarioRegular(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el usuario.');
        }
    }



    // Eliminar usuario regular gestionado por el Admin
    public function eliminarUsuarioRegular()
    {
        try {
            $idUser = $_GET['idUser'] ?? null;
            if (!$idUser) {
                throw new Exception('ID de usuario no especificado.');
            }

            $usuario = $this->validarAccesoUsuarioRegular($idUser);
            $this->gestionAdmin->eliminarUsuarioRegular($idUser);
            $_SESSION['mensaje_exito'] = "Usuario eliminado correctamente.";
            header('Location: index.php?ctl=listarUsuariosAdmin');
            exit();
        } catch (Exception $e) {
            error_log("Error en eliminarUsuarioRegular(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el usuario.');
        }
    }

    // Método privado para validar el acceso del Admin a un usuario regular
    private function validarAccesoUsuarioRegular($idUser)
    {
        $usuarios = $this->gestionAdmin->listarUsuariosGestionados();

        // Filtrar usuarios asegurando que 'id' existe en cada elemento
        $usuario = array_filter($usuarios, function ($u) use ($idUser) {
            return isset($u['idUser']) && $u['idUser'] == $idUser && isset($u['nivel_usuario']) && $u['nivel_usuario'] === 'usuario';
        });

        if (empty($usuario)) {
            throw new Exception("No tienes permisos para gestionar este usuario.");
        }

        return current($usuario);  // Devuelve el primer usuario encontrado
    }


    // Renderizar vistas
    private function render($vista, $params = [])
    {
        try {
            extract($params);
            ob_start();
            require __DIR__ . "/../../web/templates/$vista";
            $contenido = ob_get_clean();
            require __DIR__ . "/../../web/templates/layout.php";
        } catch (Exception $e) {
            error_log("Error en render(): " . $e->getMessage());
            $this->redireccionarError('Error al renderizar la vista.');
        }
    }

    // Redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
    public function formCrearUsuarioAdmin()
    {
        try {
            // Verificación del nivel de usuario
            if ($_SESSION['usuario']['nivel_usuario'] !== 'admin') {
                $this->redireccionarError('Acceso denegado. Solo los administradores pueden crear usuarios.');
                return;
            }

            // Inicialización de la clase de gestión específica para administradores
            $adminGestion = new AdminGestion($this->adminId); // Usa $this->adminId para consistencia
            error_log("Cargando familias y grupos para el formulario de creación de usuario (Admin)...");

            // Obtener las familias y grupos administrados por el administrador
            $familias = $adminGestion->obtenerFamiliasAdministradas();
            $grupos = $adminGestion->obtenerGruposAdministrados();

            // Verificar que las familias y grupos se obtuvieron correctamente
            if (empty($familias) || empty($grupos)) {
                throw new Exception("No se pudieron cargar las familias o los grupos para el administrador.");
            }
            error_log("Familias y grupos para administrador cargados exitosamente.");

            // Generar un token CSRF para el formulario
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            error_log("Token CSRF generado para el formulario de creación de usuario (Admin).");

            // Pasar los datos a la vista del formulario de creación de usuario
            $params = [
                'csrf_token' => $_SESSION['csrf_token'],
                'familias' => $familias,
                'grupos' => $grupos,
                'roles_disponibles' => ['usuario', 'admin'], // Roles permitidos para el administrador
            ];

            // Renderizar la vista del formulario de creación de usuario específico para administradores
            $this->render('formCrearUsuarioAdmin.php', $params);
        } catch (Exception $e) {
            error_log("Error en formCrearUsuarioAdmin(): " . $e->getMessage());
            $this->redireccionarError('Error al mostrar el formulario de creación de usuario para administrador.');
        }
    }
}
