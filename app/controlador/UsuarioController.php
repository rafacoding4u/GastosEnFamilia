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
            // Inicialización
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

            // Obtener familias y grupos (igual que en crearUsuario)
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Recoger datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $contrasenya = recoge('contrasenya');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $nivel_usuario = recoge('nivel_usuario');
                $fecha_nacimiento = recoge('fecha_nacimiento');
                $idFamilias = recoge('idFamilia');
                $idGrupos = recoge('idGrupo');
                $nombre_nueva_familia = recoge('nombre_nueva_familia');
                $password_nueva_familia = recoge('password_nueva_familia');
                $nombre_nuevo_grupo = recoge('nombre_nuevo_grupo');
                $password_nuevo_grupo = recoge('password_nuevo_grupo');
                $passwordFamiliaExistente = recoge('passwordFamiliaExistente');
                $passwordGrupoExistente = recoge('passwordGrupoExistente');

                // Validaciones
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cContrasenya($contrasenya, $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                // Validar alias existente
                if ($m->existeUsuario($alias)) {
                    $errores[] = "El alias ya está en uso.";
                }

                if (empty($errores)) {
                    // Insertar usuario
                    $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
                    $idUsuario = $m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);

                    // Verificar creación de nuevas familias y grupos
                    if (!empty($nombre_nueva_familia) && !empty($password_nueva_familia)) {
                        $idFamilia = $m->insertarFamilia($nombre_nueva_familia, encriptar($password_nueva_familia));
                        if ($idFamilia) {
                            $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                        }
                    }
                    if (!empty($nombre_nuevo_grupo) && !empty($password_nuevo_grupo)) {
                        $idGrupo = $m->insertarGrupo($nombre_nuevo_grupo, encriptar($password_nuevo_grupo));
                        if ($idGrupo) {
                            $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                        }
                    }

                    // Asignar a familias y grupos existentes
                    $idFamilias = is_array($idFamilias) ? $idFamilias : (!empty($idFamilias) ? [$idFamilias] : []);
                    foreach ($idFamilias as $idFamilia) {
                        if ($m->verificarPasswordFamilia($idFamilia, $passwordFamiliaExistente)) {
                            $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                        }
                    }

                    $idGrupos = is_array($idGrupos) ? $idGrupos : (!empty($idGrupos) ? [$idGrupos] : []);
                    foreach ($idGrupos as $idGrupo) {
                        if ($m->verificarPasswordGrupo($idGrupo, $passwordGrupoExistente)) {
                            $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                        }
                    }

                    // Redirigir tras éxito
                    $_SESSION['mensaje_exito'] = 'Usuario registrado correctamente.';
                    header('Location: index.php?ctl=login');
                    exit();
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
                $idFamilias = recoge('idFamilia'); // Recogemos un array de familias seleccionadas
                $idGrupos = recoge('idGrupo'); // Recogemos un array de grupos seleccionados
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

                // Verificar y asignar nueva familia
                if (!empty($nombre_nueva_familia) && !empty($password_nueva_familia)) {
                    error_log("Intentando crear nueva familia: $nombre_nueva_familia");
                    if (!$m->insertarFamilia($nombre_nueva_familia, $password_nueva_familia)) {
                        $errores[] = "No se pudo crear la nueva familia.";
                        error_log("Error al crear nueva familia: $nombre_nueva_familia");
                    } else {
                        $idFamilia = $m->obtenerUltimoId();
                        error_log("Familia creada con éxito con ID: $idFamilia");
                        $m->asignarUsuarioAFamilia($idUser, $idFamilia);

                        // Si el usuario es administrador, asignarlo como tal
                        if ($nivel_usuario === 'admin') {
                            $m->asignarAdministradorAFamilia($idUser, $idFamilia);
                            error_log("Usuario $idUser asignado como administrador a la familia $idFamilia");
                        }
                    }
                }

                // Asignar a múltiples familias seleccionadas
                $idFamilias = is_array($idFamilias) ? $idFamilias : (!empty($idFamilias) ? [$idFamilias] : []);
                if (!empty($idFamilias)) {
                    foreach ($idFamilias as $idFamilia) {
                        error_log("Asignando usuario $idUser a la familia ID: $idFamilia");
                        if (!$m->verificarPasswordFamilia($idFamilia, $passwordFamiliaExistente)) {
                            $errores[] = "Contraseña de familia incorrecta para la familia ID: $idFamilia.";
                        } else {
                            $m->asignarUsuarioAFamilia($idUser, $idFamilia);
                            error_log("Usuario $idUser asignado a la familia $idFamilia");

                            // Asignar como administrador a la familia si el rol es 'admin'
                            if ($nivel_usuario === 'admin') {
                                $m->asignarAdministradorAFamilia($idUser, $idFamilia);
                                error_log("Usuario $idUser asignado como administrador a la familia $idFamilia");
                            }
                        }
                    }
                }

                // Verificar y asignar nuevo grupo
                if (!empty($nombre_nuevo_grupo) && !empty($password_nuevo_grupo)) {
                    error_log("Intentando crear nuevo grupo: $nombre_nuevo_grupo");
                    if (!$m->insertarGrupo($nombre_nuevo_grupo, $password_nuevo_grupo)) {
                        $errores[] = "No se pudo crear el nuevo grupo.";
                        error_log("Error al crear nuevo grupo: $nombre_nuevo_grupo");
                    } else {
                        $idGrupo = $m->obtenerUltimoId();
                        error_log("Grupo creado con éxito con ID: $idGrupo");
                        $m->asignarUsuarioAGrupo($idUser, $idGrupo);

                        // Si el usuario es administrador, asignarlo como tal
                        if ($nivel_usuario === 'admin') {
                            $m->asignarAdministradorAGrupo($idUser, $idGrupo);
                            error_log("Usuario $idUser asignado como administrador al grupo $idGrupo");
                        }
                    }
                }

                // Asignar a múltiples grupos seleccionados
                $idGrupos = is_array($idGrupos) ? $idGrupos : (!empty($idGrupos) ? [$idGrupos] : []);
                if (!empty($idGrupos)) {
                    foreach ($idGrupos as $idGrupo) {
                        error_log("Asignando usuario $idUser al grupo ID: $idGrupo");
                        if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoExistente)) {
                            $errores[] = "Contraseña de grupo incorrecta para el grupo ID: $idGrupo.";
                        } else {
                            $m->asignarUsuarioAGrupo($idUser, $idGrupo);
                            error_log("Usuario $idUser asignado al grupo $idGrupo");

                            // Asignar como administrador al grupo si el rol es 'admin'
                            if ($nivel_usuario === 'admin') {
                                $m->asignarAdministradorAGrupo($idUser, $idGrupo);
                                error_log("Usuario $idUser asignado como administrador al grupo $idGrupo");
                            }
                        }
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

    public function buscarFamilias()
    {
        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            $m = new GastosModelo();
            $familias = $m->buscarFamiliasPorNombre($query);

            foreach ($familias as $familia) {
                echo '<div onclick="seleccionarFamilia(' . $familia['idFamilia'] . ', \'' . $familia['nombre_familia'] . '\')">'
                    . htmlspecialchars($familia['nombre_familia']) . '</div>';
            }
        }
    }

    public function buscarGrupos()
    {
        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            $m = new GastosModelo();
            $grupos = $m->buscarGruposPorNombre($query);

            foreach ($grupos as $grupo) {
                echo '<div onclick="seleccionarGrupo(' . $grupo['idGrupo'] . ', \'' . $grupo['nombre_grupo'] . '\')">'
                    . htmlspecialchars($grupo['nombre_grupo']) . '</div>';
            }
        }
    }
}
