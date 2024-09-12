<?php
require_once 'app/libs/bSeguridad.php'; // Incluimos las funciones de seguridad para los roles
require_once 'app/libs/bGeneral.php'; // Incluimos las funciones generales y de validación

class Controller {

    // Método para probar la conexión a la base de datos
    public function probarConexionBD() {
        $modelo = new GastosModelo();
        if ($modelo->pruebaConexion()) {
            echo "Conexión exitosa a la base de datos.";
        } else {
            echo "Fallo en la conexión a la base de datos.";
        }
    }

    private function cargaMenu() {
        if ($_SESSION['nivel_usuario'] == 'usuario') {
            return 'menuUser.php';
        } else if ($_SESSION['nivel_usuario'] == 'admin') {
            return 'menuAdmin.php';
        } else if ($_SESSION['nivel_usuario'] == 'superadmin') {
            return 'menuSuperadmin.php';
        }
    }

    private function render($vista, $params = array()) {
        ob_start();
        extract($params); 
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    public function home() {
        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y')
        );
        if (isset($_SESSION['nivel_usuario']) && $_SESSION['nivel_usuario'] > 0) {
            header("location:index.php?ctl=inicio");
            exit();
        }
        $this->render('inicio.php', $params);
    }

    public function inicio() {
        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y')
        );
        $this->render('inicio.php', $params);
    }

    public function salir() {
        session_destroy();
        header("location:index.php?ctl=home");
    }

    public function error() {
        $this->render('error.php');
    }

    public function iniciarSesion() {
        $params = array(
            'nombreUsuario' => '',
            'contrasenya' => ''
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {
            $nombreUsuario = htmlspecialchars(recoge('nombreUsuario'), ENT_QUOTES, 'UTF-8');
            $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');

            $m = new GastosModelo();
            if ($usuario = $m->consultarUsuario($nombreUsuario)) {
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
                    iniciarSesion($usuario);
                    header('Location: index.php?ctl=inicio');
                    exit();
                } else {
                    $params['mensaje'] = 'Contraseña incorrecta.';
                }
            } else {
                $params['mensaje'] = 'Usuario no encontrado.';
            }
        }
        $this->render('formIniciarSesion.php', $params);
    }

    public function registro() {
        $params = array(
            'nombre' => '',
            'apellido' => '',
            'nombreUsuario' => '',
            'contrasenya' => '',
            'fecha_nacimiento' => '',
            'email' => '',
            'telefono' => '',
            'nivel_usuario' => 'usuario',
            'es_menor' => false
        );
        $errores = array();

        if (isset($_POST['bRegistro'])) {
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $nombreUsuario = recoge('nombreUsuario');
            $contrasenya = recoge('contrasenya');
            $fecha_nacimiento = recoge('fecha_nacimiento');
            $email = recoge('email');
            $telefono = recoge('telefono');
            $nivel_usuario = recoge('nivel_usuario');

            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($nombreUsuario, "nombreUsuario", $errores);
            cContrasenya($contrasenya, $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);

            if (validarEdad($fecha_nacimiento, $errores)) {
                $params['es_menor'] = true;
                $nivel_usuario = 'usuario';
            }

            $m = new GastosModelo();
            if ($m->existeUsuario($nombreUsuario)) {
                $errores['nombreUsuario'] = "El nombre de usuario ya existe. Por favor elija otro.";
            }

            if (empty($errores)) {
                try {
                    $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);

                    if ($m->insertarUsuario($nombre, $apellido, $nombreUsuario, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono)) {
                        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
                        header('Location: index.php?ctl=iniciarSesion');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se ha podido insertar el usuario. Revisa el formulario.';
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
                    header('Location: index.php?ctl=error');
                } catch (Error $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logError.txt");
                    header('Location: index.php?ctl=error');
                }
            }
        }

        $this->render('formRegistro.php', $params);
    }
}
?>
