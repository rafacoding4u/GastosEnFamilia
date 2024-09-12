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
        if (isset($_SESSION['nivel_usuario'])) {
            if ($_SESSION['nivel_usuario'] == 'usuario') {
                return 'menuUser.php';
            } else if ($_SESSION['nivel_usuario'] == 'admin') {
                return 'menuAdmin.php';
            } else if ($_SESSION['nivel_usuario'] == 'superadmin') {
                return 'menuSuperadmin.php';
            } else {
                return 'menuUser.php'; // Valor por defecto para usuarios estándar
            }
        } else {
            return null;
        }
    }
    
    

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
            // Si no hay un menú específico, cargar solo el layout general
            require __DIR__ . '/../../web/templates/layout.php';
        }
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
        echo "DEBUG: nivel_usuario -> " . $_SESSION['nivel_usuario']; // Para verificar el nivel del usuario
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
            'alias' => '', // Actualizado de 'nombreUsuario' a 'alias'
            'contrasenya' => ''
        );
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bIniciarSesion'])) {
            $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
            $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');
    
            echo "DEBUG: Alias recibido: " . $alias . "<br>";
            echo "DEBUG: Contraseña recibida: " . $contrasenya . "<br>";
    
            $m = new GastosModelo();
            $usuario = $m->consultarUsuario($alias);
    
            if ($usuario) {
                echo "DEBUG: Usuario encontrado: ";
                print_r($usuario); // Ver los datos del usuario encontrados en la base de datos
                echo "<br>";
    
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
                    echo "DEBUG: Contraseña correcta<br>";
                    iniciarSesion($usuario);
                    header('Location: index.php?ctl=inicio');
                    exit();
                } else {
                    echo "DEBUG: Contraseña incorrecta<br>";
                    $params['mensaje'] = 'Contraseña incorrecta.';
                }
            } else {
                echo "DEBUG: Usuario no encontrado<br>";
                $params['mensaje'] = 'Usuario no encontrado.';
            }
        }
        $this->render('formIniciarSesion.php', $params);
    }
    
    
        

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
    
    
}
?>
