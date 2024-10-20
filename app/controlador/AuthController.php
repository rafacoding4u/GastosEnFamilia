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
            if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] > 0) {
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
                            'idFamilia' => $usuario['idFamilia'] ?? null,
                            'idGrupo' => $usuario['idGrupo'] ?? null
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
    try {
        error_log("Iniciando proceso de registro de usuario...");

        $m = new GastosModelo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recoger datos del formulario
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $alias = recoge('alias');
            $email = recoge('email');
            $telefono = recoge('telefono');
            $fecha_nacimiento = recoge('fecha_nacimiento');
            $contrasenya = recoge('contrasenya');
            $rol_vinculo = recoge('rol_vinculo'); // Selección de usuario o admin
            $nombre_nueva_familia = recoge('nombre_nueva_familia');
            $password_nueva_familia = recoge('password_nueva_familia');
            $nombre_nuevo_grupo = recoge('nombre_nuevo_grupo');
            $password_nuevo_grupo = recoge('password_nuevo_grupo');
            $passwordFamiliaExistente = recoge('passwordFamiliaExistente');
            $passwordGrupoExistente = recoge('passwordGrupoExistente');

            // Validaciones y comprobaciones...
            // Aquí repites las validaciones de datos como lo haces en crearUsuario()
            // ...

            // Encriptar la contraseña
            $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT);
            $nivel_usuario = 'registro'; // Temporalmente asignamos el rol de "registro"

            // Insertar el nuevo usuario en la base de datos
            $idUser = $m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);

            if (!$idUser) {
                throw new Exception('Error al registrar el usuario.');
            }
            error_log("Usuario creado con ID $idUser");

            // Verificar y asignar nueva familia
            if (!empty($nombre_nueva_familia) && !empty($password_nueva_familia)) {
                if (!$m->insertarFamilia($nombre_nueva_familia, $password_nueva_familia)) {
                    throw new Exception('No se pudo crear la nueva familia.');
                }
                $idFamilia = $m->obtenerUltimoId();
                $m->asignarUsuarioAFamilia($idUser, $idFamilia);

                if ($rol_vinculo === 'admin') {
                    $m->asignarAdministradorAFamilia($idUser, $idFamilia);
                    error_log("Usuario $idUser asignado como administrador a la familia $idFamilia");
                }
            }

            // Verificar y asignar nuevo grupo
            if (!empty($nombre_nuevo_grupo) && !empty($password_nuevo_grupo)) {
                if (!$m->insertarGrupo($nombre_nuevo_grupo, $password_nuevo_grupo)) {
                    throw new Exception('No se pudo crear el nuevo grupo.');
                }
                $idGrupo = $m->obtenerUltimoId();
                $m->asignarUsuarioAGrupo($idUser, $idGrupo);

                if ($rol_vinculo === 'admin') {
                    $m->asignarAdministradorAGrupo($idUser, $idGrupo);
                    error_log("Usuario $idUser asignado como administrador al grupo $idGrupo");
                }
            }

            // Asignar familias y grupos existentes, similar a crearUsuario()
            // ...

            // Actualizar el rol del usuario dependiendo del tipo de vínculo
            if ($rol_vinculo === 'admin') {
                $m->actualizarUsuarioNivel($idUser, 'admin');
            } else {
                $m->actualizarUsuarioNivel($idUser, 'usuario');
            }

            // Mensaje de éxito y redirección
            $_SESSION['mensaje_exito'] = 'Usuario registrado con éxito';
            header('Location: index.php?ctl=iniciarSesion');
            exit();
        }

        // Renderizar el formulario si no es POST
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
        $params = array(
            'familias' => $familias,
            'grupos' => $grupos,
        );
        $this->render('formRegistro.php', $params);

    } catch (Exception $e) {
        error_log("Error en registro(): " . $e->getMessage());
        $params['mensaje'] = 'Error al registrarse. ' . $e->getMessage();
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
