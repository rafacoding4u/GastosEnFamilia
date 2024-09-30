<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class SituacionFinancieraController
{
    // Ver la situación financiera de un usuario, grupo o familia
    public function verSituacion()
    {
        try {
            $m = new GastosModelo();
            $params = [];
            $tipo = recoge('tipo') ?? 'global';  // Tipo de vista seleccionada
            $idSeleccionado = recoge('idSeleccionado') ?? null;  // ID seleccionado, si aplica
            $params['tipo'] = $tipo;

            // Comprobar el nivel de usuario y mostrar la situación correspondiente
            if (esUsuarioNormal()) {
                error_log("Usuario normal viendo su situación financiera.");
                $this->verSituacionUsuario($m, $_SESSION['usuario']['id'], $params);
            } elseif (esAdmin()) {
                error_log("Administrador viendo situación financiera.");
                $this->verSituacionAdmin($m, $tipo, $idSeleccionado, $params);
            } elseif (esSuperadmin()) {
                error_log("Superadmin viendo situación financiera.");
                $this->verSituacionSuperadmin($m, $tipo, $idSeleccionado, $params);
            }

            $this->render('verSituacion.php', $params);
        } catch (Exception $e) {
            error_log("Error en verSituacion(): " . $e->getMessage());
            $this->redireccionarError("Error al obtener la situación financiera.");
        }
    }

    // Función para ver la situación financiera de un usuario normal
    private function verSituacionUsuario($m, $idUsuario, &$params)
    {
        try {
            $usuario = $this->calcularSituacionUsuario($m, $idUsuario);
            $params['usuarios'] = [$usuario];
            $params['situacion'] = $m->obtenerSituacionFinanciera($idUsuario);
        } catch (Exception $e) {
            error_log("Error en verSituacionUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera del usuario.');
        }
    }

    // Función para ver la situación financiera de un administrador
    private function verSituacionAdmin($m, $tipo, $idSeleccionado, &$params)
    {
        try {
            $familiasAsignadas = $m->obtenerFamiliasPorAdministrador($_SESSION['usuario']['id']);
            $gruposAsignados = $m->obtenerGruposPorAdministrador($_SESSION['usuario']['id']);
            $params['familias'] = $familiasAsignadas;
            $params['grupos'] = $gruposAsignados;

            if ($tipo === 'familia' && $idSeleccionado) {
                $this->verSituacionFamilia($m, $idSeleccionado, $params);
            } elseif ($tipo === 'grupo' && $idSeleccionado) {
                $this->verSituacionGrupo($m, $idSeleccionado, $params);
            } elseif ($tipo === 'usuario' && $idSeleccionado) {
                $this->verSituacionUsuario($m, $idSeleccionado, $params);
            }
        } catch (Exception $e) {
            error_log("Error en verSituacionAdmin(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera del administrador.');
        }
    }

    // Función para ver la situación financiera de un superadmin
    private function verSituacionSuperadmin($m, $tipo, $idSeleccionado, &$params)
    {
        try {
            $params['familias'] = $m->obtenerFamilias();
            $params['grupos'] = $m->obtenerGrupos();
            $params['usuariosLista'] = $m->obtenerUsuarios();

            if ($tipo === 'global') {
                $params['situacion'] = $m->obtenerSituacionGlobal();
            } elseif ($tipo === 'familia' && $idSeleccionado) {
                $this->verSituacionFamilia($m, $idSeleccionado, $params);
            } elseif ($tipo === 'grupo' && $idSeleccionado) {
                $this->verSituacionGrupo($m, $idSeleccionado, $params);
            } elseif ($tipo === 'usuario' && $idSeleccionado) {
                $this->verSituacionUsuario($m, $idSeleccionado, $params);
            }
        } catch (Exception $e) {
            error_log("Error en verSituacionSuperadmin(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera del superadmin.');
        }
    }

    // Función para ver la situación financiera de una familia
    private function verSituacionFamilia($m, $idFamilia, &$params)
    {
        try {
            $situacion = $m->obtenerSituacionFinancieraFamilia($idFamilia);
            $usuarios = $m->obtenerUsuariosPorFamilia($idFamilia);
            $this->calcularSituacionUsuarios($m, $usuarios);
            $params['usuarios'] = $usuarios;
            $params['situacion'] = $situacion;
        } catch (Exception $e) {
            error_log("Error en verSituacionFamilia(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera de la familia.');
        }
    }

    // Función para ver la situación financiera de un grupo
    private function verSituacionGrupo($m, $idGrupo, &$params)
    {
        try {
            $situacion = $m->obtenerSituacionFinancieraGrupo($idGrupo);
            $usuarios = $m->obtenerUsuariosPorGrupo($idGrupo);
            $this->calcularSituacionUsuarios($m, $usuarios);
            $params['usuarios'] = $usuarios;
            $params['situacion'] = $situacion;
        } catch (Exception $e) {
            error_log("Error en verSituacionGrupo(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera del grupo.');
        }
    }

    // Función para calcular la situación financiera de un usuario
    private function calcularSituacionUsuario($m, $idUsuario)
    {
        try {
            $usuario = $m->obtenerUsuarioPorId($idUsuario);
            $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idUsuario);
            $usuario['totalGastos'] = $m->obtenerTotalGastos($idUsuario);
            $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
            $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idUsuario);
            $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idUsuario);
            return $usuario;
        } catch (Exception $e) {
            error_log("Error en calcularSituacionUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al calcular la situación financiera del usuario.');
        }
    }

    // Función para calcular la situación financiera de varios usuarios (para familias y grupos)
    private function calcularSituacionUsuarios($m, &$usuarios)
    {
        foreach ($usuarios as &$usuario) {
            $usuario = $this->calcularSituacionUsuario($m, $usuario['idUser']);
        }
    }

    // Método para renderizar vistas
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

    // Método para manejar errores de redireccionamiento
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
}
