<?php
require_once 'app/libs/bSeguridad.php';
require_once 'app/libs/bGeneral.php';
require_once 'app/modelo/classModelo.php';

class UsuarioAdminController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new GastosModelo();
    }

    public function listarUsuariosAdmin()
    {
        try {
            $idAdmin = $_SESSION['usuario']['id'];
            $usuarios = $this->modelo->obtenerUsuariosGestionadosConRoles($idAdmin, $_GET); // Método para obtener solo los usuarios que el admin gestiona

            $params = [
                'usuarios' => $usuarios,
                'mensaje' => 'Lista de usuarios bajo administración',
            ];

            $this->render('listarUsuariosAdmin.php', $params);
        } catch (Exception $e) {
            error_log("Error en listarUsuariosAdmin(): " . $e->getMessage());
            $this->redireccionarError('Error al listar los usuarios.');
        }
    }

    public function crearUsuarioAdmin()
    {
        // Método para que el admin pueda crear usuarios, limitado a sus familias o grupos
        // Similar a `crearUsuario` en `UsuarioController`, pero con lógica ajustada para el Admin
    }

    public function editarUsuarioAdmin($idUser)
    {
        // Método para editar usuarios gestionados por el admin
        // Validará que solo pueda editar usuarios regulares de su ámbito
    }

    public function eliminarUsuarioAdmin($idUser)
    {
        // Método para eliminar usuarios, asegurando que solo puede eliminar usuarios regulares dentro de su gestión
    }

    private function render($vista, $params = array())
    {
        extract($params);
        ob_start();
        require __DIR__ . '/../../web/templates/' . $vista;
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }

    private function redireccionarError($mensaje)
    {
        $_SESSION['error_mensaje'] = $mensaje;
        header('Location: index.php?ctl=error');
        exit();
    }
}
