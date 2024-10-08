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

                session_start();
                session_regenerate_id(true); // Generar un nuevo ID de sesión
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

    // Método de inicio para usuarios autenticados
    public function inicio()
    {
        try {
            // Asegurarse de que el usuario está autenticado
            if (!isset($_SESSION['usuario']) || $_SESSION['nivel_usuario'] == 0) {
                throw new Exception('Usuario no autenticado.');
            }

            // Mensaje de bienvenida personalizado según el usuario autenticado
            $params = array(
                'mensaje' => 'Bienvenido, ' . $_SESSION['usuario']['nombre'],
                'nivel_usuario' => $_SESSION['nivel_usuario'],
                'fecha' => date('d-m-Y')
            );

            // Renderizar la vista de inicio
            $this->render('inicio.php', $params);
        } catch (Exception $e) {
            error_log("Error en inicio(): " . $e->getMessage());
            header('Location: index.php?ctl=home');
            exit();
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
