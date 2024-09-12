<?php
require_once 'app/libs/bSeguridad.php'; // Incluimos las funciones de seguridad para los roles

class Controller {

    private function cargaMenu() {
        // Mejora para usar los roles por nombre
        if ($_SESSION['usuario']['nivel_usuario'] == 'usuario') {
            return 'menuUser.php';
        } else if ($_SESSION['usuario']['nivel_usuario'] == 'admin') {
            return 'menuAdmin.php';
        } else if ($_SESSION['usuario']['nivel_usuario'] == 'superadmin') {
            return 'menuSuperadmin.php';
        }
    }

    private function render($vista, $params = array()) {
        // Buffer de salida para capturar la vista y pasarla al layout
        ob_start();
        extract($params); // Extraemos las variables para usarlas en la vista
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();

        // Cargar el layout con el contenido de la vista
        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    public function home() {
        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y')
        );
        if (isset($_SESSION['usuario']['nivel_usuario']) && $_SESSION['usuario']['nivel_usuario'] > 0) {
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
        cerrarSesion();
        header("location:index.php?ctl=home");
    }

    public function error() {
        $params = array('mensaje' => 'Ha ocurrido un error en el sistema. Por favor, inténtalo más tarde.');
        $this->render('error.php', $params);
    }

    public function iniciarSesion() {
        $params = array(
            'nombreUsuario' => '',
            'contrasenya' => '',
            'mensaje' => ''
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
            'es_menor' => false,
            'mensaje' => ''
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
    
            $fecha_actual = new DateTime();
            $fecha_nacimiento_dt = new DateTime($fecha_nacimiento);
            $edad = $fecha_actual->diff($fecha_nacimiento_dt)->y;
    
            if ($edad < 18) {
                $params['es_menor'] = true;
                $nivel_usuario = 'usuario';
            }
    
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($nombreUsuario, "nombreUsuario", $errores);
    
            if (strlen($contrasenya) < 8 || !preg_match('/[A-Z]/', $contrasenya) || !preg_match('/[0-9]/', $contrasenya)) {
                $errores['contrasenya'] = "La contraseña debe contener al menos 1 letra mayúscula, 1 número y tener un tamaño mínimo de 8 caracteres.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = "El correo electrónico no es válido.";
            }

            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                $errores['telefono'] = "El número de teléfono debe tener 9 dígitos.";
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

    // Función para ver la situación financiera
    public function verSituacion() {
        $modelo = new GastosModelo();
        $situacion = [];

        if (esSuperadmin()) {
            $situacion = $modelo->obtenerSituacionGlobal(); // Todos los usuarios
            $totalSaldo = $modelo->obtenerSaldoTotal();
        } elseif (esAdmin()) {
            $situacion = $modelo->obtenerSituacionGrupo($_SESSION['usuario']['idGrupo']);
            $totalSaldo = $modelo->obtenerSaldoTotalGrupo($_SESSION['usuario']['idGrupo']);
        } elseif (esUsuarioNormal()) {
            $situacion = $modelo->obtenerSituacionUsuario($_SESSION['usuario']['idUser']);
            $totalSaldo = null; // No se muestra saldo total
        }

        $params = [
            'situacion' => $situacion,
            'totalSaldo' => $totalSaldo ?? null
        ];

        $this->render('verSituacion.php', $params);
    }
}
