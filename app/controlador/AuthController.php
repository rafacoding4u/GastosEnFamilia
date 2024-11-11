<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class AuthController
{
    const NIVEL_SUPERADMIN = 'superadmin';
    const NIVEL_ADMIN = 'admin';
    const NIVEL_USUARIO = 'usuario';

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

    // Método de redirección a una página de error (útil si aún no está definido)
    private function redireccionarError($mensaje)
    {
        if ($_GET['ctl'] !== 'error') {
            $_SESSION['error_mensaje'] = $mensaje;
            header('Location: index.php?ctl=error');
            exit();
        }
    }



    public function home()
    {
        try {
            if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_usuario'] > 0) {
                header("Location: index.php?ctl=inicio");
                exit();
            }

            $params = array(
                'mensaje' => 'Bienvenido a LasCuentasClaras',
                'mensaje2' => 'Gestiona tus finanzas de manera eficiente',
                'fecha' => date('d-m-Y')
            );

            $this->render('home.php', $params);
        } catch (Exception $e) {
            error_log("Error en home(): " . $e->getMessage());
            header('Location: index.php?ctl=error');
        }
    }

    public function iniciarSesion()
    {
        $params = array('alias' => '', 'contrasenya' => '');

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
                    $this->registrarAcceso(null, 'acceso_denegado');
                } else {
                    if (comprobarhash($contrasenya, $usuario['contrasenya'])) {
                        session_regenerate_id(true);

                        $_SESSION['usuario'] = array(
                            'id' => $usuario['idUser'],
                            'nombre' => $usuario['nombre'],
                            'nivel_usuario' => $usuario['nivel_usuario'],
                            'email' => $usuario['email']
                        );

                        header('Location: index.php?ctl=inicio');
                        exit();
                    } else {
                        $params['mensaje'] = 'Usuario o contraseña incorrectos.';
                        $this->registrarAcceso(null, 'acceso_denegado');
                    }
                }
            } catch (Exception $e) {
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
                session_unset();
                session_destroy();
            }
        } catch (Exception $e) {
            error_log("Error al cerrar la sesión: " . $e->getMessage());
        }

        header("Location: index.php?ctl=iniciarSesion");
        exit();
    }
    private function registrarAcceso($idUser, $accion)
    {
        $modelo = new GastosModelo();
        if ($idUser === null) {
            $idUser = 'NULL';
        }
        $modelo->registrarAcceso($idUser, $accion);
    }
    public function registro()
    {
        $limiteFamilias = 5;
        $limiteGrupos = 10;

        try {
            error_log("Iniciando proceso de registro de usuario...");

            $m = new GastosModelo();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // Recoger datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = !empty(recoge('telefono')) ? recoge('telefono') : null;
                $fecha_nacimiento = !empty(recoge('fecha_nacimiento')) ? recoge('fecha_nacimiento') : null;
                $contrasenya = recoge('contrasenya'); // Contraseña del usuario
                $hashedPassword = password_hash($contrasenya, PASSWORD_BCRYPT); // Encriptación de la contraseña
                $nivel_usuario = 'usuario'; // Rol por defecto

                // Generar y encriptar la contraseña premium
                $passwordPremium = bin2hex(random_bytes(4));
                $hashedPasswordPremium = password_hash($passwordPremium, PASSWORD_BCRYPT);

                // Insertar usuario en la base de datos
                $idUser = $m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono);
                if (!$idUser) {
                    throw new Exception('Error al registrar el usuario.');
                }
                error_log("Usuario creado con ID $idUser");

                // Actualizar la contraseña premium en la base de datos
                $m->actualizarPasswordPremium($idUser, $hashedPasswordPremium);
                error_log("Contraseña premium generada para el usuario $alias: $passwordPremium");

                // Opción de creación del formulario
                $opcion_creacion = recoge('opcion_creacion');
                $maxFamiliasNoPremium = 1;
                $maxGruposNoPremium = 1;
                $totalFamilias = 0;
                $totalGrupos = 0;

                // Contar las familias y grupos que se están intentando crear
                for ($i = 1; $i <= $limiteFamilias; $i++) {
                    $nombre_familia = recoge("nombre_nueva_familia_$i");
                    $password_familia = recoge("password_nueva_familia_$i");
                    if (!empty($nombre_familia) && !empty($password_familia)) {
                        $totalFamilias++;
                    }
                }

                for ($i = 1; $i <= $limiteGrupos; $i++) {
                    $nombre_grupo = recoge("nombre_nuevo_grupo_$i");
                    $password_grupo = recoge("password_nuevo_grupo_$i");
                    if (!empty($nombre_grupo) && !empty($password_grupo)) {
                        $totalGrupos++;
                    }
                }

                // Verificación de límites para usuarios no premium
                if ($totalFamilias > $maxFamiliasNoPremium || $totalGrupos > $maxGruposNoPremium) {
                    throw new Exception('No tienes permisos para crear más de una familia o grupo. Debes ser usuario premium.');
                }

                // Creación de familias y asignación del usuario como administrador
                if ($opcion_creacion === 'crear_familia' || $opcion_creacion === 'crear_ambos') {
                    for ($i = 1; $i <= $totalFamilias; $i++) {
                        $nombre_familia = recoge("nombre_nueva_familia_$i");
                        $password_familia = recoge("password_nueva_familia_$i");

                        if (!empty($nombre_familia) && !empty($password_familia)) {
                            if (!$m->insertarFamilia($nombre_familia, $password_familia)) {
                                throw new Exception("No se pudo crear la nueva familia $i.");
                            }
                            $idFamilia = $m->obtenerUltimoId();
                            if ($idFamilia) {
                                $m->asignarUsuarioAFamilia($idUser, $idFamilia);
                                $m->asignarAdministradorAFamilia($idUser, $idFamilia);
                                $nivel_usuario = 'admin';
                                error_log("Usuario $idUser asignado como administrador a la familia $idFamilia");
                            }
                        }
                    }
                }

                // Creación de grupos y asignación del usuario como administrador
                if ($opcion_creacion === 'crear_grupo' || $opcion_creacion === 'crear_ambos') {
                    for ($i = 1; $i <= $totalGrupos; $i++) {
                        $nombre_grupo = recoge("nombre_nuevo_grupo_$i");
                        $password_grupo = recoge("password_nuevo_grupo_$i");

                        if (!empty($nombre_grupo) && !empty($password_grupo)) {
                            if (!$m->insertarGrupo($nombre_grupo, $password_grupo)) {
                                throw new Exception("No se pudo crear el nuevo grupo $i.");
                            }
                            $idGrupo = $m->obtenerUltimoId();
                            if ($idGrupo) {
                                $m->asignarUsuarioAGrupo($idUser, $idGrupo);
                                $m->asignarAdministradorAGrupo($idUser, $idGrupo);
                                $nivel_usuario = 'admin';
                                error_log("Usuario $idUser asignado como administrador al grupo $idGrupo");
                            }
                        }
                    }
                }

                // Actualizar el nivel de usuario
                $m->actualizarUsuarioNivel($idUser, $nivel_usuario);

                // Mensaje de éxito y redirección
                $_SESSION['mensaje_exito'] = "Usuario registrado con éxito. 
            <br>Contraseña premium generada: <strong>$passwordPremium</strong>";
                header('Location: index.php?ctl=iniciarSesion');
                exit();
            }

            // Renderizar el formulario de registro si no es POST
            $this->render('formRegistro.php', []);
        } catch (Exception $e) {
            error_log("Error en registro(): " . $e->getMessage());
            $params['mensaje'] = 'Error al registrarse. ' . $e->getMessage();
            $this->render('formRegistro.php', $params);
        }
    }
    public function inicio()
    {
        try {
            // Verificar que el usuario esté autenticado y tenga nivel de usuario adecuado
            if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['nivel_usuario']) || $_SESSION['usuario']['nivel_usuario'] == 'registro') {
                header('Location: index.php?ctl=iniciarSesion');
                exit();
            }

            $m = new GastosModelo();
            $idUser = $_SESSION['usuario']['id'] ?? null; // Uso seguro de id

            $params = [
                'mensaje' => isset($_SESSION['usuario']['nombre']) ? 'Bienvenido, ' . $_SESSION['usuario']['nombre'] : 'Bienvenido',
                'nivel_usuario' => $_SESSION['usuario']['nivel_usuario'],
                'fecha' => date('d-m-Y')
            ];

            if ($_SESSION['usuario']['nivel_usuario'] === self::NIVEL_SUPERADMIN) {
                $params['finanzasGlobales'] = $m->obtenerSituacionGlobal();
            } elseif ($_SESSION['usuario']['nivel_usuario'] === self::NIVEL_ADMIN) {
                $params['finanzasFamilias'] = $m->obtenerFamiliasPorAdministrador($idUser);
                $params['finanzasGrupos'] = $m->obtenerGruposPorAdministrador($idUser);
            } else {
                $params['finanzasPersonales'] = $m->obtenerSituacionFinanciera($idUser);
            }

            $this->render('inicio.php', $params);
        } catch (Exception $e) {
            error_log("Error en inicio(): " . $e->getMessage());
            header('Location: index.php?ctl=error');
            exit();
        }
    }
    public function mostrarError()
    {
        $mensaje = $_SESSION['error_mensaje'] ?? 'Ha ocurrido un error inesperado.';
        unset($_SESSION['error_mensaje']);  // Limpiar el mensaje después de mostrarlo
        error_log("Mostrando página de error: " . $mensaje);  // Log para seguimiento
        $params = array('mensaje' => $mensaje);
        $this->render('error.php', $params);
    }
}
