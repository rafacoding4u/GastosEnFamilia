<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class AuditoriaController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
    }

    // Método para ver la auditoría
    public function verAuditoria()
    {
        try {
            // Comprobar si el usuario tiene permisos de superadmin
            if (!esSuperadmin()) {
                throw new Exception("No tienes permisos para acceder a esta página.");
            }

            $params = [];

            // Verificar si se ha solicitado la auditoría de un usuario específico
            $idUser = recoge('idUser');
            if ($idUser) {
                $params['auditoria'] = $this->modelo->obtenerAuditoriaPorUsuario($idUser);
                $params['usuario'] = $this->modelo->obtenerUsuarioPorId($idUser);
            } else {
                // Obtener todos los registros de auditoría
                $params['auditoria'] = $this->modelo->obtenerAuditoriaGlobal();
            }

            // Renderizar la vista con los datos de auditoría
            $this->render('verAuditoria.php', $params);
        } catch (Exception $e) {
            error_log("Error en verAuditoria(): " . $e->getMessage());
            $this->redireccionarError("Error al intentar ver la auditoría.");
        }
    }

    // Método para registrar una acción en la auditoría
    public function registrarAccion($accion, $detalles)
    {
        try {
            if (isset($_SESSION['usuario']['id'])) {
                $idUser = $_SESSION['usuario']['id'];
                $this->modelo->registrarAccionAuditoria($idUser, $accion, $detalles);
            }
        } catch (Exception $e) {
            error_log("Error en registrarAccion(): " . $e->getMessage());
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
