<?php
require_once 'app/libs/bSeguridad.php'; 
require_once 'app/libs/bGeneral.php'; 

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

    // Cargar el menú según el nivel de usuario
    private function cargaMenu() {
        if (isset($_SESSION['nivel_usuario'])) {
            switch ($_SESSION['nivel_usuario']) {
                case 'usuario':
                    return 'menuUser.php';
                case 'admin':
                    return 'menuAdmin.php';
                case 'superadmin':
                    return 'menuSuperadmin.php';
                default:
                    return 'menuUser.php';
            }
        }
        return null;
    }

    // Renderizar una vista con los parámetros dados
    private function render($vista, $params = array()) {
        ob_start();
        extract($params);
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();

        // Cargar el menú si está disponible
        $menu = $this->cargaMenu();
        if ($menu) {
            require __DIR__ . '/../../web/templates/layout.php';
        } else {
            echo "Error: No se pudo cargar el menú.";
            require __DIR__ . '/../../web/templates/layout.php';
        }
    }

    // Página de inicio
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

    // Página principal del usuario
    public function inicio() {
        $m = new GastosModelo();
        $totalIngresos = $m->obtenerTotalIngresos($_SESSION['usuario']['id']);
        $totalGastos = $m->obtenerTotalGastos($_SESSION['usuario']['id']);
        $balance = $totalIngresos - $totalGastos;

        $params = array(
            'mensaje' => 'Bienvenido a GastosEnFamilia',
            'mensaje2' => 'Gestiona tus finanzas familiares de manera eficiente',
            'fecha' => date('d-m-Y'),
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'balance' => $balance
        );
        $this->render('inicio.php', $params);
    }

    // Cerrar sesión
    public function salir() {
        session_destroy();
        header("location:index.php?ctl=home");
    }

    // Página de error
    public function error() {
        $this->render('error.php');
    }

    // Iniciar sesión
    public function iniciarSesion() {
        $params = array(
            'alias' => '',
            'contrasenya' => ''
        );
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {
            $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
            $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
    
            $m = new GastosModelo();
            $usuario = $m->consultarUsuario($alias);
    
            if ($usuario) {
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
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
                    $params['mensaje'] = 'Contraseña incorrecta.';
                }
            } else {
                $params['mensaje'] = 'Usuario no encontrado.';
            }
        }
        $this->render('formIniciarSesion.php', $params);
    }

    // Registro de usuario
    public function registro() {
        $params = array(
            'nombre' => '',
            'apellido' => '',
            'alias' => '',
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
            $alias = recoge('alias');
            $contrasenya = recoge('contrasenya');
            $fecha_nacimiento = recoge('fecha_nacimiento');
            $email = recoge('email');
            $telefono = recoge('telefono');
            $nivel_usuario = recoge('nivel_usuario');
    
            // Validar campos
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($alias, "alias", $errores);
            cContrasenya($contrasenya, $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);
    
            // Validar la edad y asignar nivel de usuario si es menor
            if (validarEdad($fecha_nacimiento, $errores)) {
                $params['es_menor'] = true;
                $nivel_usuario = 'usuario';
            }
    
            // Comprobar si ya existe el alias en la base de datos
            $m = new GastosModelo();
            if ($m->existeUsuario($alias)) {
                $errores['alias'] = "El alias ya existe. Por favor elija otro.";
            }
    
            if (empty($errores)) {
                try {
                    $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono)) {
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

    // Ver Gastos
    public function verGastos() {
        $m = new GastosModelo();
        $gastos = $m->obtenerGastosPorUsuario($_SESSION['usuario']['id']);
        
        $params = array(
            'gastos' => $gastos,
            'mensaje' => 'Lista de tus gastos'
        );
        
        $this->render('verGastos.php', $params);
    }

    // Ver Ingresos
    public function verIngresos() {
        $m = new GastosModelo();
        $ingresos = $m->obtenerIngresosPorUsuario($_SESSION['usuario']['id']);
        
        $params = array(
            'ingresos' => $ingresos,
            'mensaje' => 'Lista de tus ingresos'
        );
        
        $this->render('verIngresos.php', $params);
    }

    // Ver Situación Financiera
    public function verSituacion() {
        $m = new GastosModelo();
        $situacion = $m->obtenerSituacionFinanciera($_SESSION['usuario']['id']);
        
        $params = array(
            'situacion' => $situacion,
            'mensaje' => 'Tu situación financiera actual'
        );
        
        $this->render('verSituacion.php', $params);
    }

    // Insertar Gasto
    public function insertarGasto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarGasto'])) {
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarGasto($_SESSION['usuario']['id'], $monto, $categoria, $concepto, $origen)) {
                header('Location: index.php?ctl=verGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el gasto.';
            }
        }
        $this->render('formInsertarGasto.php');
    }

    // Insertar Ingreso
    public function insertarIngreso() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarIngreso'])) {
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarIngreso($_SESSION['usuario']['id'], $monto, $categoria, $concepto, $origen)) {
                header('Location: index.php?ctl=verIngresos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el ingreso.';
            }
        }
        $this->render('formInsertarIngreso.php');
    }

    // Listar Usuarios
    public function listarUsuarios() {
        $m = new GastosModelo();
        $usuarios = $m->obtenerUsuarios();
        
        $params = array(
            'usuarios' => $usuarios,
            'mensaje' => 'Lista de usuarios registrados'
        );
        
        $this->render('listarUsuarios.php', $params);
    }

    // Eliminar Usuario
    public function eliminarUsuario() {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            if ($m->eliminarUsuario($_GET['id'])) {
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo eliminar el usuario.';
            }
        }
    }
}
