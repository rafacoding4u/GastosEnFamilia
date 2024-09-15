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

    // Página de inicio (landing page para usuarios no autenticados)
public function home() {
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
    
    $this->render('home.php', $params); // Usar una vista separada para el landing page
}


    /// Página principal del usuario autenticado
public function inicio() {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['usuario'])) {
        // Si no está autenticado, redirigir al inicio de sesión
        header("Location: index.php?ctl=iniciarSesion");
        exit();
    }

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

    $this->render('inicio.php', $params); // Renderizar la vista del usuario autenticado
}


    // Cerrar sesión
    public function salir() {
        session_start();  // Asegurarse de que la sesión está activa
        session_unset();  // Eliminar todas las variables de sesión
        session_destroy();  // Destruir la sesión
        header("Location: index.php?ctl=home");
        exit(); 
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
            'idFamilia' => null,
            'idGrupo' => null,
            'es_menor' => false
        );
        $errores = array();
    
        // Cargar grupos y familias existentes para el formulario
        $m = new GastosModelo();
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
    
        if (isset($_POST['bRegistro'])) {
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $alias = recoge('alias');
            $contrasenya = recoge('contrasenya');
            $fecha_nacimiento = recoge('fecha_nacimiento');
            $email = recoge('email');
            $telefono = recoge('telefono');
            $nivel_usuario = recoge('nivel_usuario');
            $idGrupoFamilia = recoge('idGrupoFamilia');
            $passwordFamiliaGrupo = recoge('passwordGrupoFamilia');
            $nombreNuevo = recoge('nombre_nuevo');
            $passwordNuevo = recoge('password_nuevo');
    
            // Validar campos
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($alias, "alias", $errores);
            cContrasenya($contrasenya, $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);
    
            // Verificar si seleccionó un grupo o familia existente
            if (!empty($idGrupoFamilia)) {
                if (strpos($idGrupoFamilia, 'grupo_') === 0) {
                    $idGrupo = substr($idGrupoFamilia, 6);
                    $grupo = $m->obtenerGrupoPorId($idGrupo);
                    if (!password_verify($passwordFamiliaGrupo, $grupo['password'])) {
                        $errores['idGrupo'] = "La contraseña del grupo es incorrecta.";
                    }
                } elseif (strpos($idGrupoFamilia, 'familia_') === 0) {
                    $idFamilia = substr($idGrupoFamilia, 8);
                    $familia = $m->obtenerFamiliaPorId($idFamilia);
                    if (!password_verify($passwordFamiliaGrupo, $familia['password'])) {
                        $errores['idFamilia'] = "La contraseña de la familia es incorrecta.";
                    }
                }
            }
    
            // Verificar si está creando un nuevo grupo o familia
            if (!empty($nombreNuevo) && !empty($passwordNuevo)) {
                $hashedPasswordNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                if ($_POST['tipo_vinculo'] == 'grupo') {
                    $idGrupo = $m->insertarGrupo($nombreNuevo, $hashedPasswordNuevo);
                } elseif ($_POST['tipo_vinculo'] == 'familia') {
                    $idFamilia = $m->insertarFamilia($nombreNuevo, $hashedPasswordNuevo);
                }
            }
    
            if (empty($errores)) {
                try {
                    $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $idGrupo, $idFamilia)) {
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
    
        $params['familias'] = $familias;
        $params['grupos'] = $grupos;
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
            $usuario = $m->obtenerUsuarioPorId($_SESSION['usuario']['id']);
            
            if ($usuario['nivel_usuario'] === 'superadmin') {
                // Si el usuario es superadmin, obtener la situación de la familia
                $situacion = $m->obtenerSituacionFinancieraFamilia($usuario['idFamilia']);
                $totalSaldo = $m->calcularSaldoGlobalFamilia($usuario['idFamilia']);
            } else {
                // Obtener situación financiera individual
                $situacion = $m->obtenerSituacionFinanciera($_SESSION['usuario']['id']);
                $totalSaldo = null;
            }
    
            $params = array(
                'situacion' => $situacion,
                'mensaje' => 'Tu situación financiera actual',
                'totalSaldo' => $totalSaldo
            );
            
            $this->render('verSituacion.php', $params);
        }
    
        // Formulario para insertar gasto
        public function formInsertarGasto() {
            $m = new GastosModelo();
            $params = array(
                'categorias' => $m->obtenerCategoriasGastos(),
                'mensaje' => ''
            );
            $this->render('formInsertarGasto.php', $params);
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
            $this->formInsertarGasto();
        }
    
        // Formulario para insertar ingreso
        public function formInsertarIngreso() {
            $m = new GastosModelo();
            $params = array(
                'categorias' => $m->obtenerCategoriasIngresos(),
                'mensaje' => ''
            );
            $this->render('formInsertarIngreso.php', $params);
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
            $this->formInsertarIngreso();
        }
    
        // Gestión de Categorías de Gastos
        public function verCategoriasGastos() {
            $m = new GastosModelo();
            $categorias = $m->obtenerCategoriasGastos();
            
            $params = array(
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de gastos'
            );
            
            $this->render('verCategoriasGastos.php', $params);
        }
    
        public function insertarCategoriaGasto() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaGasto'])) {
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                
                if ($m->insertarCategoriaGasto($nombreCategoria)) {
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo insertar la categoría de gasto.';
                }
            }
            $this->verCategoriasGastos();
        }
    
        public function editarCategoriaGasto() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaGasto'])) {
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
    
                if ($m->actualizarCategoriaGasto($idCategoria, $nombreCategoria)) {
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar la categoría de gasto.';
                }
            }
            $this->verCategoriasGastos();
        }
    
        public function eliminarCategoriaGasto() {
            if (isset($_GET['id'])) {
                $m = new GastosModelo();
                if ($m->eliminarCategoriaGasto($_GET['id'])) {
                    header('Location: index.php?ctl=verCategoriasGastos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo eliminar la categoría de gasto.';
                }
            }
        }
    
        // Gestión de Categorías de Ingresos
        public function verCategoriasIngresos() {
            $m = new GastosModelo();
            $categorias = $m->obtenerCategoriasIngresos();
            
            $params = array(
                'categorias' => $categorias,
                'mensaje' => 'Gestión de categorías de ingresos'
            );
            
            $this->render('verCategoriasIngresos.php', $params);
        }
    
        public function insertarCategoriaIngreso() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaIngreso'])) {
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
                
                if ($m->insertarCategoriaIngreso($nombreCategoria)) {
                    header('Location: index.php?ctl=verCategoriasIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo insertar la categoría de ingreso.';
                }
            }
            $this->verCategoriasIngresos();
        }
    
        public function editarCategoriaIngreso() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
                $idCategoria = recoge('idCategoria');
                $nombreCategoria = recoge('nombreCategoria');
                $m = new GastosModelo();
    
                if ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
                    header('Location: index.php?ctl=verCategoriasIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
                }
            }
            $this->verCategoriasIngresos();
        }
    
        public function eliminarCategoriaIngreso() {
            if (isset($_GET['id'])) {
                $m = new GastosModelo();
                if ($m->eliminarCategoriaIngreso($_GET['id'])) {
                    header('Location: index.php?ctl=verCategoriasIngresos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo eliminar la categoría de ingreso.';
                }
            }
        }
    
        // Gestión de Grupos
        public function verGrupos() {
            $m = new GastosModelo();
            $grupos = $m->obtenerGrupos();
            
            $params = array(
                'grupos' => $grupos,
                'mensaje' => 'Gestión de grupos'
            );
            
            $this->render('verGrupos.php', $params);
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
    
            // Editar Usuario
    public function editarUsuario() {
        $m = new GastosModelo();
        if (isset($_GET['id'])) {
            $usuario = $m->obtenerUsuarioPorId($_GET['id']);
            if (!$usuario) {
                $params['mensaje'] = 'Usuario no encontrado.';
                $this->listarUsuarios();
                return;
            }
        }

        $params = array(
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'alias' => $usuario['alias'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'idUser' => $usuario['idUser'],
            'nivel_usuario' => $usuario['nivel_usuario']
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarUsuario'])) {
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $alias = recoge('alias');
            $email = recoge('email');
            $telefono = recoge('telefono');
            $nivel_usuario = recoge('nivel_usuario');

            $errores = array();

            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($alias, "alias", $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);

            if (empty($errores)) {
                if ($m->actualizarUsuario($usuario['idUser'], $nombre, $apellido, $alias, $email, $telefono)) {
                    header('Location: index.php?ctl=listarUsuarios');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo actualizar el usuario.';
                }
            } else {
                $params['errores'] = $errores;
            }
        }

        $this->render('formEditarUsuario.php', $params);
    }
}
