<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class AuthController
{
    // Definir constantes para niveles de usuario
    const NIVEL_SUPERADMIN = 'superadmin';
    const NIVEL_ADMIN = 'admin';
    const NIVEL_USUARIO = 'usuario';

    public function home()
    {
        try {
            if (isset($_SESSION['usuario']) && $_SESSION['nivel_usuario'] > 0) {
                header("Location: index.php?ctl=inicio");
                exit();
            }

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
            if (!isset($_SESSION['usuario']) || $_SESSION['nivel_usuario'] == 0) {
                header('Location: index.php?ctl=iniciarSesion');
                exit();
            }

            $m = new GastosModelo();
            $idUsuario = $_SESSION['usuario']['id'];

            $params = [
                'mensaje' => 'Bienvenido, ' . $_SESSION['usuario']['nombre'],
                'nivel_usuario' => $_SESSION['nivel_usuario'],
                'fecha' => date('d-m-Y')
            ];

            if ($_SESSION['nivel_usuario'] === self::NIVEL_SUPERADMIN) {
                // Superadmin ve todo el resumen financiero global
                $params['finanzasGlobales'] = $m->obtenerSituacionGlobal();
            } elseif ($_SESSION['nivel_usuario'] === self::NIVEL_ADMIN) {
                // Admin ve el resumen financiero de sus familias y grupos
                $params['finanzasFamilias'] = $m->obtenerFamiliasPorAdministrador($idUsuario);
                $params['finanzasGrupos'] = $m->obtenerGruposPorAdministrador($idUsuario);
            } else {
                // Usuario regular solo ve su resumen personal
                $params['finanzasPersonales'] = $m->obtenerSituacionFinanciera($idUsuario);
            }

            $this->render('inicio.php', $params);
        } catch (Exception $e) {
            error_log("Error en inicio(): " . $e->getMessage());
            header('Location: index.php?ctl=home');
            exit();
        }
    }

    public function iniciarSesion()
    {
        $params = array(
            'alias' => '',
            'contrasenya' => ''
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {
            try {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
                $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');

                $m = new GastosModelo();
                $usuario = $m->consultarUsuario($alias);

                if (!$usuario) {
                    $params['mensaje'] = 'Alias incorrecto.';
                    error_log("Intento fallido de inicio de sesión para el alias {$alias}: usuario no encontrado.");
                    $this->registrarAcceso(null, 'acceso_denegado');
                } else {
                    if (comprobarhash($contrasenya, $usuario['contrasenya'])) {
                        session_regenerate_id(true);

                        $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
                        $_SESSION['usuario'] = array(
                            'id' => $usuario['idUser'],
                            'nombre' => $usuario['nombre'],
                            'nivel_usuario' => $usuario['nivel_usuario'],
                            'email' => $usuario['email'],
                            'idFamilia' => $usuario['idFamilia'],
                            'idGrupo' => $usuario['idGrupo']
                        );

                        $this->registrarAcceso($usuario['idUser'], 'login');

                        header('Location: index.php?ctl=inicio');
                        exit();
                    } else {
                        $params['mensaje'] = 'Usuario o contraseña incorrectos.';
                        error_log("Intento fallido de inicio de sesión para el alias {$alias}: contraseña incorrecta.");
                        $this->registrarAcceso(null, 'acceso_denegado');
                    }
                }
            } catch (Exception $e) {
                error_log("Error en iniciarSesion(): " . $e->getMessage());
                $params['mensaje'] = 'Error al iniciar sesión. Inténtelo de nuevo.';
            }
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $params['csrf_token'] = $_SESSION['csrf_token'];

        $this->render('formIniciarSesion.php', $params);
    }

    public function salir()
    {
        try {
            if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['usuario'])) {
                $this->registrarAcceso($_SESSION['usuario']['id'], 'logout');
                session_unset();
                session_destroy();
                error_log("Sesión cerrada exitosamente.");
            }
        } catch (Exception $e) {
            error_log("Error al cerrar la sesión: " . $e->getMessage());
        }

        header("Location: index.php?ctl=home");
        exit();
    }

    public function registro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $m = new GastosModelo();

                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('CSRF token inválido.');
                }

                $nombre = htmlspecialchars(recoge('nombre'), ENT_QUOTES, 'UTF-8');
                $apellido = htmlspecialchars(recoge('apellido'), ENT_QUOTES, 'UTF-8');
                $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
                $email = filter_var(recoge('email'), FILTER_VALIDATE_EMAIL);
                $telefono = htmlspecialchars(recoge('telefono'), ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
                $fechaNacimiento = recoge('fecha_nacimiento');
                $tipoVinculo = recoge('tipo_vinculo');

                if (!$email || empty($password) || empty($nombre) || empty($alias)) {
                    throw new Exception('Todos los campos son obligatorios.');
                }

                if ($m->existeUsuario($alias)) {
                    throw new Exception('El alias ya está en uso.');
                }

                $passwordEncriptada = password_hash($password, PASSWORD_DEFAULT);

                $nivel_usuario = self::NIVEL_USUARIO;
                $idFamilia = null;
                $idGrupo = null;

                if ($_SESSION['nivel_usuario'] !== self::NIVEL_SUPERADMIN && ($tipoVinculo === 'crear_familia' || $tipoVinculo === 'crear_grupo')) {
                    throw new Exception('Solo los Superadmins pueden crear nuevas familias o grupos.');
                }

                $usuarioRegistrado = $m->insertarUsuario($nombre, $apellido, $alias, $passwordEncriptada, $nivel_usuario, $fechaNacimiento, $email, $telefono);

                if (!$usuarioRegistrado) {
                    throw new Exception('Error al registrar el usuario.');
                }

                $idUsuario = $m->obtenerIdUsuarioPorAlias($alias);

                if ($tipoVinculo === 'crear_familia') {
                    $nombreFamilia = recoge('nombre_nuevo');
                    $passwordFamilia = password_hash(recoge('password_nuevo'), PASSWORD_DEFAULT);
                    $m->insertarFamilia($nombreFamilia, $passwordFamilia);
                    $idFamilia = $m->obtenerIdFamiliaPorNombre($nombreFamilia);

                    if (!$idFamilia) {
                        throw new Exception('Error al crear la familia.');
                    }

                    $nivel_usuario = self::NIVEL_ADMIN;
                    $m->actualizarFamilia($idFamilia, $nombreFamilia, $idUsuario);
                    $m->añadirAdministradorAFamilia($idUsuario, $idFamilia);
                    $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                } elseif ($tipoVinculo === 'crear_grupo') {
                    $nombreGrupo = recoge('nombre_nuevo');
                    $passwordGrupo = password_hash(recoge('password_nuevo'), PASSWORD_DEFAULT);
                    $m->insertarGrupo($nombreGrupo, $passwordGrupo);
                    $idGrupo = $m->obtenerIdGrupoPorNombre($nombreGrupo);

                    if (!$idGrupo) {
                        throw new Exception('Error al crear el grupo.');
                    }

                    $nivel_usuario = self::NIVEL_ADMIN;
                    $m->actualizarGrupo($idGrupo, $nombreGrupo, $idUsuario);
                    $m->añadirAdministradorAGrupo($idUsuario, $idGrupo);
                    $m->asignarUsuarioAGrupo($idUsuario, $idGrupo);
                } elseif ($tipoVinculo === 'familia' || $tipoVinculo === 'grupo') {
                    // Pertenecer a una familia o grupo existente
                    $idGrupoFamilia = recoge('idGrupoFamilia');
                    $passwordGrupoFamilia = recoge('passwordGrupoFamilia');

                    if (strpos($idGrupoFamilia, 'familia_') === 0) {
                        $idFamilia = str_replace('familia_', '', $idGrupoFamilia);
                        if (!$m->verificarPasswordFamilia($idFamilia, $passwordGrupoFamilia)) {
                            throw new Exception('Contraseña de la familia incorrecta.');
                        } else {
                            $m->asignarUsuarioAFamilia($idUsuario, $idFamilia);
                        }
                    } elseif (strpos($idGrupoFamilia, 'grupo_') === 0) {
                        $idGrupo = str_replace('grupo_', '', $idGrupoFamilia);
                        if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoFamilia)) {
                            throw new Exception('Contraseña del grupo incorrecta.');
                        } else {
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

                // Volver a cargar los grupos y familias en caso de error
                if (!isset($m)) {
                    $m = new GastosModelo();
                }

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

    private function registrarAcceso($idUser, $accion)
    {
        $m = new GastosModelo();
        if ($idUser === null) {
            $idUser = 'NULL';
        }

        $m->registrarAcceso($idUser, $accion);
    }

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
