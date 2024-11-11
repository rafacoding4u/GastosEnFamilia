<?php

class Router
{
    private $routes = [];

    // Añadir una ruta específica con controlador y método asociados
    public function addRoute($path, $controller, $method)
    {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
        error_log("Ruta añadida: $path -> $controller::$method");
    }

    // Manejar la solicitud actual verificando la ruta y llamando al controlador adecuado
    public function handleRequest($path)
    {
        try {
            // Comprobar si la ruta está definida en el array de rutas
            if (isset($this->routes[$path])) {
                $controllerName = $this->routes[$path]['controller'];
                $methodName = $this->routes[$path]['method'];

                error_log("Procesando ruta: $path usando controlador: $controllerName y método: $methodName");

                // Ruta hacia el controlador
                $controllerPath = "app/controlador/{$controllerName}.php";

                // Comprobar si el archivo de controlador existe
                if (file_exists($controllerPath)) {
                    require_once $controllerPath;

                    // Verificar que la clase del controlador existe
                    if (class_exists($controllerName)) {
                        $controllerInstance = new $controllerName();

                        // Verificar que el método existe en el controlador
                        if (method_exists($controllerInstance, $methodName)) {
                            $controllerInstance->$methodName();
                        } else {
                            throw new Exception("Método $methodName no encontrado en el controlador $controllerName.");
                        }
                    } else {
                        throw new Exception("Controlador $controllerName no encontrado.");
                    }
                } else {
                    throw new Exception("Archivo de controlador $controllerPath no encontrado.");
                }
            } else {
                // Redirigir a error si la ruta no existe
                error_log("Ruta no encontrada: $path. Redirigiendo a página de error.");
                header("Location: index.php?ctl=error");
                exit();
            }
        } catch (Exception $e) {
            error_log("Excepción en Router al manejar la ruta: " . $e->getMessage());
            header("Location: index.php?ctl=error");
            exit();
        }
    }
}
