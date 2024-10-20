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
                $fecha_nacimiento = recoge('fecha_nacimiento');

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

                // Validar la existencia de la familia o grupo
                if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
                    $errores['familia'] = 'La familia seleccionada no existe.';
                }
                if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
                    $errores['grupo'] = 'El grupo seleccionado no existe.';
                }

                // Crear nuevo grupo o familia si es necesario
                if (!empty($nombreNuevo) && !empty($passwordNuevo)) {
                    $hashedPasswordNuevo = encriptar($passwordNuevo);
                    if ($_POST['tipo_vinculo'] == 'grupo') {
                        $idGrupo = $m->insertarGrupo($nombreNuevo, $hashedPasswordNuevo);
                        if (!$idGrupo) {
                            $errores['nuevo_grupo'] = "Error al crear el grupo.";
                            error_log("Error al crear el grupo con nombre: $nombreNuevo");
                        }
                    } elseif ($_POST['tipo_vinculo'] == 'familia') {
                        $idFamilia = $m->insertarFamilia($nombreNuevo, $hashedPasswordNuevo);
                        if (!$idFamilia) {
                            $errores['nueva_familia'] = "Error al crear la familia.";
                            error_log("Error al crear la familia con nombre: $nombreNuevo");
                        }
                    }
                }

                // Insertar usuario si no hay errores
                if (empty($errores)) {
                    $hashedPassword = encriptar($contrasenya);
                    // Insertar el usuario con solo 8 parámetros
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono)) {
                        $idUsuario = $m->obtenerUltimoId();

                        // Asignar usuario a familia y/o grupo si no existe duplicado
                        if ($idFamilia && !$m->usuarioYaEnFamilia($idUsuario, $idFamilia)) {
                            if (!$m->asignarUsuarioAFamilia($idUsuario, $idFamilia)) {
                                error_log("Error al asignar el usuario ID $idUsuario a la familia ID $idFamilia");
                            }
                        }
                        if ($idGrupo && !$m->usuarioYaEnGrupo($idUsuario, $idGrupo)) {
                            if (!$m->asignarUsuarioAGrupo($idUsuario, $idGrupo)) {
                                error_log("Error al asignar el usuario ID $idUsuario al grupo ID $idGrupo");
                            }
                        }

                        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo insertar el usuario. Revisa los datos.';
                        error_log("Error al insertar el usuario con alias: $alias");
                    }
                } else {
                    $params['errores'] = $errores;
                    foreach ($errores as $campo => $error) {
                        error_log("Error en el campo $campo: $error");
                    }
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
        // Registrar inicio de proceso
        error_log("Iniciando proceso de creación de usuario...");

        // Verificar nivel de usuario
        if ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin') {
            throw new Exception('No tienes permisos para crear un usuario.');
        }

        // Inicializar el modelo antes de cualquier uso
        $m = new GastosModelo();
        error_log("Modelo GastosModelo instanciado correctamente.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recoger datos del formulario
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
            $nombre_nueva_familia = recoge('nombre_nueva_familia');
            $password_nueva_familia = recoge('password_nueva_familia');
            $nombre_nuevo_grupo = recoge('nombre_nuevo_grupo');
            $password_nuevo_grupo = recoge('password_nuevo_grupo');
            $passwordFamiliaExistente = recoge('passwordFamiliaExistente');
            $passwordGrupoExistente = recoge('passwordGrupoExistente');

            // Registrar datos recibidos
            error_log("Datos recibidos: nombre=$nombre, apellido=$apellido, alias=$alias, email=$email, teléfono=$telefono, fecha_nacimiento=$fecha_nacimiento, nivel_usuario=$nivel_usuario");

            // Validaciones
            $errores = [];
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($alias, "alias", $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);
            cContrasenya($contrasenya, $errores);

            // Validar si el alias ya está registrado
            if ($m->existeUsuario($alias)) {
                $errores[] = "El alias ya está en uso.";
                error_log("El alias '$alias' ya está registrado.");
            }

            // Verificar errores de validación
            if (!empty($errores)) {
                $params['errores'] = $errores;
                error_log("Errores de validación: " . print_r($errores, true));

                // Recargar el formulario con los datos y errores
                $familias = $m->obtenerFamilias();
                $grupos = $m->obtenerGrupos();
                $params['familias'] = $familias;
                $params['grupos'] = $grupos;
                $this->render('formCrearUsuario.php', $params);
                return;
            }

            // Encriptar la contraseña
            $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
            error_log("Contraseña encriptada con éxito.");

            // Insertar usuario en la base de datos
            $idUser = $m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);

            if (!$idUser) {
                throw new Exception('Error al insertar el usuario.');
            }
            error_log("Usuario creado con ID $idUser");

            // Verificar y asignar familia
            if (!empty($nombre_nueva_familia) && !empty($password_nueva_familia)) {
                error_log("Intentando crear nueva familia: $nombre_nueva_familia");
                if (!$m->insertarFamilia($nombre_nueva_familia, $password_nueva_familia)) {
                    $errores[] = "No se pudo crear la nueva familia.";
                    error_log("Error al crear nueva familia: $nombre_nueva_familia");
                } else {
                    $idFamilia = $m->obtenerUltimoId();
                    error_log("Familia creada con éxito con ID: $idFamilia");
                }
            } elseif (!empty($idFamilia)) {
                // Validar contraseña para la familia existente
                error_log("Verificando contraseña para la familia ID: $idFamilia");
                if (!$m->verificarPasswordFamilia($idFamilia, $passwordFamiliaExistente)) {
                    $errores[] = "Contraseña de familia incorrecta.";
                    error_log("Contraseña incorrecta para familia ID: $idFamilia");
                }
            }

            // Verificar y asignar grupo
            if (!empty($nombre_nuevo_grupo) && !empty($password_nuevo_grupo)) {
                error_log("Intentando crear nuevo grupo: $nombre_nuevo_grupo");
                if (!$m->insertarGrupo($nombre_nuevo_grupo, $password_nuevo_grupo)) {
                    $errores[] = "No se pudo crear el nuevo grupo.";
                    error_log("Error al crear nuevo grupo: $nombre_nuevo_grupo");
                } else {
                    $idGrupo = $m->obtenerUltimoId();
                    error_log("Grupo creado con éxito con ID: $idGrupo");
                }
            } elseif (!empty($idGrupo)) {
                // Validar contraseña para el grupo existente
                error_log("Verificando contraseña para el grupo ID: $idGrupo");
                if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoExistente)) {
                    $errores[] = "Contraseña de grupo incorrecta.";
                    error_log("Contraseña incorrecta para grupo ID: $idGrupo");
                }
            }

            // Si hay errores, recargar el formulario con mensajes
            if (!empty($errores)) {
                $familias = $m->obtenerFamilias();
                $grupos = $m->obtenerGrupos();
                $params['errores'] = $errores;
                error_log("Errores detectados durante la creación: " . print_r($errores, true));
                $params['familias'] = $familias;
                $params['grupos'] = $grupos;
                $this->render('formCrearUsuario.php', $params);
                return;
            }

            // Asignar usuario a la familia o grupo si fue seleccionado
            if (!empty($idFamilia)) {
                $m->asignarUsuarioAFamilia($idUser, $idFamilia);
                error_log("Usuario $idUser asignado a la familia $idFamilia");
            }
            if (!empty($idGrupo)) {
                $m->asignarUsuarioAGrupo($idUser, $idGrupo);
                error_log("Usuario $idUser asignado al grupo $idGrupo");
            }

            // Mensaje de éxito y redirección
            $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
            error_log("Redirigiendo a listarUsuarios...");
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        }

        // Renderiza el formulario si no se ha enviado POST
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
        $params = array(
            'familias' => $familias,
            'grupos' => $grupos,
        );
        $this->render('formCrearUsuario.php', $params);
    } catch (Exception $e) {
        error_log("Error en crearUsuario(): " . $e->getMessage());

        // Mostrar mensaje de error
        $m = new GastosModelo(); // Inicializamos el modelo antes de usarlo en el bloque catch
        $params['mensaje'] = 'Error al crear el usuario: ' . $e->getMessage();
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
        $params['familias'] = $familias;
        $params['grupos'] = $grupos;
        $this->render('formCrearUsuario.php', $params);
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

    // Eliminar referencias de idFamilia y idGrupo en los métodos restantes
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
                'familias' => $familias,
                'grupos' => $grupos
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarUsuario'])) {
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $nivel_usuario = ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : $usuario['nivel_usuario'];

                $errores = array();

                // Validaciones
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                // Actualizar si no hay errores
                if (empty($errores)) {
                    if ($m->actualizarUsuario($usuario['idUser'], $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario)) {
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
            ($m->usuarioYaEnFamilia($idUsuario, $_SESSION['usuario']['idFamilia']) ||
                $m->usuarioYaEnGrupo($idUsuario, $_SESSION['usuario']['idGrupo'])));
    }
}
