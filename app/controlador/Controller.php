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
    private function render($vista, $params = array())
    {
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

    // Crear una nueva familia
    public function crearFamilia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bCrearFamilia'])) {
            $nombre_familia = recoge('nombre_familia');
            $password_familia = recoge('password_familia');

            $errores = array();
            cTexto($nombre_familia, "nombre_familia", $errores);
            cContrasenya($password_familia, $errores);

            if (empty($errores)) {
                $m = new GastosModelo();
                if ($m->insertarFamilia($nombre_familia, $password_familia)) {
                    header('Location: index.php?ctl=listarFamilias');
                    exit();
                } else {
                    $params['mensaje'] = 'No se pudo crear la familia.';
                }
            } else {
                $params['errores'] = $errores;
            }

            $this->render('formCrearFamilia.php', $params);
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
        if (isset($_GET['id'])) {
            $familia = $m->obtenerFamiliaPorId($_GET['id']);
            if (!$familia) {
                $params['mensaje'] = 'Familia no encontrada.';
                $this->listarFamilias();
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
        if (isset($_GET['id'])) {
            $m = new GastosModelo();
            if ($m->eliminarFamilia($_GET['id'])) {
                header('Location: index.php?ctl=listarFamilias');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo eliminar la familia.';
            }
        }
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

        // Verificar si es un superadmin para permitir seleccionar filtros
        if ($_SESSION['nivel_usuario'] === 'superadmin') {
            $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos'; // Filtro por defecto "todos"
            $idSeleccionado = isset($_GET['idSeleccionado']) ? $_GET['idSeleccionado'] : null;

            // Obtener gastos según el tipo de filtro
            if ($tipo === 'todos') {
                $gastos = $m->obtenerTodosGastos();
            } elseif ($tipo === 'familia') {
                $gastos = $m->obtenerGastosPorFamilia($idSeleccionado);
            } elseif ($tipo === 'grupo') {
                $gastos = $m->obtenerGastosPorGrupo($idSeleccionado);
            } elseif ($tipo === 'usuario') {
                $gastos = $m->obtenerGastosPorUsuario($idSeleccionado);
            }

            // Obtener familias, grupos y usuarios para el selector
            $familias = $m->obtenerFamilias();
            $grupos = $m->obtenerGrupos();
            $usuarios = $m->obtenerUsuarios();
        } else {
            // Obtener gastos del usuario actual o admin del grupo/familia
            $gastos = $m->obtenerGastosPorUsuario($_SESSION['usuario']['id']);
        }

        $params = array(
            'gastos' => $gastos,
            'familias' => $familias ?? null,
            'grupos' => $grupos ?? null,
            'usuarios' => $usuarios ?? null,
            'tipo' => $tipo ?? 'todos',
            'idSeleccionado' => $idSeleccionado ?? null
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

        $params = array(
            'ingresos' => $ingresos,
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






    // Ver Ingresos de un Usuario Específico (para superadmin)
    public function verIngresosUsuario()
    {
        $idUsuario = recoge('id'); // Obtener el ID del usuario
        $m = new GastosModelo();

        // Asegurarse de que el usuario no es un superusuario
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        if ($usuario['nivel_usuario'] === 'superadmin') {
            header('Location: index.php?ctl=error'); // Redirigir si es superadmin
            exit();
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

    if ($tipo === 'global') {
        // Obtener situación global
        $situacion = $m->obtenerSituacionGlobal();
        $params['situacion'] = $situacion;

    } elseif ($tipo === 'familia' && $idSeleccionado) {
        // Obtener la situación financiera de una familia específica
        $situacion = $m->obtenerSituacionFinancieraFamilia($idSeleccionado);
        $params['situacion'] = $situacion;

        // Obtener los usuarios pertenecientes a la familia y sus totales
        $usuarios = $m->obtenerUsuariosPorFamilia($idSeleccionado);
        foreach ($usuarios as &$usuario) {
            $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
            $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
            $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];

            // Obtener detalles de ingresos y gastos del usuario
            $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
            $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
        }
        $params['usuarios'] = $usuarios;

    } elseif ($tipo === 'grupo' && $idSeleccionado) {
        // Obtener la situación financiera de un grupo específico
        $situacion = $m->obtenerSituacionFinancieraGrupo($idSeleccionado);
        $params['situacion'] = $situacion;

        // Obtener los usuarios pertenecientes al grupo y sus totales
        $usuarios = $m->obtenerUsuariosPorGrupo($idSeleccionado);
        foreach ($usuarios as &$usuario) {
            $usuario['totalIngresos'] = $m->obtenerTotalIngresos($usuario['idUser']);
            $usuario['totalGastos'] = $m->obtenerTotalGastos($usuario['idUser']);
            $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];

            // Obtener detalles de ingresos y gastos del usuario
            $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($usuario['idUser']);
            $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($usuario['idUser']);
        }
        $params['usuarios'] = $usuarios;

    } elseif ($tipo === 'usuario' && $idSeleccionado) {
        // Obtener la situación financiera de un usuario específico
        $situacion = $m->obtenerSituacionFinanciera($idSeleccionado);
        $params['situacion'] = $situacion;

        // Obtener los detalles del usuario seleccionado
        $usuario = $m->obtenerUsuarioPorId($idSeleccionado);
        $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idSeleccionado);
        $usuario['totalGastos'] = $m->obtenerTotalGastos($idSeleccionado);
        $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];

        // Agregar detalles de ingresos y gastos al usuario
        $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idSeleccionado);
        $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idSeleccionado);

        // Pasar el usuario con los detalles al parámetro de la vista
        $params['usuarios'] = [$usuario];
    }

    // Cargar listas para el dropdown de familias, grupos y usuarios
    if ($tipo === 'familia') {
        $params['familias'] = $m->obtenerFamilias();
    } elseif ($tipo === 'grupo') {
        $params['grupos'] = $m->obtenerGrupos();
    } elseif ($tipo === 'usuario') {
        $params['usuariosLista'] = $m->obtenerUsuarios();  // Lista de usuarios para el dropdown
    }

    $params['idSeleccionado'] = $idSeleccionado;
    $this->render('verSituacion.php', $params);
}

    // Ver Situación Financiera con filtro
    public function verSituacionFiltrada()
    {
        $filtro = recoge('filtro');
        $m = new GastosModelo();

        if ($filtro === 'todas') {
            $situacion = $m->obtenerSituacionGlobal();
        } elseif (strpos($filtro, 'familia_') === 0) {
            $idFamilia = substr($filtro, 8);
            $situacion = $m->obtenerSituacionFinancieraFamilia($idFamilia);
        } elseif (strpos($filtro, 'grupo_') === 0) {
            $idGrupo = substr($filtro, 6);
            $situacion = $m->obtenerSituacionFinancieraGrupo($idGrupo);
        } elseif (strpos($filtro, 'usuario_') === 0) {
            $idUsuario = substr($filtro, 8);
            $situacion = $m->obtenerSituacionFinanciera($idUsuario);
        }

        $params = array(
            'situacion' => $situacion,
            'mensaje' => 'Situación financiera filtrada'
        );

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

    public function editarCategoriaGasto()
    {
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

    public function eliminarCategoriaGasto()
    {
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
    // Obtener el desglose de ingresos y gastos por familia o grupo
    public function verDesglose()
    {
        $tipo = recoge('tipo');
        $id = recoge('id');
        $m = new GastosModelo();

        if ($tipo === 'familia') {
            $gastos = $m->obtenerGastosPorFamilia($id);
            $ingresos = $m->obtenerIngresosPorFamilia($id);
        } elseif ($tipo === 'grupo') {
            $gastos = $m->obtenerGastosPorGrupo($id);
            $ingresos = $m->obtenerIngresosPorGrupo($id);
        } elseif ($tipo === 'usuario') {
            $gastos = $m->obtenerGastosPorUsuario($id);
            $ingresos = $m->obtenerIngresosPorUsuario($id);
        }

        $params = array(
            'gastos' => $gastos,
            'ingresos' => $ingresos
        );

        $this->render('verDesglose.php', $params);
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

    public function eliminarCategoriaIngreso()
    {
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

    // Formulario para asignar un usuario a una familia o grupo
    public function formAsignarUsuario()
    {
        if ($_SESSION['nivel_usuario'] !== 'superadmin') {
            header('Location: index.php?ctl=inicio');
            exit();
        }

        $m = new GastosModelo();
        $usuarios = $m->obtenerUsuarios();
        $familias = $m->obtenerFamilias();
        $grupos = $m->obtenerGrupos();

        $params = array(
            'usuarios' => $usuarios,
            'familias' => $familias,
            'grupos' => $grupos
        );

        $this->render('formAsignarUsuario.php', $params);
    }

    // Asignar usuario a familia o grupo
    public function asignarUsuario()
    {
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
            $email = recoge('email'); // Asegurarse de que recoge el email
            $contrasenya = password_hash(recoge('contrasenya'), PASSWORD_DEFAULT);
            $nivel_usuario = recoge('nivel_usuario');
            $idFamilia = recoge('idFamilia') ?: null;
            $idGrupo = recoge('idGrupo') ?: null;

            // Insertar usuario en la base de datos
            if ($m->insertarUsuario($nombre, $apellido, $alias, $contrasenya, $nivel_usuario, $email, $idFamilia, $idGrupo)) {
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            } else {
                $params['mensaje'] = 'No se pudo insertar el usuario. Inténtalo de nuevo.';
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
            'nivel_usuario' => $usuario['nivel_usuario'], // Se pasa el nivel de usuario al formulario
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
            $nivel_usuario = $_SESSION['nivel_usuario'] === 'superadmin' ? recoge('nivel_usuario') : $usuario['nivel_usuario']; // Solo superadmin puede cambiar el nivel

            $errores = array();

            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cUser($alias, "alias", $errores);
            cEmail($email, $errores);
            cTelefono($telefono, $errores);

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
}
