<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';

class SituacionFinancieraController
{
    // Ver la situación financiera de un usuario, grupo o familia
    public function verSituacion()
    {
        $m = new GastosModelo();
        $params = [];

        // Obtener el tipo seleccionado (global, familia, grupo, usuario)
        $tipo = recoge('tipo') ?? 'global';
        $idSeleccionado = recoge('idSeleccionado') ?? null;
        $params['tipo'] = $tipo;

        // Comprobar el nivel de usuario
        if (esUsuarioNormal()) {
            $this->verSituacionUsuario($m, $_SESSION['usuario']['id'], $params);
        } elseif (esAdmin()) {
            $this->verSituacionAdmin($m, $tipo, $idSeleccionado, $params);
        } elseif (esSuperadmin()) {
            $this->verSituacionSuperadmin($m, $tipo, $idSeleccionado, $params);
        }

        $this->render('verSituacion.php', $params);
    }

    // Función para ver la situación financiera de un usuario normal
    private function verSituacionUsuario($m, $idUsuario, &$params)
    {
        $usuario = $this->calcularSituacionUsuario($m, $idUsuario);
        $params['usuarios'] = [$usuario];
        $params['situacion'] = $m->obtenerSituacionFinanciera($idUsuario);
    }

    // Función para ver la situación financiera de un administrador
    private function verSituacionAdmin($m, $tipo, $idSeleccionado, &$params)
    {
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
    }

    // Función para ver la situación financiera de un superadmin
    private function verSituacionSuperadmin($m, $tipo, $idSeleccionado, &$params)
    {
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
    }

    // Función para ver la situación financiera de una familia
    private function verSituacionFamilia($m, $idFamilia, &$params)
    {
        $situacion = $m->obtenerSituacionFinancieraFamilia($idFamilia);
        $usuarios = $m->obtenerUsuariosPorFamilia($idFamilia);
        $this->calcularSituacionUsuarios($m, $usuarios);
        $params['usuarios'] = $usuarios;
        $params['situacion'] = $situacion;
    }

    // Función para ver la situación financiera de un grupo
    private function verSituacionGrupo($m, $idGrupo, &$params)
    {
        $situacion = $m->obtenerSituacionFinancieraGrupo($idGrupo);
        $usuarios = $m->obtenerUsuariosPorGrupo($idGrupo);
        $this->calcularSituacionUsuarios($m, $usuarios);
        $params['usuarios'] = $usuarios;
        $params['situacion'] = $situacion;
    }

    // Función para calcular la situación financiera de un usuario
    private function calcularSituacionUsuario($m, $idUsuario)
    {
        $usuario = $m->obtenerUsuarioPorId($idUsuario);
        $usuario['totalIngresos'] = $m->obtenerTotalIngresos($idUsuario);
        $usuario['totalGastos'] = $m->obtenerTotalGastos($idUsuario);
        $usuario['saldo'] = $usuario['totalIngresos'] - $usuario['totalGastos'];
        $usuario['detalles_ingresos'] = $m->obtenerIngresosPorUsuario($idUsuario);
        $usuario['detalles_gastos'] = $m->obtenerGastosPorUsuario($idUsuario);
        return $usuario;
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
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    // Método para manejar errores de redireccionamiento
    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
}
