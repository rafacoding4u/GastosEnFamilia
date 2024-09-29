<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class AuthController
{
    // Página de inicio (landing page para usuarios no autenticados)
    public function home()
    {
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
    }

    // Iniciar sesión
    public function iniciarSesion()
    {
        $params = array(
            'alias' => '',
            'contrasenya' => ''
        );

        // Si es una solicitud POST, procesar el formulario de inicio de sesión
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {

            // Verificar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: CSRF token inválido.');
            }

            // Limpiar los datos del formulario
            $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
            $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');

            // Consultar al usuario en la base de datos
            $m = new GastosModelo();
            $usuario = $m->consultarUsuario($alias);

            // Verificar la contraseña usando la función unificada
            if ($usuario && comprobarhash($contrasenya, $usuario['contrasenya'])) {
                session_regenerate_id(true);

                // Establecer los datos del usuario en la sesión
                $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
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
        session_start();
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        header("Location: index.php?ctl=home");
        exit();
    }

    // Método de inicio para usuarios autenticados
    public function inicio()
    {
        // Asegurarse de que el usuario está autenticado
        if (!isset($_SESSION['usuario']) || $_SESSION['nivel_usuario'] == 0) {
            header('Location: index.php?ctl=home');
            exit();
        }

        // Mensaje de bienvenida personalizado según el usuario autenticado
        $params = array(
            'mensaje' => 'Bienvenido, ' . $_SESSION['usuario']['nombre'],
            'nivel_usuario' => $_SESSION['nivel_usuario'],
            'fecha' => date('d-m-Y')
        );

        // Renderizar la vista de inicio
        $this->render('inicio.php', $params);
    }

    // Método para renderizar las vistas
    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }
}
