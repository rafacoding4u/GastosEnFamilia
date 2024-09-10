<?php
require_once 'app/libs/bSeguridad.php'; // Incluimos las funciones de seguridad para los roles

class Controller {

    private function cargaMenu() {
        // Mejora para usar los roles por nombre
        if ($_SESSION['nivel_usuario'] == 'usuario') {
            return 'menuUser.php';
        } else if ($_SESSION['nivel_usuario'] == 'admin') {
            return 'menuAdmin.php';
        } else if ($_SESSION['nivel_usuario'] == 'superadmin') {
            return 'menuSuperAdmin.php';
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
        
            if (isset($_POST['bIniciarSesion'])) {
                $nombreUsuario = htmlspecialchars(recoge('nombreUsuario'), ENT_QUOTES, 'UTF-8');
                $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
        
                $m = new GastosModelo();
                if ($usuario = $m->consultarUsuario($nombreUsuario)) {
                    if (password_verify($contrasenya, $usuario['contrasenya'])) {
                        // Iniciar sesión y guardar datos del usuario
                        iniciarSesion($usuario);
                        header('Location: index.php?ctl=inicio');
                    } else {
                        $params['mensaje'] = 'Contraseña incorrecta.';
                    }
                } else {
                    $params['mensaje'] = 'Usuario no encontrado.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            header('Location: index.php?ctl=error');
        }
    
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
            'fecha_nacimiento' => '',
            'email' => '',
            'telefono' => '',
            'nivel_usuario' => 'usuario', // Nivel de usuario por defecto
            'es_menor' => false // Inicializamos este parámetro para la vista
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
    
            // Calcular la edad a partir de la fecha de nacimiento
            $fecha_actual = new DateTime();
            $fecha_nacimiento_dt = new DateTime($fecha_nacimiento);
            $edad = $fecha_actual->diff($fecha_nacimiento_dt)->y;
    
            // Si el usuario es menor de 18 años, solo puede ser "usuario"
            if ($edad < 18) {
                $params['es_menor'] = true;
                $nivel_usuario = 'usuario'; // Forzamos el nivel de usuario a "usuario"
            }
    
            // Validaciones de los campos
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
                        header('Location: index.php?ctl=registro');
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
    
        require __DIR__ . '/../../web/templates/formRegistro.php';
    }
    
    public function listarGastos() {
        try {
            checkUser(); // Verifica que el usuario esté autenticado
            $m = new GastosModelo(); // Objeto para interactuar con la base de datos
            $usuario = $_SESSION['usuario'];
    
            // Verifica el rol y carga los gastos correspondientes
            if (esSuperadmin()) {
                $params = array('gastos' => $m->listarGastosTodos());
            } elseif (esAdmin()) {
                // Admin puede ver los gastos de su familia o grupo
                $params = array('gastos' => $m->listarGastosPorGrupoFamilia($usuario['idFamilia'], $usuario['idGrupo']));
            } elseif (esUsuarioNormal()) {
                // Usuario normal solo ve sus propios gastos
                $params = array('gastos' => $m->listarGastosPorUsuario($usuario['idUser']));
            }
    
            // Si no hay gastos que mostrar
            if (!$params['gastos']) {
                $params['mensaje'] = "No hay gastos que mostrar.";
            }
        } catch (Exception $e) {
            // Manejo de errores
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, __DIR__ . "/../log/logExcepcio.txt");
            header('Location: index.php?ctl=error');
        }
    
        $menu = $this->cargaMenu(); // Cargar el menú correspondiente
        require __DIR__ . '/../../web/templates/mostrarGastos.php'; // Cargar la vista
    }
    

    public function insertarGasto() {
        $menu = $this->cargaMenu();
        $errores = [];
        $params = ['concepto' => '', 'monto' => '', 'fecha' => ''];
        
        checkUser(); // Verifica que el usuario esté autenticado
    
        if (esAdmin() || esSuperadmin() || esUsuarioNormal()) {
            $m = new GastosModelo();
            if (isset($_POST['bInsertarGasto'])) {
                $concepto = htmlspecialchars(recoge('concepto'), ENT_QUOTES, 'UTF-8');
                $monto = htmlspecialchars(recoge('monto'), ENT_QUOTES, 'UTF-8');
                $fecha = recoge('fecha');
        
                if ($m->insertarGasto($concepto, $monto, $fecha, $_SESSION['usuario']['idUser'])) {
                    $_SESSION['mensaje_exito_gasto'] = 'Gasto registrado correctamente';
                    header('Location: index.php?ctl=insertarGasto');
                    exit();
                } else {
                    $params['mensaje'] = 'No se ha podido registrar el gasto.';
                }
            }
        }
        
        require __DIR__ . '/../../web/templates/formInsertarGasto.php';
    }
    
    function checkUser() {
        if (!isset($_SESSION['usuario'])) {
            // Redirigir al formulario de inicio de sesión si no hay usuario en la sesión
            header("Location: index.php?ctl=iniciarSesion");
            exit();
        }
        return $_SESSION['usuario']; // Retorna los datos del usuario almacenados en la sesión
    }
    public function enviarRefranDiario($momento) {
        $horaEnvio = ($momento == 'mañana') ? '11:00' : '20:00';
        $m = new RefranesModelo();
        
        // Obtener el refrán que no se haya usado en 365 días
        $refran = $m->obtenerRefranNoUsado();
    
        // Obtener la lista de usuarios
        $usuarios = $m->listarUsuarios();
        
        foreach ($usuarios as $usuario) {
            // Enviar refrán por correo electrónico
            mail($usuario['email'], "Refrán del día", $refran['texto_refran']);
    
            // Registrar el envío en la base de datos
            $m->registrarEnvioRefran($refran['idRefran'], $usuario['idUser'], $momento);
        }
    }

}
