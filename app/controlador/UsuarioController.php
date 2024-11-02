<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class UsuarioController
{
    private $modelo; // Definir la propiedad $modelo

    public function __construct()
    {
        // Inicializar el modelo en el constructor
        $this->modelo = new GastosModelo();

        // Verifica si el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ctl=iniciarSesion');
            exit();
        }
    }

    public function ejecutarActualizacionContraseñas()
    {
        // Actualizar todas las contraseñas de usuarios, grupos y familias
        $this->modelo->actualizarContraseñasUsuariosGruposFamilias();
    }

    // Función para resetear la contraseña del superadmin
    public function resetearPasswordSuperadmin()
    {
        $nuevaContrasena = 'Temp@1234ComplexLa7890@2@';
        $hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Actualización de la contraseña del superadmin
        $sql = "UPDATE usuarios SET contrasenya = :hashedPassword WHERE nivel_usuario = 'superadmin'";
        $stmt = $this->modelo->getConexion()->prepare($sql);
        $stmt->bindValue(':hashedPassword', $hashedPassword);
        $stmt->execute();

        echo "Contraseña del superadmin actualizada correctamente.";
    }

    public function asignarPasswordPremium($idUser, $passwordPremium)
    {
        // Generar el hash de la nueva contraseña premium
        $hashedPasswordPremium = password_hash($passwordPremium, PASSWORD_DEFAULT);

        // Ejecutar la consulta para actualizar la contraseña premium del usuario
        $sql = "UPDATE usuarios SET password_premium = :hashedPasswordPremium WHERE idUser = :idUser";
        $stmt = $this->modelo->getConexion()->prepare($sql);
        $stmt->bindValue(':hashedPasswordPremium', $hashedPasswordPremium);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        echo "Contraseña premium asignada correctamente al usuario con ID $idUser.";
    }

    public function verificarPasswordPremium($idUser, $passwordIntroducido)
    {
        $sql = "SELECT password_premium FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->modelo->getConexion()->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la contraseña introducida coincide con el hash almacenado
        return $resultado && password_verify($passwordIntroducido, $resultado['password_premium']);
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
        $conexion = $this->modelo->getConexion(); // Obtener la conexión una vez para reutilizar
        try {
            error_log("Entrando en actualizarUsuario()");

            // Verificar permisos
            if (!isset($_SESSION['usuario']['nivel_usuario']) || ($_SESSION['usuario']['nivel_usuario'] !== 'superadmin' &&
                !($this->esAdmin() && isset($_GET['idUser']) && $this->perteneceAFamiliaOGrupo($_GET['idUser'])))) {
                throw new Exception('No tienes permisos para actualizar este usuario.');
            }

            $m = new GastosModelo();

            // Validar que sea una solicitud POST con ID de usuario
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUser'])) {
                $idUser = $_POST['idUser'];

                // Verificación para evitar que el usuario edite su propio usuario
                if ($idUser == $_SESSION['usuario']['id']) {
                    throw new Exception('No puedes editar tu propio usuario.');
                }

                // Verificar token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('Token CSRF inválido.');
                }

                // Recoger datos
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
                $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
                $nivel_usuario = ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : 'usuario';

                $errores = [];

                // Validar datos
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                // Validar la existencia de familia y grupo
                if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
                    $errores['familia'] = 'La familia seleccionada no existe.';
                }
                if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
                    $errores['grupo'] = 'El grupo seleccionado no existe.';
                }

                // Si no hay errores, actualizar en una transacción
                if (empty($errores)) {
                    $conexion->beginTransaction(); // Iniciar transacción

                    // Actualizar usuario
                    if (!$m->actualizarUsuario($idUser, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario)) {
                        throw new Exception('Error al actualizar los datos del usuario.');
                    }

                    // Actualizar familia
                    if ($idFamilia) {
                        $sqlFamilia = "UPDATE usuarios_familias SET idFamilia = :idFamilia WHERE idUser = :idUser";
                        $stmtFamilia = $conexion->prepare($sqlFamilia);
                        $stmtFamilia->execute([':idFamilia' => $idFamilia, ':idUser' => $idUser]);
                    }

                    // Actualizar grupo
                    if ($idGrupo) {
                        $sqlGrupo = "UPDATE usuarios_grupos SET idGrupo = :idGrupo WHERE idUser = :idUser";
                        $stmtGrupo = $conexion->prepare($sqlGrupo);
                        $stmtGrupo->execute([':idGrupo' => $idGrupo, ':idUser' => $idUser]);
                    }

                    $conexion->commit(); // Confirmar transacción

                    $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente';
                    header('Location: index.php?ctl=listarUsuarios');
                    exit();
                } else {
                    $params['errores'] = $errores;
                }
            } else {
                throw new Exception('Método de solicitud no permitido o ID de usuario no proporcionado.');
            }

            // Obtener datos para la vista si hay errores
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();

            $params = array(
                'familias' => $familias,
                'grupos' => $grupos,
                'errores' => $errores,
                'csrf_token' => $_SESSION['csrf_token'],
                'idUser' => $_POST['idUser'] ?? null
            );

            $this->render('formEditarUsuario.php', $params);
        } catch (Exception $e) {
            // Verificación para asegurarse de que solo se llama a rollBack() si hay una transacción activa
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en actualizarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }







    public function eliminarUsuario()
    {
        try {
            $idUser = recoge('id'); // Usuario que se intenta eliminar

            // Verificar que el usuario no intente eliminarse a sí mismo
            if ($idUser === $_SESSION['usuario']['id']) {
                throw new Exception('No puedes eliminar tu propio usuario.');
            }

            // Verificar permisos según el nivel de usuario
            if (
                $_SESSION['usuario']['nivel_usuario'] !== 'superadmin' &&
                !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($idUser))
            ) {
                throw new Exception('No tienes permisos para eliminar este usuario.');
            }

            $m = new GastosModelo();
            $usuario = $m->obtenerUsuarioPorId($idUser);

            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            // Eliminar datos asociados al usuario
            if ($m->eliminarGastosPorUsuario($idUser) && $m->eliminarIngresosPorUsuario($idUser) && $m->eliminarUsuario($idUser)) {
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



    // Listar usuarios con restricción para administradores 
    public function listarUsuarios()
    {
        try {
            $m = new GastosModelo();

            // Verificamos el nivel de usuario para aplicar la restricción
            if ($_SESSION['usuario']['nivel_usuario'] === 'admin') {
                // Obtener solo los usuarios gestionados por el administrador
                $idAdmin = $_SESSION['usuario']['id'];
                $usuarios = $m->obtenerUsuariosGestionadosPorAdmin($idAdmin);
                $mensaje = 'Lista de usuarios gestionados por el administrador';
            } else {
                // Si es Superadmin, obtiene todos los usuarios
                $usuarios = $m->obtenerUsuarios();
                $mensaje = 'Lista de usuarios registrados';
            }

            // Parámetros para pasar a la vista
            $params = [
                'usuarios' => $usuarios,
                'mensaje' => $mensaje
            ];

            // Renderizar la vista listarUsuarios.php con los parámetros establecidos
            $this->render('listarUsuarios.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarUsuarios(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los usuarios.');
        }
    }



    // Renderizar vistas con corrección para incluir .php automáticamente
    private function render($vista, $params = array())
    {
        try {
            extract($params);
            ob_start();
            // Agregar la extensión .php automáticamente
            require __DIR__ . '/../../web/templates/' . $vista;
            $contenido = ob_get_clean();
            require __DIR__ . '/../../web/templates/layout.php';
        } catch (Exception $e) {
            error_log("Error en render(): " . $e->getMessage());
            echo 'Error al renderizar la vista: ' . $e->getMessage(); // Mensaje temporal de depuración
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

    // Validar si el usuario es administrador o superadmin
    private function esAdmin()
    {
        if ($_SESSION['usuario']['nivel_usuario'] === 'superadmin') {
            error_log("Permisos verificados: Usuario es SuperAdmin");
            return true; // SuperAdmin tiene permisos para todo
        }
        $isAdmin = $_SESSION['usuario']['nivel_usuario'] === 'admin';
        error_log("Permisos verificados: Usuario es " . ($isAdmin ? "Admin" : "No es Admin ni SuperAdmin"));
        return $isAdmin;
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
    public function crearFamiliaGrupoAdicionales()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password_premium = recoge('password_premium');
                $idUser = $_SESSION['usuario']['id'];

                // Verificar la contraseña premium
                if (!$this->verificarPasswordPremium($idUser, $password_premium)) {
                    throw new Exception('Contraseña premium incorrecta. No tienes permisos para crear familias o grupos.');
                }

                // Crear familias adicionales
                for ($i = 1; $i <= 4; $i++) {
                    $nombre_familia = recoge("nombre_nueva_familia_$i");
                    $password_familia = recoge("password_nueva_familia_$i");

                    if (!empty($nombre_familia) && !empty($password_familia)) {
                        if (!$this->modelo->insertarFamilia($nombre_familia, $password_familia)) {
                            throw new Exception("No se pudo crear la nueva familia $i.");
                        }
                        $idFamilia = $this->modelo->obtenerUltimoId();
                        $this->modelo->asignarUsuarioAFamilia($idUser, $idFamilia);
                        $this->modelo->asignarAdministradorAFamilia($idUser, $idFamilia);
                    }
                }

                // Crear grupos adicionales
                for ($i = 1; $i <= 9; $i++) {
                    $nombre_grupo = recoge("nombre_nuevo_grupo_$i");
                    $password_grupo = recoge("password_nuevo_grupo_$i");

                    if (!empty($nombre_grupo) && !empty($password_grupo)) {
                        if (!$this->modelo->insertarGrupo($nombre_grupo, $password_grupo)) {
                            throw new Exception("No se pudo crear el nuevo grupo $i.");
                        }
                        $idGrupo = $this->modelo->obtenerUltimoId();
                        $this->modelo->asignarUsuarioAGrupo($idUser, $idGrupo);
                        $this->modelo->asignarAdministradorAGrupo($idUser, $idGrupo);
                    }
                }

                $_SESSION['mensaje_exito'] = 'Familias y grupos creados con éxito';
                header('Location: index.php?ctl=inicio');
                exit();
            }

            // Renderizar el formulario
            $this->render('formCrearFamiliaGrupoAdicionales.php', []);
        } catch (Exception $e) {
            error_log("Error en crearFamiliaGrupoAdicionales(): " . $e->getMessage());
            $params['mensaje'] = 'Error al crear familias o grupos adicionales. ' . $e->getMessage();
            $this->render('formCrearFamiliaGrupoAdicionales.php', $params);
        }
    }
}
