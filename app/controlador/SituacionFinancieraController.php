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
    private function verSituacionUsuario($m, $idUser, &$params)
    {
        try {
            $usuario = $this->calcularSituacionUsuario($m, $idUser);
            $params['usuarios'] = [$usuario];
            $params['situacion'] = $m->obtenerSituacionFinanciera($idUser);
        } catch (Exception $e) {
            error_log("Error en verSituacionUsuario(): " . $e->getMessage());
            $this->redireccionarError('Error al obtener la situación financiera del usuario.');
        }
    }

    // Función para ver la situación financiera de un administrador
    private function verSituacionAdmin($m, $tipo, $idSeleccionado, &$params)
    {
        try {
            $idAdmin = $_SESSION['usuario']['id'];  // Obtener el ID del administrador
            $params['situacion'] = $m->obtenerSituacionFinancieraPorAdmin($idAdmin);  // Calcular la situación financiera del admin

            // Si el administrador tiene familias o grupos asignados, se muestran también
            $familiasAsignadas = $m->obtenerFamiliasPorAdministrador($idAdmin);
            $gruposAsignados = $m->obtenerGruposPorAdministrador($idAdmin);
            $params['familias'] = $familiasAsignadas;
            $params['grupos'] = $gruposAsignados;

            // Seleccionar la vista correspondiente
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

            // Situación financiera global o específica
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
    private function calcularSituacionUsuario($m, $idUser)
    {
        try {
            $usuario = $m->obtenerUsuarioPorId($idUser);
            $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idUser) ?? 0;
            $usuario['totalGastos'] = $m->obtenerTotalGastos($idUser) ?? 0;
            $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];

            // Mensajes de depuración para comprobar los valores
            error_log("Usuario: " . $idUser);
            error_log("Total Ingresos: " . $usuario['totalIngresos']);
            error_log("Total Gastos: " . $usuario['totalGastos']);
            error_log("Saldo: " . $usuario['saldo']);

            $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idUser);
            $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idUser);
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
