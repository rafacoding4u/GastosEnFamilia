<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class Controller
{

    // Método para probar la conexión a la base de datos
    public function probarConexionBD()
    {
        $modelo = new GastosModelo();
        if ($modelo->pruebaConexion()) {
            echo "Conexión exitosa a la base de datos.";
        } else {
            echo "Fallo en la conexión a la base de datos.";
        }
    }

    // Cargar el menú según el nivel de usuario
    private function cargaMenu()
    {
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
    // Renderizar una vista con los parámetros dados
    // Renderizar una vista con los parámetros dados
    private function render($vista, $params = array())
    {
        extract($params); // Extraer los parámetros
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();

        // Verificar si el contenido fue generado correctamente
        if (!empty($contenido)) {
            $menu = $this->cargaMenu();
            require __DIR__ . '/../../web/templates/layout.php'; // Cargar layout y contenido
        } else {
            echo "Error: Contenido no disponible.";
        }
    }


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

        $this->render('home.php', $params); // Usar una vista separada para el landing page
    }


    /// Página principal del usuario autenticado
    public function inicio()
    {
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
    public function salir()
    {
        session_start();  // Asegurarse de que la sesión está activa
        session_regenerate_id(true); // Para prevenir ataques de fijación de sesión
        session_unset();  // Eliminar todas las variables de sesión
        session_destroy();  // Destruir la sesión
        header("Location: index.php?ctl=home");
        exit();
    }

    // Página de error
    public function error()
    {
        $this->render('error.php');
    }

    // Iniciar sesión
    public function iniciarSesion()
    {
        // Inicializar parámetros
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

            // Limpiar los datos del formulario para evitar inyecciones
            $alias = htmlspecialchars(recoge('alias'), ENT_QUOTES, 'UTF-8');
            $contrasenya = htmlspecialchars(recoge('contrasenya'), ENT_QUOTES, 'UTF-8');

            // Consultar al usuario en la base de datos
            $m = new GastosModelo();
            $usuario = $m->consultarUsuario($alias);

            // Si el usuario existe
            if ($usuario) {
                // Verificar la contraseña
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
                    // Regenerar el ID de sesión por seguridad
                    session_regenerate_id(true);

                    // Establecer los datos del usuario en la sesión
                    $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
                    $_SESSION['usuario'] = array(
                        'id' => $usuario['idUser'],
                        'nombre' => $usuario['nombre'],
                        'nivel_usuario' => $usuario['nivel_usuario'],
                        'email' => $usuario['email']
                    );

                    // Redirigir a la página de inicio
                    header('Location: index.php?ctl=inicio');
                    exit();
                } else {
                    // Mensaje de error si la contraseña es incorrecta
                    $params['mensaje'] = 'Contraseña incorrecta.';
                }
            } else {
                // Mensaje de error si el usuario no se encuentra
                $params['mensaje'] = 'Usuario no encontrado.';
            }
        }

        // Generar token CSRF y almacenarlo en la sesión
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $params['csrf_token'] = $_SESSION['csrf_token'];

        // Renderizar el formulario de inicio de sesión
        $this->render('formIniciarSesion.php', $params);
    }


// Registro de usuario
public function registro()
{
    $params = array(
        'nombre' => '',
        'apellido' => '',
        'alias' => '',
        'contrasenya' => '',
        'fecha_nacimiento' => '',
        'email' => '',
        'telefono' => '',
        'nivel_usuario' => 'usuario',
        'idFamilia' => null, // Inicializando la variable
        'idGrupo' => null, // Inicializando la variable
        'es_menor' => false
    );
    $errores = array();

    // Cargar grupos y familias existentes para el formulario
    $m = new GastosModelo();

    if ($_SESSION['nivel_usuario'] === 'superadmin') {
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();
    } elseif ($_SESSION['nivel_usuario'] === 'admin') {
        $familias = $m->obtenerFamiliasPorAdministrador($_SESSION['usuario']['id']);
        $grupos = $m->obtenerGruposPorAdministrador($_SESSION['usuario']['id']);
    }

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

        // Inicializar las variables antes de usarlas
        $idFamilia = null;
        $idGrupo = null;

        // Registro de usuario (comprobación de contraseñas de grupo o familia)
        if (!empty($idGrupoFamilia)) {
            if (strpos($idGrupoFamilia, 'grupo_') === 0) {
                $idGrupo = substr($idGrupoFamilia, 6);
                if (!$m->verificarPasswordGrupo($idGrupo, $passwordFamiliaGrupo)) {
                    $errores['idGrupo'] = "La contraseña del grupo es incorrecta.";
                }
            } elseif (strpos($idGrupoFamilia, 'familia_') === 0) {
                $idFamilia = substr($idGrupoFamilia, 8);
                if (!$m->verificarPasswordFamilia($idFamilia, $passwordFamiliaGrupo)) {
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



    // Formulario para crear una nueva familia
    public function formCrearFamilia()
    {
        // Solo superadmin tiene acceso
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            header('Location: index.php?ctl=inicio');
            exit();
        }

        $this->render('formCrearFamilia.php');
    }

    // Método para redirigir en caso de errores
    private function redireccionarError($mensaje)
    {
        // Almacenar el mensaje de error en la sesión
        $_SESSION['error_mensaje'] = $mensaje;

        // Redirigir a la página de error o a una página designada
        header("Location: index.php?ctl=error");
        exit();
    }


    // Crear una nueva familia
    public function crearFamilia()
    {
        // Verificar si el usuario es superadmin
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo superadmin puede crear familias.');
            return;
        }

        // Procesar el formulario si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearFamilia'])) {
            $nombre_familia = recoge('nombre_familia');
            $password_familia = recoge('password_familia');

            $errores = array();
            cTexto($nombre_familia, "nombre_familia", $errores);
            cContrasenya($password_familia, $errores);

            // Si no hay errores, insertar la familia
            if (empty($errores)) {
                $m = new GastosModelo();
                $hashedPassword = password_hash($password_familia, PASSWORD_DEFAULT); // Encriptar la contraseña

                if ($m->insertarFamilia($nombre_familia, $hashedPassword)) {
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo crear la familia.';
                }
            } else {
                $params['errores'] = $errores;
            }

            $this->render('formCrearFamilia.php', $params);
        } else {
            // Mostrar el formulario por defecto si no es POST
            $this->render('formCrearFamilia.php');
        }
    }

    // Listar Familias
    public function listarFamilias()
    {
        $m = new GastosModelo();
        $familias = $m->obtenerFamilias(); // Utiliza el método del modelo que ya tienes implementado

        $params = array(
            'familias' => $familias,
            'mensaje' => 'Lista de familias registradas'
        );

        $this->render('listarFamilias.php', $params); // Renderiza la vista listarFamilias
    }
    // Editar Familia
    public function editarFamilia()
{
    $m = new GastosModelo();

    // Obtener el ID de la familia
    if (isset($_GET['id'])) {
        $familia = $m->obtenerFamiliaPorId($_GET['id']);
        if (!$familia) {
            $params['mensaje'] = 'Familia no encontrada.';
            $this->listarFamilias();
            return;
        }
    }

    // Comprobar si el usuario es superadmin o administrador de la familia
    $esAdmin = false;

    // Si el usuario no es superadmin, comprobar si es administrador de la familia
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $administradores = $m->obtenerAdministradoresFamilia($familia['idFamilia']);
        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                $esAdmin = true;
                break;
            }
        }

        if (!$esAdmin) {
            $this->redireccionarError('No tienes permiso para editar esta familia.');
            return;
        }
    }

    $params = array(
        'nombre_familia' => $familia['nombre_familia'],
        'idFamilia' => $familia['idFamilia']
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarFamilia'])) {
        $nombre_familia = recoge('nombre_familia');
        $errores = array();

        cTexto($nombre_familia, "nombre_familia", $errores);

        if (empty($errores)) {
            if ($m->actualizarFamilia($familia['idFamilia'], $nombre_familia)) {
                header('Location: index.php?ctl=listarFamilias');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar la familia.';
            }
        } else {
            $params['errores'] = $errores;
        }
    }

    $this->render('formEditarFamilia.php', $params);
}


    // Eliminar Familia
    public function eliminarFamilia()
{
    // Verificar si el usuario es superadmin
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo superadmin puede eliminar familias.');
        return;
    }

    $idFamilia = recoge('id'); // Obtener el ID de la familia
    $m = new GastosModelo();
    
    // Validar si hay usuarios asociados a esta familia
    $usuariosAsociados = $m->obtenerUsuariosPorFamilia($idFamilia);
    if (!empty($usuariosAsociados)) {
        $this->redireccionarError('No se puede eliminar la familia. Hay usuarios asociados.');
        return;
    }

    // Si no hay usuarios asociados, eliminar la familia
    if ($m->eliminarFamilia($idFamilia)) {
        header('Location: index.php?ctl=listarFamilias');
        exit();
    } else {
        $this->redireccionarError('Error al eliminar la familia.');
    }
}




    public function unirseGrupoFamilia()
    {
        $tipo = recoge('tipo');
        $idGrupoFamilia = recoge('idGrupoFamilia');
        $password = recoge('password');
        $m = new GastosModelo();

        // Verificar si el grupo o familia existe y si la contraseña es correcta
        if ($tipo === 'grupo') {
            $grupo = $m->obtenerGrupoPorId($idGrupoFamilia);
            if ($grupo && password_verify($password, $grupo['password'])) {
                $m->añadirUsuarioAGrupo($_SESSION['usuario']['id'], $idGrupoFamilia);
                $mensaje = "Te has unido al grupo correctamente.";
            } else {
                $mensaje = "Contraseña incorrecta o grupo no encontrado.";
            }
        } elseif ($tipo === 'familia') {
            $familia = $m->obtenerFamiliaPorId($idGrupoFamilia);
            if ($familia && password_verify($password, $familia['password'])) {
                $m->añadirUsuarioAFamilia($_SESSION['usuario']['id'], $idGrupoFamilia);
                $mensaje = "Te has unido a la familia correctamente.";
            } else {
                $mensaje = "Contraseña incorrecta o familia no encontrada.";
            }
        }

        $params = ['mensaje' => $mensaje];
        $this->render('resultadoUnion.php', $params);
    }


    // Formulario para insertar gasto de otro usuario
    public function formInsertarGastoOtroUsuario()
    {
        $idUsuario = recoge('id');
        $m = new GastosModelo();

        // Asegurarse de que el usuario no es un superusuario
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        if ($usuario['nivel_usuario'] === 'superadmin') {
            header('Location: index.php?ctl=error');
            exit();
        }

        $params = array(
            'categorias' => $m->obtenerCategoriasGastos(),
            'usuario' => $usuario
        );
        $this->render('formInsertarGasto.php', $params);
    }


    // Ver Gastos
    public function verGastos()
    {
        $m = new GastosModelo();

        // Parámetros de filtro
        $fechaInicio = isset($_GET['fechaInicio']) ? recoge('fechaInicio') : null;
        $fechaFin = isset($_GET['fechaFin']) ? recoge('fechaFin') : null;
        $categoria = isset($_GET['categoria']) ? recoge('categoria') : null;
        $origen = isset($_GET['origen']) ? recoge('origen') : null;

        // Parámetros de paginación
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $registrosPorPagina = 10;
        $offset = ($paginaActual - 1) * $registrosPorPagina;

        // Obtener los gastos aplicando los filtros y la paginación
        $gastos = $m->obtenerGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen, $offset, $registrosPorPagina);

        // Obtener el número total de gastos para la paginación
        $totalGastos = $m->contarGastosFiltrados($_SESSION['usuario']['id'], $fechaInicio, $fechaFin, $categoria, $origen);
        $totalPaginas = ceil($totalGastos / $registrosPorPagina);

        // Pasar las categorías a la vista
        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gastos' => $gastos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'categoriaSeleccionada' => $categoria,
            'origenSeleccionado' => $origen
        );

        $this->render('verGastos.php', $params);
    }



    // Ver Gastos de un Usuario Específico (para superadmin)
    public function verGastosUsuario()
    {
        $idUsuario = recoge('id'); // Obtener el ID del usuario
        $m = new GastosModelo();

        // Asegurarse de que el usuario no es un superusuario
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        if ($usuario['nivel_usuario'] === 'superadmin') {
            header('Location: index.php?ctl=error'); // Redirigir si es superadmin
            exit();
        }

        // Obtener los gastos del usuario
        $gastos = $m->obtenerGastosPorUsuario($idUsuario);

        $params = array(
            'gastos' => $gastos,
            'usuario' => $usuario
        );

        $this->render('verGastos.php', $params); // Renderizar la vista
    }

    // Insertar gasto para otro usuario
    public function insertarGastoOtroUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarGasto'])) {
            $idUsuario = recoge('idUsuario');
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarGasto($idUsuario, $monto, $categoria, $concepto, $origen)) {
                header("Location: index.php?ctl=verGastosUsuario&id=$idUsuario");
                exit();
            }
        }
    }

    // Formulario para insertar ingreso de otro usuario
    public function formInsertarIngresoOtroUsuario()
    {
        $idUsuario = recoge('id');
        $m = new GastosModelo();

        // Asegurarse de que el usuario no es un superusuario
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        if ($usuario['nivel_usuario'] === 'superadmin') {
            header('Location: index.php?ctl=error');
            exit();
        }

        $params = array(
            'categorias' => $m->obtenerCategoriasIngresos(),
            'usuario' => $usuario
        );
        $this->render('formInsertarIngreso.php', $params);
    }

    // Insertar ingreso para otro usuario
    public function insertarIngresoOtroUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarIngreso'])) {
            $idUsuario = recoge('idUsuario');
            $monto = recoge('importe');
            $categoria = recoge('idCategoria');
            $concepto = recoge('concepto');
            $origen = recoge('origen');

            $m = new GastosModelo();
            if ($m->insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen)) {
                header("Location: index.php?ctl=verIngresosUsuario&id=$idUsuario");
                exit();
            }
        }
    }

    // Ver Ingresos
    public function verIngresos()
    {
        $m = new GastosModelo();

        // Obtener las categorías de ingresos para mostrarlas en la vista
        $categorias = $m->obtenerCategoriasIngresos(); // Método para obtener las categorías de ingresos

        if ($_SESSION['nivel_usuario'] === 'superadmin') {
            $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
            $idSeleccionado = isset($_GET['idSeleccionado']) ? $_GET['idSeleccionado'] : null;

            if ($tipo === 'todos') {
                $ingresos = $m->obtenerTodosIngresos();
            } elseif ($tipo === 'familia') {
                $ingresos = $m->obtenerIngresosPorFamilia($idSeleccionado);
            } elseif ($tipo === 'grupo') {
                $ingresos = $m->obtenerIngresosPorGrupo($idSeleccionado);
            } elseif ($tipo === 'usuario') {
                $ingresos = $m->obtenerIngresosPorUsuario($idSeleccionado);
            }

            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();
            $usuarios = $m->obtenerUsuarios();
        } else {
            $ingresos = $m->obtenerIngresosPorUsuario($_SESSION['usuario']['id']);
        }

        // Depuración (Puedes eliminar esto si ya no lo necesitas)
        echo "<pre>";
        print_r($ingresos); // Mostrar los datos de ingresos
        print_r($categorias); // Mostrar las categorías de ingresos
        echo "</pre>";
        // Fin de la depuración

        // Pasar las categorías a la vista junto con los ingresos
        $params = array(
            'ingresos' => $ingresos,
            'categorias' => $categorias, // Asegúrate de pasar las categorías a la vista
            'familias' => $familias ?? null,
            'grupos' => $grupos ?? null,
            'usuarios' => $usuarios ?? null,
            'tipo' => $tipo ?? 'todos',
            'idSeleccionado' => $idSeleccionado ?? null
        );

        $this->render('verIngresos.php', $params);
    }



    public function verDetalleIngreso()
    {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            $ingreso = $m->obtenerIngresoPorId($_GET['id']);

            if ($ingreso) {
                $params = array(
                    'ingreso' => $ingreso
                );
                $this->render('verDetalleIngreso.php', $params);
            } else {
                header('Location: index.php?ctl=verIngresos');
            }
        }
    }

    public function verDetalleGasto()
    {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            $gasto = $m->obtenerGastoPorId($_GET['id']);

            if ($gasto) {
                $params = array(
                    'gasto' => $gasto
                );
                $this->render('verDetalleGasto.php', $params);
            } else {
                header('Location: index.php?ctl=verGastos');
            }
        }
    }

    public function editarGasto()
    {
        $m = new GastosModelo();

        if (isset($_GET['id'])) {
            $gasto = $m->obtenerGastoPorId($_GET['id']);

            if (!$gasto) {
                header('Location: index.php?ctl=verGastos');
                exit();
            }
        }

        $categorias = $m->obtenerCategoriasGastos();

        $params = array(
            'gasto' => $gasto,
            'categorias' => $categorias
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGasto'])) {
            $concepto = recoge('concepto');
            $importe = recoge('importe');
            $fecha = recoge('fecha');
            $origen = recoge('origen');
            $categoria = recoge('categoria');

            if ($m->actualizarGasto($gasto['idGasto'], $concepto, $importe, $fecha, $origen, $categoria)) {
                header('Location: index.php?ctl=verGastos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar el gasto. Inténtalo de nuevo.';
            }
        }

        $this->render('formEditarGasto.php', $params);
    }

    public function eliminarGasto()
    {
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            if ($m->eliminarGasto($_GET['id'])) {
                header('Location: index.php?ctl=verGastos');
            } else {
                $params['mensaje'] = 'No se pudo eliminar el gasto. Inténtalo de nuevo.';
                $this->verGastos();
            }
        }
    }






    // Ver Ingresos de un Usuario Específico
    public function verIngresosUsuario()
    {
        $idUsuario = recoge('id'); // Obtener el ID del usuario
        $m = new GastosModelo();

        // Obtener el usuario que se está intentando visualizar
        $usuario = $m->obtenerUsuarioPorId($idUsuario);

        // Verificar si el usuario actual es un superadmin o administrador
        if ($_SESSION['nivel_usuario'] === 'admin' || $_SESSION['nivel_usuario'] === 'superadmin') {
            // Asegurarse de que el administrador no puede acceder a un superadmin
            if ($usuario['nivel_usuario'] === 'superadmin') {
                $this->redireccionarError('Acceso denegado. No puedes acceder a los datos de un superusuario.');
                return;
            }
        } else {
            // Si es un usuario normal, solo puede ver sus propios datos
            if ($idUsuario !== $_SESSION['usuario']['id']) {
                $this->redireccionarError('Acceso denegado. Solo puedes acceder a tus propios datos.');
                return;
            }
        }

        // Obtener los ingresos del usuario
        $ingresos = $m->obtenerIngresosPorUsuario($idUsuario);

        $params = array(
            'ingresos' => $ingresos,
            'usuario' => $usuario
        );

        $this->render('verIngresos.php', $params); // Renderizar la vista
    }


    public function verSituacion()
{
    $m = new GastosModelo();
    $params = [];

    // Obtener el tipo seleccionado (global, familia, grupo, usuario)
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'global';
    $idSeleccionado = isset($_GET['idSeleccionado']) ? $_GET['idSeleccionado'] : null;
    $params['tipo'] = $tipo;

    // Depuración: verificar tipo y ID seleccionado
    error_log("Tipo seleccionado: " . $tipo);
    error_log("ID Seleccionado: " . $idSeleccionado);

    // Comprobar el nivel de usuario
    if ($_SESSION['nivel_usuario'] === 'normal') {
        error_log("El usuario es de tipo 'normal'");
        $idUsuario = $_SESSION['usuario']['id'];
        $situacion = $m->obtenerSituacionFinanciera($idUsuario);
        error_log("Situación Financiera obtenida: " . print_r($situacion, true));

        if ($situacion === false) {
            error_log("Error: No se pudo obtener la situación financiera para el usuario con ID " . $idUsuario);
        }

        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        error_log("Usuario obtenido: " . print_r($usuario, true));

        if ($usuario === false) {
            error_log("Error: No se pudo obtener el usuario con ID " . $idUsuario);
        }

        $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idUsuario);
        $usuario['totalGastos'] = $m->obtenerTotalGastos($idUsuario);
        $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];

        $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idUsuario);
        $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idUsuario);

        $params['usuarios'] = [$usuario];
        $params['situacion'] = $situacion;
    } elseif ($_SESSION['nivel_usuario'] === 'admin') {
        error_log("El usuario es de tipo 'admin'");
        error_log("ID Usuario (admin): " . $_SESSION['usuario']['id']);

        $familiasAsignadas = $m->obtenerFamiliasPorAdministrador($_SESSION['usuario']['id']);
        error_log("Familias Asignadas obtenidas: " . print_r($familiasAsignadas, true));

        $gruposAsignados = $m->obtenerGruposPorAdministrador($_SESSION['usuario']['id']);
        error_log("Grupos Asignados obtenidos: " . print_r($gruposAsignados, true));

        if ($tipo === 'familia' && $idSeleccionado) {
            error_log("Tipo: familia con ID seleccionado: " . $idSeleccionado);
            if (in_array($idSeleccionado, array_column($familiasAsignadas, 'idFamilia'))) {
                error_log("El usuario es administrador de la familia con ID " . $idSeleccionado);
                $situacion = $m->obtenerSituacionFinancieraFamilia($idSeleccionado);
                error_log("Situación Financiera Familia: " . print_r($situacion, true));

                $usuarios = $m->obtenerUsuariosPorFamilia($idSeleccionado);
                foreach ($usuarios as &$usuario) {
                    $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
                    $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
                    $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
                    $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
                    $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
                }
                $params['situacion'] = $situacion;
                $params['usuarios'] = $usuarios;
            } else {
                error_log("El usuario no tiene permiso para ver la familia con ID " . $idSeleccionado);
            }
        } elseif ($tipo === 'grupo' && $idSeleccionado) {
            error_log("Tipo: grupo con ID seleccionado: " . $idSeleccionado);
            if (in_array($idSeleccionado, array_column($gruposAsignados, 'idGrupo'))) {
                error_log("El usuario es administrador del grupo con ID " . $idSeleccionado);
                $situacion = $m->obtenerSituacionFinancieraGrupo($idSeleccionado);
                error_log("Situación Financiera Grupo: " . print_r($situacion, true));

                $usuarios = $m->obtenerUsuariosPorGrupo($idSeleccionado);
                foreach ($usuarios as &$usuario) {
                    $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
                    $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
                    $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
                    $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
                    $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
                }
                $params['situacion'] = $situacion;
                $params['usuarios'] = $usuarios;
            } else {
                error_log("El usuario no tiene permiso para ver el grupo con ID " . $idSeleccionado);
            }
        } elseif ($tipo === 'usuario' && $idSeleccionado) {
            error_log("Tipo: usuario con ID seleccionado: " . $idSeleccionado);
            $usuario = $m->obtenerUsuarioPorId($idSeleccionado);
            if ($usuario && $usuario['nivel_usuario'] !== 'superadmin') {
                $situacion = $m->obtenerSituacionFinanciera($idSeleccionado);
                error_log("Situación Financiera Usuario: " . print_r($situacion, true));

                $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idSeleccionado);
                $usuario['totalGastos'] = $m->obtenerTotalGastos($idSeleccionado);
                $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
                $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idSeleccionado);
                $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idSeleccionado);
                $params['usuarios'] = [$usuario];
                $params['situacion'] = $situacion;
            }
        }
        $params['familias'] = $familiasAsignadas;
        $params['grupos'] = $gruposAsignados;
    } elseif ($_SESSION['nivel_usuario'] === 'superadmin') {
        error_log("El usuario es de tipo 'superadmin'");
        if ($tipo === 'global') {
            $situacion = $m->obtenerSituacionGlobal();
            error_log("Situación Financiera Global: " . print_r($situacion, true));
            $params['situacion'] = $situacion;
        } elseif ($tipo === 'familia' && $idSeleccionado) {
            $situacion = $m->obtenerSituacionFinancieraFamilia($idSeleccionado);
            error_log("Situación Financiera Familia: " . print_r($situacion, true));

            $usuarios = $m->obtenerUsuariosPorFamilia($idSeleccionado);
            foreach ($usuarios as &$usuario) {
                $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
                $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
                $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
                $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
                $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
            }
            $params['situacion'] = $situacion;
            $params['usuarios'] = $usuarios;
        } elseif ($tipo === 'grupo' && $idSeleccionado) {
            $situacion = $m->obtenerSituacionFinancieraGrupo($idSeleccionado);
            error_log("Situación Financiera Grupo: " . print_r($situacion, true));

            $usuarios = $m->obtenerUsuariosPorGrupo($idSeleccionado);
            foreach ($usuarios as &$usuario) {
                $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
                $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
                $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
                $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
                $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
            }
            $params['situacion'] = $situacion;
            $params['usuarios'] = $usuarios;
        } elseif ($tipo === 'usuario' && $idSeleccionado) {
            $situacion = $m->obtenerSituacionFinanciera($idSeleccionado);
            error_log("Situación Financiera Usuario: " . print_r($situacion, true));

            $usuario = $m->obtenerUsuarioPorId($idSeleccionado);
            $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idSeleccionado);
            $usuario['totalGastos'] = $m->obtenerTotalGastos($idSeleccionado);
            $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
            $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idSeleccionado);
            $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idSeleccionado);
            $params['usuarios'] = [$usuario];
            $params['situacion'] = $situacion;
        }

        $params['familias'] = $m->obtenerFamilias();
        $params['grupos'] = $m->obtenerGrupos();
        $params['usuariosLista'] = $m->obtenerUsuarios();  // Lista de usuarios para el dropdown
    }

    $params['idSeleccionado'] = $idSeleccionado;
    $this->render('verSituacion.php', $params);
}





    // Formulario para insertar gasto
    public function formInsertarGasto()
    {
        $m = new GastosModelo();
        $params = array(
            'categorias' => $m->obtenerCategoriasGastos(),
            'mensaje' => ''
        );
        $this->render('formInsertarGasto.php', $params);
    }

    // Insertar Gasto
    public function insertarGasto()
    {
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
    public function formInsertarIngreso()
    {
        $m = new GastosModelo();
        $params = array(
            'categorias' => $m->obtenerCategoriasIngresos(),
            'mensaje' => ''
        );
        $this->render('formInsertarIngreso.php', $params);
    }

    // Insertar Ingreso
    public function insertarIngreso()
    {
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
    // Ver categorías de gastos
    public function verCategoriasGastos()
{
    $m = new GastosModelo();
    $categorias = $m->obtenerCategoriasGastos();

    $params = array(
        'categorias' => $categorias,
        'mensaje' => 'Gestión de categorías de gastos'
    );

    $this->render('verCategoriasGastos.php', $params);
}


public function insertarCategoriaGasto()
{
    if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado.');
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bInsertarCategoriaGasto'])) {
        $nombreCategoria = recoge('nombreCategoria');
        $m = new GastosModelo();

        $nivelUsuario = $_SESSION['nivel_usuario']; // Nivel del usuario que crea la categoría

        if (empty($nombreCategoria)) {
            $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
        } elseif ($m->insertarCategoriaGasto($nombreCategoria, $nivelUsuario)) {
            header('Location: index.php?ctl=verCategoriasGastos');
            exit();
        } else {
            $params['mensaje'] = 'No se pudo insertar la categoría de gasto.';
        }
    }

    $this->verCategoriasGastos();
}




public function actualizarCategoriaGasto()
{
    if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado.');
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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


public function eliminarCategoriaGasto()
{
    if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar categorías.');
        return;
    }

    if (isset($_GET['id'])) {
        $m = new GastosModelo();

        // Verificar si la categoría está en uso antes de eliminarla
        if ($m->categoriaEnUso($_GET['id'], 'gastos')) {
            $params['mensaje'] = 'No se puede eliminar la categoría porque está en uso.';
            $this->verCategoriasGastos();
            return;
        }

        if ($m->eliminarCategoriaGasto($_GET['id'])) {
            header('Location: index.php?ctl=verCategoriasGastos');
            exit();
        } else {
            $params['mensaje'] = 'No se pudo eliminar la categoría de gasto.';
        }
    }
}



    // Gestión de Categorías de Ingresos
    public function verCategoriasIngresos()
{
    $m = new GastosModelo();
    $categorias = $m->obtenerCategoriasIngresos();

    $params = array(
        'categorias' => $categorias,
        'mensaje' => 'Gestión de categorías de ingresos'
    );

    $this->render('verCategoriasIngresos.php', $params);
}


    public function insertarCategoriaIngreso()
    {
        if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo administradores pueden insertar categorías de ingresos.');
            return;
        }

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

    public function editarCategoriaIngreso()
{
    $m = new GastosModelo();

    // Verificar que el usuario tiene los permisos necesarios
    if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo administradores pueden editar categorías de ingresos.');
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarCategoriaIngreso'])) {
        $idCategoria = recoge('idCategoria');
        $nombreCategoria = recoge('nombreCategoria');

        // Verificar que el nombre de la categoría no está vacío
        if (empty($nombreCategoria)) {
            $params['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
        } elseif ($m->actualizarCategoriaIngreso($idCategoria, $nombreCategoria)) {
            header('Location: index.php?ctl=verCategoriasIngresos');
            exit();
        } else {
            $params['mensaje'] = 'No se pudo actualizar la categoría de ingreso.';
        }
    } else {
        if (isset($_GET['id'])) {
            $categoria = $m->obtenerCategoriaIngresoPorId($_GET['id']);
            if (!$categoria) {
                $this->redireccionarError('Categoría no encontrada.');
                return;
            }
            $params['categoria'] = $categoria;
            $this->render('formEditarCategoriaIngreso.php', $params);
        } else {
            $this->redireccionarError('Categoría no válida.');
        }
    }
}

public function eliminarCategoriaIngreso()
{
    $m = new GastosModelo();

    // Verificar que el usuario tiene los permisos necesarios
    if ($_SESSION['nivel_usuario'] !== 'admin' && $_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar categorías de ingresos.');
        return;
    }

    if (isset($_GET['id'])) {
        // Verificar si la categoría está en uso antes de eliminarla
        if ($m->categoriaIngresoEnUso($_GET['id'])) {
            $this->redireccionarError('No se puede eliminar la categoría porque está en uso.');
            return;
        }

        if ($m->eliminarCategoriaIngreso($_GET['id'])) {
            header('Location: index.php?ctl=verCategoriasIngresos');
            exit();
        } else {
            $params['mensaje'] = 'No se pudo eliminar la categoría de ingreso.';
        }
    } else {
        $this->redireccionarError('Categoría no válida.');
    }
}


    // Gestión de Grupos
    public function verGrupos()
    {
        $m = new GastosModelo();
        $grupos = $m->obtenerGrupos();

        $params = array(
            'grupos' => $grupos,
            'mensaje' => 'Gestión de grupos'
        );

        $this->render('verGrupos.php', $params);
    }
    // Formulario para crear un nuevo grupo
    public function formCrearGrupo()
    {
        // Solo superadmin tiene acceso
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            header('Location: index.php?ctl=inicio');
            exit();
        }

        $this->render('formCrearGrupo.php');
    }

    // Crear un nuevo grupo
    public function crearGrupo()
    {
        // Verificar si el usuario es superadmin
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo superadmin puede crear grupos.');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearGrupo'])) {
            $nombre_grupo = recoge('nombre_grupo');
            $password_grupo = recoge('password_grupo');

            $errores = array();
            cTexto($nombre_grupo, "nombre_grupo", $errores);
            cContrasenya($password_grupo, $errores);

            if (empty($errores)) {
                $m = new GastosModelo();
                if ($m->insertarGrupo($nombre_grupo, $password_grupo)) {
                    header('Location: index.php?ctl=listarGrupos');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo crear el grupo.';
                }
            } else {
                $params['errores'] = $errores;
            }

            $this->render('formCrearGrupo.php', $params);
        }
    }
    public function eliminarGrupo()
{
    // Verificar si el usuario es superadmin
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo superadmin puede eliminar grupos.');
        return;
    }

    $idGrupo = recoge('id'); // Obtener el ID del grupo
    $m = new GastosModelo();
    
    // Validar si hay usuarios asociados a este grupo
    $usuariosAsociados = $m->obtenerUsuariosPorGrupo($idGrupo);
    if (!empty($usuariosAsociados)) {
        $this->redireccionarError('No se puede eliminar el grupo. Hay usuarios asociados.');
        return;
    }
    
    // Validar si hay ingresos o gastos asociados a los usuarios del grupo
    $ingresosAsociados = $m->obtenerIngresosPorGrupo($idGrupo);
    $gastosAsociados = $m->obtenerGastosPorGrupo($idGrupo);
    if (!empty($ingresosAsociados) || !empty($gastosAsociados)) {
        $this->redireccionarError('No se puede eliminar el grupo. Hay ingresos o gastos asociados.');
        return;
    }

    // Si no hay usuarios, ingresos o gastos asociados, eliminar el grupo
    if ($m->eliminarGrupo($idGrupo)) {
        header('Location: index.php?ctl=listarGrupos');
        exit();
    } else {
        $this->redireccionarError('Error al eliminar el grupo.');
    }
}
public function listarGrupos()
{
    $m = new GastosModelo();
    
    // Obtener todos los grupos
    $grupos = $m->obtenerGrupos();
    
    // Parámetros a enviar a la vista
    $params = array(
        'grupos' => $grupos
    );
    
    // Renderizar la vista listarGrupos.php
    $this->render('listarGrupos.php', $params);
}


// Editar Grupo
public function editarGrupo()
{
    $m = new GastosModelo();

    // Obtener el ID del grupo
    if (isset($_GET['id'])) {
        $grupo = $m->obtenerGrupoPorId($_GET['id']);
        if (!$grupo) {
            $params['mensaje'] = 'Grupo no encontrado.';
            $this->listarGrupos();
            return;
        }
    }

    // Comprobar si el usuario es superadmin o administrador del grupo
    $esAdmin = false;

    // Si el usuario no es superadmin, comprobar si es administrador del grupo
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $administradores = $m->obtenerAdministradoresGrupo($grupo['idGrupo']);
        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                $esAdmin = true;
                break;
            }
        }

        if (!$esAdmin) {
            $this->redireccionarError('No tienes permiso para editar este grupo.');
            return;
        }
    }

    $params = array(
        'nombre_grupo' => $grupo['nombre_grupo'],
        'idGrupo' => $grupo['idGrupo']
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarGrupo'])) {
        $nombre_grupo = recoge('nombre_grupo');
        $errores = array();

        cTexto($nombre_grupo, "nombre_grupo", $errores);

        if (empty($errores)) {
            if ($m->actualizarGrupo($grupo['idGrupo'], $nombre_grupo)) {
                header('Location: index.php?ctl=listarGrupos');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo actualizar el grupo.';
            }
        } else {
            $params['errores'] = $errores;
        }
    }

    $this->render('formEditarGrupo.php', $params);
}




    // Formulario para asignar un usuario a una familia o grupo
public function formAsignarUsuario()
{
    // Permitir acceso a admin y superadmin
    if ($_SESSION['nivel_usuario'] !== 'superadmin' && $_SESSION['nivel_usuario'] !== 'admin') {
        header('Location: index.php?ctl=inicio');
        exit();
    }

    // Instanciar el modelo
    $m = new GastosModelo();

    // Obtener datos de usuarios, familias y grupos
    $usuarios = $m->obtenerUsuarios();
    $familias = $m->obtenerFamilias();
    $grupos = $m->obtenerGrupos();

    // Definir los parámetros que se enviarán a la vista
    $params = array(
        'usuarios' => $usuarios,
        'familias' => $familias,
        'grupos' => $grupos
    );

    // Renderizar la vista 'formAsignarUsuario.php' con los datos obtenidos
    $this->render('formAsignarUsuario.php', $params);
}

    // Asignar usuario a familia o grupo
    public function asignarUsuario()
    {
        // Verificar si el usuario es superadmin
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            $this->redireccionarError('Acceso denegado. Solo superadmin puede asignar usuarios a familias o grupos.');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bAsignarUsuario'])) {
            $idUsuario = recoge('idUsuario');
            $idFamilia = recoge('idFamilia');
            $idGrupo = recoge('idGrupo');

            $m = new GastosModelo();

            if ($m->actualizarUsuarioFamiliaGrupo($idUsuario, $idFamilia, $idGrupo)) {
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo asignar el usuario a la familia o grupo.';
            }

            $this->render('formAsignarUsuario.php', $params);
        }
    }
    public function formCrearUsuario()
    {
        $m = new GastosModelo();

        // Obtener familias y grupos para asignar al nuevo usuario
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();

        $params = array(
            'familias' => $familias,
            'grupos' => $grupos,
            'mensaje' => ''
        );

        $this->render('formCrearUsuario.php', $params);
    }

    public function crearUsuario()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $m = new GastosModelo();

        // Recoger los datos del formulario
        $nombre = recoge('nombre');
        $apellido = recoge('apellido');
        $alias = recoge('alias');
        $email = recoge('email');
        $contrasenya = recoge('contrasenya');
        $nivel_usuario = recoge('nivel_usuario');
        $fecha_nacimiento = recoge('fecha_nacimiento');
        $telefono = recoge('telefono');
        $idFamilia = recoge('idFamilia') ?: null; // Asignar null si no se selecciona una familia
        $idGrupo = recoge('idGrupo') ?: null; // Asignar null si no se selecciona un grupo

        $errores = [];

        // Validar datos
        cTexto($nombre, "nombre", $errores);
        cTexto($apellido, "apellido", $errores);
        cTexto($alias, "alias", $errores);
        cEmail($email, $errores);
        cContrasenya($contrasenya, $errores);
        cTelefono($telefono, $errores); // Validar teléfono si es necesario

        // Si hay errores, mostrar el formulario con los mensajes de error
        if (!empty($errores)) {
            $params = array(
                'familias' => $m->obtenerFamilias(),
                'grupos' => $m->obtenerGrupos(),
                'mensaje' => 'Por favor corrige los errores:',
                'errores' => $errores,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'alias' => $alias,
                'email' => $email,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fecha_nacimiento,
                'idFamilia' => $idFamilia,
                'idGrupo' => $idGrupo
            );
            $this->render('formCrearUsuario.php', $params);
            return;
        }

        // Si no hay errores, proceder a la creación
        $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);

        // Insertar usuario en la base de datos
        if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $idFamilia, $idGrupo)) {
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        } else {
            // Si falla la inserción, volver a mostrar el formulario con un mensaje de error
            $params = array(
                'mensaje' => 'No se pudo insertar el usuario. Inténtalo de nuevo.',
                'familias' => $m->obtenerFamilias(),
                'grupos' => $m->obtenerGrupos(),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'alias' => $alias,
                'email' => $email,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fecha_nacimiento,
                'idFamilia' => $idFamilia,
                'idGrupo' => $idGrupo
            );
            $this->render('formCrearUsuario.php', $params);
        }
    }
}

    // Listar Usuarios
    public function listarUsuarios()
    {
        $m = new GastosModelo();
        $usuarios = $m->obtenerUsuarios();

        $params = array(
            'usuarios' => $usuarios,
            'mensaje' => 'Lista de usuarios registrados'
        );

        $this->render('listarUsuarios.php', $params);
    }

    // Eliminar Usuario
    public function eliminarUsuario()
{
    // Verificar si el usuario es superadmin o admin
    if ($_SESSION['nivel_usuario'] !== 'superadmin' && $_SESSION['nivel_usuario'] !== 'admin') {
        $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar usuarios.');
        return;
    }

    $idUsuario = recoge('id'); // Obtener el ID del usuario
    $m = new GastosModelo();
    
    // Verificar si el usuario existe
    $usuario = $m->obtenerUsuarioPorId($idUsuario);
    if (!$usuario) {
        $this->redireccionarError('Usuario no encontrado.');
        return;
    }

    // Eliminar los gastos asociados al usuario
    $gastosEliminados = $m->eliminarGastosPorUsuario($idUsuario);
    // Eliminar los ingresos asociados al usuario
    $ingresosEliminados = $m->eliminarIngresosPorUsuario($idUsuario);

    // Si se eliminaron correctamente los gastos e ingresos, eliminar el usuario
    if ($gastosEliminados && $ingresosEliminados && $m->eliminarUsuario($idUsuario)) {
        header('Location: index.php?ctl=listarUsuarios');
        exit();
    } else {
        $this->redireccionarError('Error al eliminar el usuario o sus registros asociados.');
    }
}
    // Editar Usuario
    public function editarUsuario()
{
    $m = new GastosModelo();

    if (isset($_GET['id'])) {
        $usuario = $m->obtenerUsuarioPorId($_GET['id']);
        if (!$usuario) {
            $params['mensaje'] = 'Usuario no encontrado.';
            $this->listarUsuarios();
            return;
        }
    }

    $familias = $m->obtenerFamilias();
    $grupos = $m->obtenerGrupos();

    $params = array(
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'alias' => $usuario['alias'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'],
        'idUser' => $usuario['idUser'],
        'nivel_usuario' => $usuario['nivel_usuario'],
        'idFamilia' => $usuario['idFamilia'],
        'idGrupo' => $usuario['idGrupo'],
        'familias' => $familias,
        'grupos' => $grupos
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bEditarUsuario'])) {
        $nombre = recoge('nombre');
        $apellido = recoge('apellido');
        $alias = recoge('alias');
        $email = recoge('email');
        $telefono = recoge('telefono');
        $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
        $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
        $nivel_usuario = $_SESSION['nivel_usuario'] === 'superadmin' ? recoge('nivel_usuario') : $usuario['nivel_usuario'];

        $errores = array();

        cTexto($nombre, "nombre", $errores);
        cTexto($apellido, "apellido", $errores);
        cUser($alias, "alias", $errores);
        cEmail($email, $errores);
        cTelefono($telefono, $errores);

        // Validar que la familia y el grupo existen
        if ($idFamilia && !$m->obtenerFamiliaPorId($idFamilia)) {
            $errores['familia'] = 'La familia seleccionada no existe.';
        }

        if ($idGrupo && !$m->obtenerGrupoPorId($idGrupo)) {
            $errores['grupo'] = 'El grupo seleccionado no existe.';
        }

        if (empty($errores)) {
            if ($m->actualizarUsuario($usuario['idUser'], $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $idFamilia, $idGrupo)) {
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

// Eliminar administrador de una familia
public function eliminarAdministradorDeFamilia()
{
    $idAdmin = recoge('idAdmin'); // ID del administrador
    $idFamilia = recoge('idFamilia'); // ID de la familia
    $m = new GastosModelo();

    // Verificar si el usuario es superadmin o si es un administrador válido
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $administradores = $m->obtenerAdministradoresFamilia($idFamilia);
        $esAdmin = false;

        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                $esAdmin = true;
                break;
            }
        }

        if (!$esAdmin) {
            $this->redireccionarError('No tienes permiso para eliminar administradores de esta familia.');
            return;
        }
    }

    // Proceder a eliminar el administrador
    if ($m->eliminarAdministradorDeFamilia($idAdmin, $idFamilia)) {
        header('Location: index.php?ctl=verFamilia&id=' . $idFamilia);
        exit();
    } else {
        $this->redireccionarError('Error al eliminar el administrador de la familia.');
    }
}
// Eliminar administrador de un grupo
public function eliminarAdministradorDeGrupo()
{
    $idAdmin = recoge('idAdmin'); // ID del administrador
    $idGrupo = recoge('idGrupo'); // ID del grupo
    $m = new GastosModelo();

    // Verificar si el usuario es superadmin o si es un administrador válido
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $administradores = $m->obtenerAdministradoresGrupo($idGrupo);
        $esAdmin = false;

        foreach ($administradores as $admin) {
            if ($admin['idUser'] === $_SESSION['usuario']['id']) {
                $esAdmin = true;
                break;
            }
        }

        if (!$esAdmin) {
            $this->redireccionarError('No tienes permiso para eliminar administradores de este grupo.');
            return;
        }
    }

    // Proceder a eliminar el administrador
    if ($m->eliminarAdministradorDeGrupo($idAdmin, $idGrupo)) {
        header('Location: index.php?ctl=verGrupo&id=' . $idGrupo);
        exit();
    } else {
        $this->redireccionarError('Error al eliminar el administrador del grupo.');
    }
}

// Asignar administrador a una familia
public function asignarAdministradorAFamilia()
{
    $idFamilia = recoge('idFamilia');
    $idAdmin = recoge('idAdmin'); // ID del usuario que se convertirá en administrador
    $m = new GastosModelo();

    // Verificar si el usuario es superadmin
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo superadmin puede asignar administradores.');
        return;
    }

    // Verificar si el usuario ya es administrador de la familia
    $administradores = $m->obtenerAdministradoresFamilia($idFamilia);
    foreach ($administradores as $admin) {
        if ($admin['idUser'] === $idAdmin) {
            $this->redireccionarError('El usuario ya es administrador de esta familia.');
            return;
        }
    }

    // Asignar al usuario como administrador de la familia
    if ($m->añadirAdministradorAFamilia($idAdmin, $idFamilia)) {
        header('Location: index.php?ctl=verFamilia&id=' . $idFamilia);
        exit();
    } else {
        $this->redireccionarError('Error al asignar el administrador a la familia.');
    }
}

// Asignar administrador a un grupo
public function asignarAdministradorAGrupo()
{
    $idGrupo = recoge('idGrupo');
    $idAdmin = recoge('idAdmin'); // ID del usuario que se convertirá en administrador
    $m = new GastosModelo();

    // Verificar si el usuario es superadmin
    if ($_SESSION['nivel_usuario'] !== 'superadmin') {
        $this->redireccionarError('Acceso denegado. Solo superadmin puede asignar administradores.');
        return;
    }

    // Verificar si el usuario ya es administrador del grupo
    $administradores = $m->obtenerAdministradoresGrupo($idGrupo);
    foreach ($administradores as $admin) {
        if ($admin['idUser'] === $idAdmin) {
            $this->redireccionarError('El usuario ya es administrador de este grupo.');
            return;
        }
    }

    // Asignar al usuario como administrador del grupo
    if ($m->añadirAdministradorAGrupo($idAdmin, $idGrupo)) {
        header('Location: index.php?ctl=verGrupo&id=' . $idGrupo);
        exit();
    } else {
        $this->redireccionarError('Error al asignar el administrador al grupo.');
    }
}
// Asignar usuarios normales a familias o grupos
public function asignarUsuarioFamiliaGrupo()
{
    // Registro de depuración al entrar en el método
    error_log("DEBUG: Entrando en asignarUsuarioFamiliaGrupo");
    
    // Instanciamos el modelo para realizar las operaciones
    $m = new GastosModelo();
    
    // Recogemos los datos del formulario
    $idUsuario = recoge('idUsuario');
    $tipoVinculo = recoge('tipoVinculo');
    $passwordGrupoFamilia = recoge('passwordGrupoFamilia');
    
    // Registro de depuración de los valores recogidos
    error_log("DEBUG: ID Usuario -> $idUsuario, Tipo de Vínculo -> $tipoVinculo");

    // Verificamos si el tipo de vínculo es 'familia' o 'grupo' y gestionamos en consecuencia
    if ($tipoVinculo === 'familia') {
        // Recogemos el ID de la familia
        $idFamilia = recoge('idFamilia');
        error_log("DEBUG: ID Familia -> $idFamilia");

        // Verificamos la contraseña de la familia usando el método del modelo
        if (!$m->verificarPasswordFamilia($idFamilia, $passwordGrupoFamilia)) {
            // Registro en caso de error con la contraseña
            error_log("ERROR: Contraseña incorrecta para la familia $idFamilia");
            $this->redireccionarError('La contraseña de la familia es incorrecta.');
            return;
        }

        // Intentamos asignar el usuario a la familia
        if ($m->asignarUsuarioAFamilia($idUsuario, $idFamilia)) {
            // Redirigimos a la vista de familias en caso de éxito
            header('Location: index.php?ctl=listarFamilias');
            exit();
        } else {
            // Registro en caso de error al asignar usuario
            error_log("ERROR: No se pudo asignar el usuario a la familia.");
            $this->redireccionarError('Error al asignar el usuario a la familia.');
        }
    } elseif ($tipoVinculo === 'grupo') {
        // Recogemos el ID del grupo
        $idGrupo = recoge('idGrupo');
        error_log("DEBUG: ID Grupo -> $idGrupo");

        // Verificamos la contraseña del grupo usando el método del modelo
        if (!$m->verificarPasswordGrupo($idGrupo, $passwordGrupoFamilia)) {
            // Registro en caso de error con la contraseña
            error_log("ERROR: Contraseña incorrecta para el grupo $idGrupo");
            $this->redireccionarError('La contraseña del grupo es incorrecta.');
            return;
        }

        // Intentamos asignar el usuario al grupo
        if ($m->asignarUsuarioAGrupo($idUsuario, $idGrupo)) {
            // Redirigimos a la vista de grupos en caso de éxito
            header('Location: index.php?ctl=verGrupos');
            exit();
        } else {
            // Registro en caso de error al asignar usuario
            error_log("ERROR: No se pudo asignar el usuario al grupo.");
            $this->redireccionarError('Error al asignar el usuario al grupo.');
        }
    } else {
        // Registro en caso de tipo de vínculo no válido
        error_log("ERROR: Tipo de vínculo no válido -> $tipoVinculo");
        $this->redireccionarError('Tipo de vínculo no válido.');
    }
}
    public function dashboard()
    {
        $m = new GastosModelo();

        // Obtener el total de ingresos y gastos
        $totalIngresos = $m->obtenerTotalIngresos($_SESSION['usuario']['id']);
        $totalGastos = $m->obtenerTotalGastos($_SESSION['usuario']['id']);

        // Obtener la distribución de gastos por categoría
        $gastosPorCategoria = $m->obtenerGastosPorCategoria($_SESSION['usuario']['id']);

        // Obtener la distribución de ingresos por categoría
        $ingresosPorCategoria = $m->obtenerIngresosPorCategoria($_SESSION['usuario']['id']);

        // Pasar los datos a la vista
        $params = array(
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'gastosPorCategoria' => $gastosPorCategoria,
            'ingresosPorCategoria' => $ingresosPorCategoria
        );

        $this->render('dashboard.php', $params);
    }
}
