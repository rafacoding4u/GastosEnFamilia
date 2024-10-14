<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php'; // Asegúrate de que la clase del modelo esté bien referenciada

class AuthController
{
    // Página de inicio (landing page para usuarios no autenticados)
    public function home()
    {
        try {
            // Si el usuario ya está autenticado, redirigir a la página principal
            if (isset($_SESSION['usuario']) && $_SESSION['nivel_usuario'] > 0) {
                header("Location: index.php?ctl=inicio");
                exit();
            }

            // Si no está autenticado, mostrar la página de bienvenida
            $params = array(
                'mensaje' => 'Bienvenido a GastosEnFamilia',
                'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
                'fecha' => date('d-m-Y')
            );

            $this->render('home.php', $params);
        } catch (Exception $e) {
            error_log("Error en home(): " . $e->getMessage());
            header('Location: index.php?ctl=error');
        }
    }

    public function inicio()
    {
        try {
            // Asegurarse de que el usuario está autenticado
            if (!isset($_SESSION['usuario']) || $_SESSION['nivel_usuario'] == 0) {
                header('Location: index.php?ctl=iniciarSesion');
                exit();
            }

            $m = new GastosModelo(); // Cargar el modelo para acceder a los datos del usuario
            $idUsuario = $_SESSION['usuario']['id'];

            // Obtener el total de ingresos, gastos y saldo del usuario
            $totalIngresos = $m->obtenerTotalIngresos($idUsuario);
            $totalGastos = $m->obtenerTotalGastos($idUsuario);
            $saldo = $totalIngresos - $totalGastos;

            // Preparar los parámetros para la vista
            $params = [
                'mensaje' => 'Bienvenido, ' . $_SESSION['usuario']['nombre'],
                'totalIngresos' => $totalIngresos,
                'totalGastos' => $totalGastos,
                'saldo' => $saldo,
                'nivel_usuario' => $_SESSION['nivel_usuario'],
                'fecha' => date('d-m-Y')
            ];

            // Renderizar la vista 'inicio.php'
            $this->render('inicio.php', $params);
        } catch (Exception $e) {
            error_log("Error en inicio(): " . $e->getMessage());
            header('Location: index.php?ctl=home');
            exit();
        }
    }


    // Iniciar sesión
    public function iniciarSesion()
    {
        $params = array(
            'alias' => '',
            'contrasenya' => ''
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {
            try {
                // Verificar el token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                // Limpiar los datos del formulario
                $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
                $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');

                // Consultar al usuario en la base de datos
                $m = new GastosModelo();
                $usuario = $m->consultarUsuario($alias);

                // Verificar que el usuario existe
                if (!$usuario) {
                    $params['mensaje'] = 'Alias incorrecto.';
                    error_log("Intento fallido de inicio de sesión para el alias {$alias}: usuario no encontrado.");
                    // Registrar acceso denegado
                    $this->registrarAcceso(null, 'acceso_denegado');
                } else {
                    // Comprobación específica para superadmin
                    if ($usuario['nivel_usuario'] == 'superadmin') {
                        error_log("Iniciando sesión con superadmin: {$alias}");
                    }

                    // Verificar la contraseña usando la función unificada
                    if (comprobarhash($contrasenya, $usuario['contrasenya'])) {
                        session_regenerate_id(true); // Proteger contra ataque de fijación de sesión

                        // Establecer los datos del usuario en la sesión
                        $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
                        $_SESSION['usuario'] = array(
                            'id' => $usuario['idUser'],
                            'nombre' => $usuario['nombre'],
                            'nivel_usuario' => $usuario['nivel_usuario'],
                            'email' => $usuario['email'],
                            'idFamilia' => $usuario['idFamilia'],
                            'idGrupo' => $usuario['idGrupo']
                        );

                        error_log("Usuario con alias {$alias} ha iniciado sesión correctamente.");

                        // Registrar inicio de sesión exitoso
                        $this->registrarAcceso($usuario['idUser'], 'login');

                        // Redirigir al inicio
                        header('Location: index.php?ctl=inicio');
                        exit();
                    } else {
                        $params['mensaje'] = 'Usuario o contraseña incorrectos.';
                        error_log("Intento fallido de inicio de sesión para el alias {$alias}: contraseña incorrecta.");
                        // Registrar acceso denegado
                        $this->registrarAcceso(null, 'acceso_denegado');
                    }
                }
            } catch (Exception $e) {
                error_log("Error en iniciarSesion(): " . $e->getMessage());
                $params['mensaje'] = 'Error al iniciar sesión. Inténtelo de nuevo.';
            }
        }

        // Generar token CSRF
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $params['csrf_token'] = $_SESSION['csrf_token'];

        // Renderizar el formulario de inicio de sesión
        $this->render('formIniciarSesion.php', $params);
    }

    // Cerrar sesión
    public function salir()
{
    try {
        // Asegurar que la sesión existe antes de cerrarla
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['usuario'])) {
            // Registrar el cierre de sesión
            $this->registrarAcceso($_SESSION['usuario']['id'], 'logout');

            // No es necesario llamar a session_start() si la sesión ya está activa
            session_unset(); // Eliminar todas las variables de sesión
            session_destroy(); // Destruir la sesión
            error_log("Sesión cerrada exitosamente.");
        }
    } catch (Exception $e) {
        error_log("Error al cerrar la sesión: " . $e->getMessage());
    }

    // Redirigir al home después de cerrar sesión
    header("Location: index.php?ctl=home");
    exit();
}

    // Método de registro de nuevos usuarios
    public function registro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $m = new GastosModelo(); // Inicializamos el modelo

                // Verificar el token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                // Recoger y validar los datos del formulario
                $nombre = htmlspecialchars(recoge('nombre'), ENT_QUOTES, 'UTF-8');
                $apellido = htmlspecialchars(recoge('apellido'), ENT_QUOTES, 'UTF-8');
                $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
                $email = filter_var(recoge('email'), FILTER_VALIDATE_EMAIL);
                $telefono = htmlspecialchars(recoge('telefono'), ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
                $fechaNacimiento = recoge('fecha_nacimiento');
                $tipoVinculo = recoge('tipo_vinculo'); // Pertenecer a grupo, familia, individual, o crear nueva familia/grupo

                // Validar que todos los campos requeridos están completos
                if (!$email || empty($password) || empty($nombre) || empty($alias)) {
                    throw new Exception('Todos los campos son obligatorios.');
                }

                // Verificar si el alias ya existe
                if ($m->existeUsuario($alias)) {
                    throw new Exception('El alias ya está en uso.');
                }

                // Encriptar la contraseña
                $passwordEncriptada = password_hash($password, PASSWORD_BCRYPT);

                // Variable para el nivel de usuario
                $nivel_usuario = 'usuario'; // Por defecto es usuario normal

                // Inicializar variables de familia o grupo como NULL por defecto
                $idFamilia = null;
                $idGrupo = null;

                // Insertar el nuevo usuario en la base de datos (se inserta antes de crear familia o grupo)
                $usuarioRegistrado = $m->insertarUsuario($nombre, $apellido, $alias, $passwordEncriptada, $nivel_usuario, $fechaNacimiento, $email, $telefono);

                if (!$usuarioRegistrado) {
                    throw new Exception('Error al registrar el usuario.');
                }

                // Obtener el ID del usuario recién creado para usarlo como administrador si es necesario
                $idUsuario = $m->obtenerIdUsuarioPorAlias($alias);

                // Lógica para familia/grupo nuevo o existente
                if ($tipoVinculo === 'crear_familia') {
                    // Crear nueva familia
                    $nombreFamilia = recoge('nombre_nuevo');
                    $passwordFamilia = password_hash(recoge('password_nuevo'), PASSWORD_BCRYPT);
                    $m->insertarFamilia($nombreFamilia, $passwordFamilia);
                    $idFamilia = $m->obtenerIdFamiliaPorNombre($nombreFamilia);

                    // Verifica que el ID de la familia no sea nulo
                    if (!$idFamilia) {
                        throw new Exception('Error al crear la familia.');
                    }

                    // Asignar al usuario como administrador
                    $nivel_usuario = 'admin';

                    // Actualizar la tabla familias con el ID del administrador
                    $m->actualizarFamilia($idFamilia, $nombreFamilia, $idUsuario);

                    // Insertar el administrador en la tabla administradores_familias
                    $m->añadirAdministradorAFamilia($idUsuario, $idFamilia);

                    // Asignar al usuario a la familia en la tabla usuarios_familias
                    $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                } elseif ($tipoVinculo === 'crear_grupo') {
                    // Crear nuevo grupo
                    $nombreGrupo = recoge('nombre_nuevo');
                    $passwordGrupo = password_hash(recoge('password_nuevo'), PASSWORD_BCRYPT);

                    // Insertar el grupo en la base de datos
                    $m->insertarGrupo($nombreGrupo, $passwordGrupo);

                    // Obtener el ID del grupo recién creado
                    $idGrupo = $m->obtenerIdGrupoPorNombre($nombreGrupo);

                    // Verificar si el grupo fue creado correctamente
                    if (!$idGrupo) {
                        throw new Exception('Error al crear el grupo.');
                    }

                    // Asignar al usuario como administrador del grupo
                    $nivel_usuario = 'admin';

                    // Actualizar el grupo con el ID del administrador
                    $m->actualizarGrupo($idGrupo, $nombreGrupo, $idUsuario);

                    // Insertar el administrador en la tabla administradores_grupos
                    $m->añadirAdministradorAGrupo($idUsuario, $idGrupo);

                    // Asignar al usuario al grupo en la tabla usuarios_grupos
                    $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                } elseif ($tipoVinculo === 'familia' || $tipoVinculo === 'grupo') {
                    // Pertenecer a una familia o grupo existente
                    $idGrupoFamilia = recoge('idGrupoFamilia');
                    $passwordGrupoFamilia = recoge('passwordGrupoFamilia');
                
                    // Validar la contraseña del grupo o familia
                    if (strpos($idGrupoFamilia, 'familia_') === 0) {
                        $idFamilia = str_replace('familia_', '', $idGrupoFamilia);
                        if (!$m->verificarPasswordFamilia($idFamilia, $passwordGrupoFamilia)) {
                            throw new Exception('Contraseña de la familia incorrecta.');
                        } else {
                            // Asignar el usuario a la familia en la tabla usuarios_familias
                            $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                        }
                    } elseif (strpos($idGrupoFamilia, 'grupo_') === 0) {
                        $idGrupo = str_replace('grupo_', '', $idGrupoFamilia);
                        if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoFamilia)) {
                            throw new Exception('Contraseña del grupo incorrecta.');
                        } else {
                            // Asignar el usuario al grupo en la tabla usuarios_grupos
                            $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                        }
                    }
                }
                                

                // Actualizar el nivel del usuario después de los cambios
                $m->actualizarUsuarioNivel($idUsuario, $nivel_usuario);

                // Registro exitoso
                $params['mensaje'] = 'Usuario registrado con éxito.';
                header('Location: index.php?ctl=iniciarSesion');
                exit();
            } catch (Exception $e) {
                error_log("Error en registro(): " . $e->getMessage());
                $params['mensaje'] = 'Error al registrarse. ' . $e->getMessage();

                // Inicializar el modelo aquí también si ocurre un error
                if (!isset($m)) {
                    $m = new GastosModelo();
                }

                // Volver a cargar los grupos y familias en caso de error
                $params['familias'] = $m->obtenerFamilias();
                $params['grupos'] = $m->obtenerGrupos();
                $this->render('formRegistro.php', $params);
            }
        } else {
            // Generar token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Obtener grupos y familias del modelo y pasarlos al formulario
            $m = new GastosModelo();
            $params['csrf_token'] = $_SESSION['csrf_token'];
            $params['familias'] = $m->obtenerFamilias();
            $params['grupos'] = $m->obtenerGrupos();

            $this->render('formRegistro.php', $params);
        }
    }

    // Método para registrar acceso en la tabla de auditoría
    private function registrarAcceso($idUser, $accion)
    {
        $m = new GastosModelo();

        // Si no hay usuario (por ejemplo, en caso de acceso denegado)
        if ($idUser === null) {
            $idUser = 'NULL';
        }

        $m->registrarAcceso($idUser, $accion);
    }

    // Método de manejo de errores
    public function error()
    {
        try {
            $params = array(
                'mensaje' => 'Ha ocurrido un error. Por favor, intenta de nuevo más tarde.'
            );
            $this->render('error.php', $params);
        } catch (Exception $e) {
            error_log("Error en el manejo de errores: " . $e->getMessage());
            echo 'Ocurrió un problema grave. Intente más tarde.';
        }
    }

    // Método para renderizar las vistas
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
            header('Location: index.php?ctl=error');
        }
    }
}
