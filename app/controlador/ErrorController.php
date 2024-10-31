<?php

class ErrorController
{
    /**
     * Método para mostrar la vista de acceso denegado.
     */
    public function accesoDenegado()
    {
        $params = ['mensaje' => 'Acceso denegado. No tienes permisos para acceder a esta página.'];
        $this->render('accesoDenegado.php', $params);
    }

    /**
     * Método para mostrar la vista de error genérico.
     */
    public function errorGeneral()
    {
        $params = ['mensaje' => 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.'];
        $this->render('error.php', $params);
    }

    /**
     * Método para renderizar vistas específicas.
     * @param string $vista La vista a mostrar.
     * @param array $params Parámetros a pasar a la vista.
     */
    private function render($vista, $params = [])
    {
        extract($params);
        ob_start();
        require __DIR__ . "/../../web/templates/{$vista}";
        $contenido = ob_get_clean();
        require __DIR__ . '/../../web/templates/layout.php';
    }
}
