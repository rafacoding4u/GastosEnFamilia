<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class UsuarioController
{
    public function __construct()
    {
        // Verifica si el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ctl=iniciarSesion');
            exit();
        }
    }

    // Registro de usuario
    public function registro()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' && $_SESSION['usuario']['nivel_usuario'] !== 'admin') {
                throw new Exception('No tienes permisos para crear usuarios.');
            }

            $params = array(
                'nombre' => '',
                'apellido' => '',
                'alias' => '',
                'contrasenya' => '',
                'email' => '',
                'telefono' => '',
                'nivel_usuario' => 'usuario',
                'idFamilia' => null,
                'idGrupo' => null,
            );
            $errores = array();
            $m = new GastosModelo();

            // Obtener familias y grupos según el nivel del usuario
            if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') {
                $familias = $m->obtenerFamilias();
                $grupos = $m->obtenerGrupos();
            } else {
                $familias = $m->obtenerFamiliasPorAdministrador($_SESSION['usuario']['id']);
                $grupos = $m->obtenerGruposPorAdministrador($_SESSION['usuario']['id']);
            }

            if (isset($_POST['bRegistro'])) {
                // Recoger datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $contrasenya = recoge('contrasenya');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $nivel_usuario = recoge('nivel_usuario');
                $idGrupoFamilia = recoge('idGrupoFamilia');
                $passwordFamiliaGrupo = recoge('passwordGrupoFamilia');
                $nombreNuevo = recoge('nombre_nuevo');
                $passwordNuevo = recoge('password_nuevo');

                // Validación de datos
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cContrasenya($contrasenya, $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                // Asignar a grupo o familia
                $idFamilia = null;
                $idGrupo = null;
                if (!empty($idGrupoFamilia)) {
                    if (strpos($idGrupoFamilia, 'grupo_') === 0) {
                        $idGrupo = substr($idGrupoFamilia, 6);
                        if (!$m->verificarPasswordGrupo($idGrupo, $passwordFamiliaGrupo)) {
                            $errores['idGrupo'] = "Contraseña del grupo incorrecta.";
                        }
                    } elseif (strpos($idGrupoFamilia, 'familia_') === 0) {
                        $idFamilia = substr($idGrupoFamilia, 8);
                        if (!$m->verificarPasswordFamilia($idFamilia, $passwordFamiliaGrupo)) {
                            $errores['idFamilia'] = "Contraseña de la familia incorrecta.";
                        }
                    }
                }

                // Crear nuevo grupo o familia si es necesario
                if (!empty($nombreNuevo) && !empty($passwordNuevo)) {
                    $hashedPasswordNuevo = encriptar($passwordNuevo);
                    if ($_POST['tipo_vinculo'] == 'grupo') {
                        $idGrupo = $m->insertarGrupo($nombreNuevo, $hashedPasswordNuevo);
                    } elseif ($_POST['tipo_vinculo'] == 'familia') {
                        $idFamilia = $m->insertarFamilia($nombreNuevo, $hashedPasswordNuevo);
                    }
                }

                // Insertar usuario si no hay errores
                if (empty($errores)) {
                    $hashedPassword = encriptar($contrasenya);
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, null, $email, $telefono)) {
                        $idUsuario = $m->obtenerUltimoId();

                        // Asignar usuario a familia y/o grupo
                        if ($idFamilia) {
                            $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                        }
                        if ($idGrupo) {
                            $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                        }

                        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo insertar el usuario. Revisa los datos.';
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            $params['familias'] = $familias;
            $params['grupos'] = $grupos;
            $this->render('formRegistro.php', $params);
        } catch (Exception $e) {
            error_log("Error en registro(): " . $e->getMessage());
            $this->redireccionarError("Error al registrar usuario.");
        }
    }

    public function crearUsuario()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                throw new Exception('No tienes permisos para crear un usuario.');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $m = new GastosModelo();
                // Aquí puedes implementar la lógica de creación del usuario,
                // similar a lo que ya tienes en el método `registro()`
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $fecha_nacimiento = recoge('fecha_nacimiento');
                $contrasenya = recoge('contrasenya');
                $nivel_usuario = recoge('nivel_usuario');
                $idFamilia = recoge('idFamilia');
                $idGrupo = recoge('idGrupo');

                $errores = [];
                // Validaciones (ya puedes usar las funciones cTexto, cEmail, etc)
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);
                cContrasenya($contrasenya, $errores);

                // Inserción del usuario si no hay errores
                if (empty($errores)) {
                    $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $idFamilia, $idGrupo)) {
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        throw new Exception('No se pudo crear el usuario.');
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            // Renderiza el formulario de creación si no se ha enviado POST
            $m = new GastosModelo();
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();
            $params = array(
                'familias' => $familias,
                'grupos' => $grupos,
            );
            $this->render('formCrearUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en crearUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al crear el usuario.');
        }
    }

    public function formCrearUsuario()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
                $this->redireccionarError('Acceso denegado. Solo superadmin puede crear usuarios.');
                return;
            }

            // Obtener las familias y grupos registrados
            $m = new GastosModelo();
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Generar un token CSRF para evitar ataques de CSRF y almacenarlo en la sesión
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Pasar las listas de familias, grupos y el token CSRF a la vista del formulario de creación de usuario
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

    public function editarUsuario()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' && !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['id']))) {
                throw new Exception('No tienes permisos para editar este usuario.');
            }

            $m = new GastosModelo();
            if (isset($_GET['id'])) {
                $usuario = $m->obtenerUsuarioPorId($_GET['id']);
                if (!$usuario) {
                    throw new Exception('Usuario no encontrado.');
                }
            }

            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            // Asegurarse de que todos los campos están en $params
            $params = array(
                'nombre' => $usuario['nombre'] ?? '',
                'apellido' => $usuario['apellido'] ?? '',
                'alias' => $usuario['alias'] ?? '',
                'email' => $usuario['email'] ?? '',
                'telefono' => $usuario['telefono'] ?? '',
                'idUser' => $usuario['idUser'] ?? '',
                'nivel_usuario' => $usuario['nivel_usuario'] ?? '',
                'idFamilia' => $usuario['idFamilia'] ?? null,
                'idGrupo' => $usuario['idGrupo'] ?? null,
                'familias' => $familias,
                'grupos' => $grupos
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarUsuario'])) {
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
                $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
                $nivel_usuario = ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : $usuario['nivel_usuario'];

                $errores = array();

                // Validaciones
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
                    $errores['familia'] = 'La familia seleccionada no existe.';
                }

                if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
                    $errores['grupo'] = 'El grupo seleccionado no existe.';
                }

                // Actualizar si no hay errores
                if (empty($errores)) {
                    if ($m->actualizarUsuario($usuario['idUser'], $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $idFamilia, $idGrupo)) {
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar el usuario.';
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            $this->render('formEditarUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en editarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el usuario.');
        }
    }


    public function actualizarUsuario()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' && !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['id']))) {
                throw new Exception('No tienes permisos para actualizar este usuario.');
            }

            $m = new GastosModelo();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $idUsuario = $_GET['id'];
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
                $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
                $nivel_usuario = ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : 'usuario';

                $errores = array();

                // Validaciones
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
                    $errores['familia'] = 'La familia seleccionada no existe.';
                }

                if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
                    $errores['grupo'] = 'El grupo seleccionado no existe.';
                }

                // Actualizar si no hay errores
                if (empty($errores)) {
                    if ($m->actualizarUsuario($idUsuario, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $idFamilia, $idGrupo)) {
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo actualizar el usuario.';
                    }
                } else {
                    $params['errores'] = $errores;
                }
            } else {
                throw new Exception('Método de solicitud no permitido o ID de usuario no proporcionado.');
            }

            // Renderizar el formulario con los errores si ocurre un problema
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            $params = array(
                'familias' => $familias,
                'grupos' => $grupos,
                'errores' => $errores
            );

            $this->render('formEditarUsuario.php', $params);
        } catch (Exception $e) {
            error_log("Error en actualizarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar el usuario.');
        }
    }


    // Eliminar usuario
    public function eliminarUsuario()
    {
        try {
            if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' && !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['id']))) {
                throw new Exception('No tienes permisos para eliminar este usuario.');
            }

            $idUsuario = recoge('id');
            $m = new GastosModelo();
            $usuario = $m->obtenerUsuarioPorId($idUsuario);

            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            if ($m->eliminarGastosPorUsuario($idUsuario) && $m->eliminarIngresosPorUsuario($idUsuario) && $m->eliminarUsuario($idUsuario)) {
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            } else {
                throw new Exception('Error al eliminar el usuario o sus registros.');
            }
        } catch (Exception $e) {
            error_log("Error en eliminarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el usuario.');
        }
    }

    // Listar usuarios
    public function listarUsuarios()
    {
        try {
            $m = new GastosModelo();
            $usuarios = $m->obtenerUsuarios();

            $params = array(
                'usuarios' => $usuarios,
                'mensaje' => 'Lista de usuarios registrados'
            );

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

    // Redireccionar en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }

    // Validar si el usuario es administrador
    private function esAdmin()
    {
        return $_SESSION['usuario']['nivel_usuario'] === 'admin';
    }

    // Verificar si el usuario pertenece a una familia o grupo
    private function perteneceAFamiliaOGrupo($idUsuario)
    {
        $m = new GastosModelo();
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        return ($usuario &&
            ($usuario['idFamilia'] == $_SESSION['usuario']['idFamilia'] ||
                $usuario['idGrupo'] == $_SESSION['usuario']['idGrupo']));
    }
}
