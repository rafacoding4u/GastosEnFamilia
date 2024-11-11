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
            $usuarios = $this->gestionAdmin->listarUsuariosGestionados();  // Solo usuarios bajo su administración
            $params = [
                'usuarios' => $usuarios,
                'mensaje' => 'Lista de usuarios bajo su administración'
            ];
            $this->render('listarUsuariosAdmin.php', $params);  // Vista específica para Admin
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

            $usuario = $this->validarAccesoUsuarioRegular($idUser);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nuevosDatos = [
                    'nombre' => recoge('nombre'),
                    'apellido' => recoge('apellido'),
                    'alias' => recoge('alias'),
                    'email' => recoge('email')
                ];
                $this->gestionAdmin->editarUsuarioRegular($idUser, $nuevosDatos);
                $_SESSION['mensaje_exito'] = "Usuario editado correctamente.";
                header('Location: index.php?ctl=listarUsuariosAdmin');
                exit();
            }

            // Cargar datos del usuario para la edición
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $params = [
                'usuario' => $usuario,
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
        $usuario = array_filter($usuarios, function ($u) use ($idUser) {
            return $u['id'] == $idUser && $u['nivel_usuario'] === 'usuario';
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
}
