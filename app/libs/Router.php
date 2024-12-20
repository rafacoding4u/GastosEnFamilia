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

            // Verificar que el archivo del controlador exista
            $controllerPath = __DIR__ . "/../controlador/{$controller}.php";
            if (file_exists($controllerPath)) {
                require_once $controllerPath;
                if (class_exists($controller) && method_exists($controller, $method)) {
                    $controllerInstance = new $controller();
                    $controllerInstance->$method();
                    return;
                }
            }
        }
        // Ruta no encontrada o controlador/método no válido
        header("Location: index.php?ctl=error");
        exit();
    }
}
