<?php

class Controller {

    private function cargaMenu() {
        if ($_SESSION['nivel_usuario'] == 0) {
            return 'menuInvitado.php';
        } else if ($_SESSION['nivel_usuario'] == 1) {
            return 'menuUser.php';
        } else if ($_SESSION['nivel_usuario'] == 2) {
            return 'menuAdmin.php';
        }
    }

    public function home() {
        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y')
        );
        $menu = 'menuHome.php';

        if ($_SESSION['nivel_usuario'] > 0) {
            header("location:index.php?ctl=inicio");
        }
        require __DIR__ . '/../../web/templates/inicio.php';
    }

    public function inicio() {
        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y')
        );
        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/inicio.php';
    }

    public function salir() {
        session_destroy();
        header("location:index.php?ctl=home");
    }

    public function error() {
        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/error.php';
    }

    public function iniciarSesion() {
        try {
            $params = array(
                'nombreUsuario' => '',
                'contrasenya' => ''
            );
            $menu = $this->cargaMenu();
    
            // Si ya hay un usuario con sesión activa, redirige al inicio
            if ($_SESSION['nivel_usuario'] > 0) {
                header("location:index.php?ctl=inicio");
            }
    
            // Cuando se envía el formulario
            if (isset($_POST['bIniciarSesion'])) {
                $nombreUsuario = htmlspecialchars(recoge('nombreUsuario'), ENT_QUOTES, 'UTF-8');
                $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
    
                // Verifica si el nombre de usuario es válido
                if (cUser($nombreUsuario, "nombreUsuario", $params)) {
                    $m = new GastosModelo();
                    // Consulta el usuario en la base de datos
                    if ($usuario = $m->consultarUsuario($nombreUsuario)) {
                        // Compara la contraseña ingresada con la contraseña hasheada en la base de datos
                        if (password_verify($contrasenya, $usuario['contrasenya'])) {
                            // La contraseña es correcta, se inicia la sesión
                            $_SESSION['idUser'] = $usuario['idUser'];
                            $_SESSION['nombreUsuario'] = $usuario['nombreUsuario'];
                            $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
    
                            // Redirige al inicio
                            header('Location: index.php?ctl=inicio');
                        } else {
                            // La contraseña es incorrecta
                            $params['mensaje'] = 'Contraseña incorrecta.';
                        }
                    } else {
                        $params = array(
                            'nombreUsuario' => $nombreUsuario,
                            'contrasenya' => $contrasenya
                        );
                        $params['mensaje'] = 'No se ha podido iniciar sesión. Revisa el formulario.';
                    }
                } else {
                    $params = array(
                        'nombreUsuario' => $nombreUsuario,
                        'contrasenya' => $contrasenya
                    );
                    $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logError.txt");
            header('Location: index.php?ctl=error');
        }
        
        // Muestra el formulario de inicio de sesión
        require __DIR__ . '/../../web/templates/formIniciarSesion.php';
    }
    
    
    public function registro() {
        $menu = $this->cargaMenu();
        if ($_SESSION['nivel_usuario'] > 0) {
            header("location:index.php?ctl=inicio");
        }
    
        $params = array(
            'nombre' => '',
            'apellido' => '',
            'nombreUsuario' => '',
            'contrasenya' => '',
            'nivel_usuario' => 1
        );
        $errores = array();
    
        if (isset($_POST['bRegistro'])) {
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $nombreUsuario = recoge('nombreUsuario');
            $contrasenya = recoge('contrasenya');
            $nivel_usuario = recoge('nivel_usuario');
    
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($nombreUsuario, "nombreUsuario", $errores);
    
            // Verificación de la contraseña
            if (strlen($contrasenya) < 8 || !preg_match('/[A-Z]/', $contrasenya) || !preg_match('/[0-9]/', $contrasenya)) {
                $errores['contrasenya'] = "La contraseña debe contener al menos 1 letra mayúscula, 1 número y tener un tamaño mínimo de 8 caracteres.";
            }
    
            $m = new GastosModelo();
            if ($m->existeUsuario($nombreUsuario)) {
                $errores['nombreUsuario'] = "El nombre de usuario ya existe. Por favor elija otro.";
            }
    
            if (empty($errores)) {
                try {
                    // Cifra la contraseña de manera segura utilizando password_hash
                    $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);
    
                    if ($m->insertarUsuario($nombre, $apellido, $nombreUsuario, $hashedPassword, $nivel_usuario)) {
                        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
                        header('Location: index.php?ctl=registro');
                        exit();
                    } else {
                        $params = array(
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'nombreUsuario' => $nombreUsuario,
                            'contrasenya' => $contrasenya,
                            'nivel_usuario' => $nivel_usuario
                        );
                        $params['mensaje'] = 'No se ha podido insertar el usuario. Revisa el formulario.';
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
                    header('Location: index.php?ctl=error');
                } catch (Error $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logError.txt");
                    header('Location: index.php?ctl=error');
                }
            } else {
                $params = array(
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'nombreUsuario' => $nombreUsuario,
                    'contrasenya' => $contrasenya,
                    'nivel_usuario' => $nivel_usuario
                );
                $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
            }
        } else {
            if (isset($_SESSION['mensaje_exito'])) {
                $params['mensaje'] = $_SESSION['mensaje_exito'];
                unset($_SESSION['mensaje_exito']);
            }
        }
    
        require __DIR__ . '/../../web/templates/formRegistro.php';
    }
    

    public function listarGastos() {
        try {
            $m = new GastosModelo();
            $params = array(
                'gastos' => $m->listarGastos(),
            );
            if (!$params['gastos']) {
                $params['mensaje'] = "No hay gastos que mostrar.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logError.txt");
            header('Location: index.php?ctl=error');
        }
    
        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/mostrarGastos.php';
    }

    public function insertarGasto() {
        $menu = $this->cargaMenu();
        $errores = [];
        $params = [
            'concepto' => '',
            'monto' => '',
            'fecha' => ''
        ];
    
        if ($_SESSION['nivel_usuario'] == 0) {
            header("location:index.php?ctl=home");
        }
    
        $m = new GastosModelo();
    
        if (isset($_POST['bInsertarGasto'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $errores[] = "Error en el token CSRF";
            }
    
            $concepto = htmlspecialchars(recoge('concepto'), ENT_QUOTES, 'UTF-8');
            $monto = htmlspecialchars(recoge('monto'), ENT_QUOTES, 'UTF-8');
            $fecha = recoge('fecha');
    
            if (!filter_var($concepto, FILTER_SANITIZE_STRING)) {
                $errores[] = "Error en el campo concepto";
            }
    
            if (!filter_var($monto, FILTER_VALIDATE_FLOAT)) {
                $errores[] = "Error en el campo monto";
            } else {
                $monto = floatval($monto);
            }
    
            if (empty($errores)) {
                try {
                    if ($m->insertarGasto($concepto, $monto, $fecha)) {
                        $_SESSION['mensaje_exito_gasto'] = 'Gasto registrado correctamente';
                        header('Location: index.php?ctl=insertarGasto');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se ha podido registrar el gasto. Revisa el formulario.';
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExcepcio.txt");
                    header('Location: index.php?ctl=error');
                } catch (Error $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
                    header('Location: index.php?ctl=error');
                }
            } else {
                $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
            }
        } else {
            if (isset($_SESSION['mensaje_exito_gasto'])) {
                $params['mensaje'] = $_SESSION['mensaje_exito_gasto'];
                unset($_SESSION['mensaje_exito_gasto']);
            }
        }
    
        require __DIR__ . '/../../web/templates/formInsertarGasto.php';
    }
}
?>
