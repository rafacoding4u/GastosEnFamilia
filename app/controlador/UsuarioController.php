<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class UsuarioController
{
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

            $idFamilia = null;
            $idGrupo = null;

            // Comprobar contraseñas y vincular a grupo o familia existente
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

            // Crear un nuevo grupo o familia
            if (!empty($nombreNuevo) && !empty($passwordNuevo)) {
                $hashedPasswordNuevo = encriptar($passwordNuevo); // Usamos la función encriptar
                if ($_POST['tipo_vinculo'] == 'grupo') {
                    $idGrupo = $m->insertarGrupo($nombreNuevo, $hashedPasswordNuevo);
                } elseif ($_POST['tipo_vinculo'] == 'familia') {
                    $idFamilia = $m->insertarFamilia($nombreNuevo, $hashedPasswordNuevo);
                }
            }

            if (empty($errores)) {
                try {
                    $hashedPassword = encriptar($contrasenya); // Encriptar la contraseña con función unificada
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

    // Editar usuario
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

    // Eliminar usuario
    public function eliminarUsuario()
    {
        if ($_SESSION['nivel_usuario'] !== 'superadmin' && $_SESSION['nivel_usuario'] !== 'admin') {
            $this->redireccionarError('Acceso denegado. Solo administradores pueden eliminar usuarios.');
            return;
        }

        $idUsuario = recoge('id');
        $m = new GastosModelo();
        $usuario = $m->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            $this->redireccionarError('Usuario no encontrado.');
            return;
        }

        if ($m->eliminarGastosPorUsuario($idUsuario) && $m->eliminarIngresosPorUsuario($idUsuario) && $m->eliminarUsuario($idUsuario)) {
            header('Location: index.php?ctl=listarUsuarios');
            exit();
        } else {
            $this->redireccionarError('Error al eliminar el usuario o sus registros asociados.');
        }
    }

    // Listar usuarios
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

    // Función privada para renderizar las vistas
    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    // Función privada para redirigir en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
}
