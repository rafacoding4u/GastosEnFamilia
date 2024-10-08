<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php'; // Asegúrate de incluir correctamente el modelo

class UsuarioController
{
    // Constructor para asegurar que el usuario esté autenticado
    public function __construct()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ctl=iniciarSesion');
            exit();
        }
    }

    // Registro de usuario
    public function registro()
    {
        try {
            if ($_SESSION['nivel_usuario'] !== 'superadmin' && $_SESSION['nivel_usuario'] !== 'admin') {
                throw new Exception('No tienes permisos para crear usuarios.');
            }

            $params = array(
                'nombre' => '', 'apellido' => '', 'alias' => '', 'contrasenya' => '',
                'fecha_nacimiento' => '', 'email' => '', 'telefono' => '',
                'nivel_usuario' => 'usuario', 'idFamilia' => null, 'idGrupo' => null,
                'es_menor' => false
            );
            $errores = array();
            $m = new GastosModelo();

            // Obtener familias y grupos según el nivel del usuario
            if ($_SESSION['nivel_usuario'] === 'superadmin') {
                $familias = $m->obtenerFamilias();
                $grupos = $m->obtenerGrupos();
            } else {
                $familias = $m->obtenerFamiliasPorAdministrador($_SESSION['usuario']['id']);
                $grupos = $m->obtenerGruposPorAdministrador($_SESSION['usuario']['id']);
            }

            if (isset($_POST['bRegistro'])) {
                // Recoger y validar datos del formulario
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $contrasenya = recoge('contrasenya');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $nivel_usuario = recoge('nivel_usuario');
                $idGrupoFamilia = recoge('idGrupoFamilia');
                $passwordFamiliaGrupo = recoge('passwordGrupoFamilia');
                $nombreNuevo = recoge('nombre_nuevo');
                $passwordNuevo = recoge('password_nuevo');

                // Validar campos y errores
                cTexto($nombre, "nombre", $errores);
                cTexto($apellido, "apellido", $errores);
                cUser($alias, "alias", $errores);
                cContrasenya($contrasenya, $errores);
                cEmail($email, $errores);
                cTelefono($telefono, $errores);

                // Asignar a un grupo o familia existente o crear uno nuevo
                $idFamilia = null;
                $idGrupo = null;
                if (!empty($idGrupoFamilia)) {
                    if (strpos($idGrupoFamilia, 'grupo_') === 0) {
                        $idGrupo = substr($idGrupoFamilia, 6);
                        if (!$m->verificarPasswordGrupo($idGrupo, $passwordFamiliaGrupo)) {
                            $errores['idGrupo'] = "Contraseña del grupo incorrecta.";
                        }
                    } elseif (strpos($idGrupoFamilia, 'familia_') === 0) {
                        $idFamilia = substr($idGrupoFamilia, 8);
                        if (!$m->verificarPasswordFamilia($idFamilia, $passwordFamiliaGrupo)) {
                            $errores['idFamilia'] = "Contraseña de la familia incorrecta.";
                        }
                    }
                }

                // Crear nuevo grupo o familia
                if (!empty($nombreNuevo) && !empty($passwordNuevo)) {
                    $hashedPasswordNuevo = encriptar($passwordNuevo);
                    if ($_POST['tipo_vinculo'] == 'grupo') {
                        $idGrupo = $m->insertarGrupo($nombreNuevo, $hashedPasswordNuevo);
                    } elseif ($_POST['tipo_vinculo'] == 'familia') {
                        $idFamilia = $m->insertarFamilia($nombreNuevo, $hashedPasswordNuevo);
                    }
                }

                // Si no hay errores, crear el usuario
                if (empty($errores)) {
                    $hashedPassword = encriptar($contrasenya);
                    if ($m->insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, null, $email, $telefono, $idGrupo, $idFamilia)) {
                        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
                        header('Location: index.php?ctl=listarUsuarios');
                        exit();
                    } else {
                        $params['mensaje'] = 'No se pudo insertar el usuario. Revisa los datos.';
                    }
                } else {
                    $params['errores'] = $errores;
                }
            }

            $params['familias'] = $familias;
            $params['grupos'] = $grupos;
            $this->render('formRegistro.php', $params);

        } catch (Exception $e) {
            error_log("Error en registro(): " . $e->getMessage());
            $this->redireccionarError("Error al registrar usuario.");
        }
    }

    // Editar usuario
    public function editarUsuario()
    {
        try {
            if ($_SESSION['nivel_usuario'] !== 'superadmin' && !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['id']))) {
                throw new Exception('No tienes permisos para editar este usuario.');
            }

            $m = new GastosModelo();
            if (isset($_GET['id'])) {
                $usuario = $m->obtenerUsuarioPorId($_GET['id']);
                if (!$usuario) {
                    throw new Exception('Usuario no encontrado.');
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
                // Recoger y validar datos
                $nombre = recoge('nombre');
                $apellido = recoge('apellido');
                $alias = recoge('alias');
                $email = recoge('email');
                $telefono = recoge('telefono');
                $idFamilia = recoge('idFamilia') ? recoge('idFamilia') : null;
                $idGrupo = recoge('idGrupo') ? recoge('idGrupo') : null;
                $nivel_usuario = ($_SESSION['nivel_usuario'] === 'superadmin') ? recoge('nivel_usuario') : $usuario['nivel_usuario'];

                $errores = array();

                // Validar los campos
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

        } catch (Exception $e) {
            error_log("Error en editarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al editar el usuario.');
        }
    }

    // Eliminar usuario
    public function eliminarUsuario()
    {
        try {
            if ($_SESSION['nivel_usuario'] !== 'superadmin' && !($this->esAdmin() && $this->perteneceAFamiliaOGrupo($_GET['id']))) {
                throw new Exception('No tienes permisos para eliminar este usuario.');
            }

            $idUsuario = recoge('id');
            $m = new GastosModelo();
            $usuario = $m->obtenerUsuarioPorId($idUsuario);

            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            if ($m->eliminarGastosPorUsuario($idUsuario) && $m->eliminarIngresosPorUsuario($idUsuario) && $m->eliminarUsuario($idUsuario)) {
                header('Location: index.php?ctl=listarUsuarios');
                exit();
            } else {
                throw new Exception('Error al eliminar el usuario o sus registros.');
            }

        } catch (Exception $e) {
            error_log("Error en eliminarUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al eliminar el usuario.');
        }
    }

    // Listar usuarios
    public function listarUsuarios()
    {
        try {
            $m = new GastosModelo();
            $usuarios = $m->obtenerUsuarios();

            $params = array(
                'usuarios' => $usuarios,
                'mensaje' => 'Lista de usuarios registrados'
            );

            $this->render('listarUsuarios.php', $params);

        } catch (Exception $e) {
            error_log("Error en listarUsuarios(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los usuarios.');
        }
    }

    // Función privada para renderizar vistas
    private function render($vista, $params = array())
    {
        try {
            extract($params);
            ob_start();
            require __DIR__ . '/../../web/templates/' . $vista;
            $contenido = ob_get_clean();
            require __DIR__ . '/../../web/templates/layout.php';
        } catch (Exception $e) {
            error_log("Error en render(): " . $e->getMessage());
            $this->redireccionarError('Error al renderizar la vista.');
        }
    }

    // Función privada para redirigir en caso de error
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }

    // Métodos auxiliares para validación de permisos
    private function esAdmin()
    {
        return $_SESSION['nivel_usuario'] === 'admin';
    }

    private function perteneceAFamiliaOGrupo($idUsuario)
    {
        $m = new GastosModelo();
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        return ($usuario &&
            ($usuario['idFamilia'] == $_SESSION['usuario']['idFamilia'] ||
             $usuario['idGrupo'] == $_SESSION['usuario']['idGrupo']));
    }

    // Obtener una configuración
    public function obtenerConfiguracion($clave, $idUser = null)
    {
        $m = new GastosModelo();
        return $m->consultarConfiguracion($clave, $idUser);
    }

    // Guardar o actualizar una configuración
    public function guardarConfiguracion($clave, $valor, $idUser = null)
    {
        $m = new GastosModelo();
        return $m->guardarConfiguracion($clave, $valor, $idUser);
    }

    // Archivar accesos antiguos
    public function archivarAccesosAntiguos()
    {
        try {
            $m = new GastosModelo();

            // Archivar accesos anteriores a 1 año
            $m->archivarAccesosAntiguos();

            // Mensaje de confirmación
            echo "Los accesos antiguos han sido archivados correctamente y la tabla ha sido limpiada.";
        } catch (Exception $e) {
            error_log("Error al archivar accesos antiguos: " . $e->getMessage());
            echo "Ocurrió un error al archivar los accesos.";
        }
    }
    public function guardarPreferencias()
    {
        try {
            $m = new GastosModelo();
    
            // Recoger las preferencias enviadas
            $resultadosPorPaginaGastos = recoge('resultados_por_pagina_gastos');
            $resultadosPorPaginaIngresos = recoge('resultados_por_pagina_ingresos');
    
            // Guardar las configuraciones en la base de datos (si están definidas)
            if ($resultadosPorPaginaGastos) {
                $m->guardarConfiguracion('resultados_por_pagina_gastos', $resultadosPorPaginaGastos, $_SESSION['usuario']['id']);
            }
            if ($resultadosPorPaginaIngresos) {
                $m->guardarConfiguracion('resultados_por_pagina_ingresos', $resultadosPorPaginaIngresos, $_SESSION['usuario']['id']);
            }
    
            $_SESSION['mensaje_exito'] = 'Preferencias guardadas correctamente.';
            header('Location: index.php?ctl=verPreferencias');
            exit();
    
        } catch (Exception $e) {
            error_log("Error en guardarPreferencias(): " . $e->getMessage());
            $_SESSION['error_mensaje'] = 'No se pudieron guardar las preferencias. Inténtalo de nuevo.';
            header('Location: index.php?ctl=verPreferencias');
            exit();
        }
    }
    

}
