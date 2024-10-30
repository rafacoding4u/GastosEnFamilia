<?php

class Router
{
    private $routes = [];

    /**
     * Define una ruta en el enrutador.
     *
     * @param string $path Ruta de la URL (por ejemplo, 'inicio').
     * @param string $controller Controlador asociado.
     * @param string $method Método del controlador a ejecutar.
     */
    public function addRoute($path, $controller, $method)
    {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
    }

    /**
     * Procesa la ruta solicitada.
     *
     * @param string $path Ruta de la URL solicitada.
     */
    public function handleRequest($path)
    {
        if (isset($this->routes[$path])) {
            $controller = $this->routes[$path]['controller'];
            $method = $this->routes[$path]['method'];

            // Cargar el controlador y llamar al método
            require_once __DIR__ . "/../controlador/{$controller}.php";
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        } else {
            // Ruta no encontrada: redirigir a una página de error o a la página principal
            header("Location: index.php?ctl=home");
            exit();
        }
    }
}
