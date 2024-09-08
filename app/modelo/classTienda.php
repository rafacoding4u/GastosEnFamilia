<?php

class Tienda extends Modelo {
    
    public function consultarUsuario($nombreUsuario) {
        $consulta = "SELECT * FROM usuarios WHERE nombreUsuario=:nombreUsuario";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombreUsuario', $nombreUsuario);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    

    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario) {
        $consulta = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasenya, nivel_usuario) VALUES (:nombre, :apellido, :nombreUsuario, :contrasenya, :nivel_usuario)";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->bindParam(':apellido', $apellido);
        $result->bindParam(':nombreUsuario', $nombreUsuario);
        $result->bindParam(':contrasenya', $contrasenya);
        $result->bindParam(':nivel_usuario', $nivel_usuario);
        $result->execute();
        return $result;
    }
    
    
    public function listarProductos() {
        $consulta = "SELECT p.idProducto, p.nombre, c.nombreCategoria, p.precio 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.idCategoria 
                     ORDER BY p.nombre ASC";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function verProducto($idProducto) {
        $consulta = "SELECT p.idProducto, p.nombre, c.nombreCategoria AS categoria, p.precio 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.idCategoria 
                     WHERE p.idProducto = :idProducto";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':idProducto', $idProducto);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    
    public function buscarProductosNombre($nombre) {
        $consulta = "SELECT p.idProducto, p.nombre, c.nombreCategoria AS categoria, p.precio 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.idCategoria 
                     WHERE p.nombre LIKE :nombre";
        $result = $this->conexion->prepare($consulta);
        $nombre = "%$nombre%";
        $result->bindParam(':nombre', $nombre);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductosCategoria($categoria) {
        $consulta = "SELECT p.idProducto, p.nombre, c.nombreCategoria, p.precio 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.idCategoria 
                     WHERE c.nombreCategoria = :categoria";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':categoria', $categoria);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductosPrecio($precio) {
        $consulta = "SELECT p.idProducto, p.nombre, c.nombreCategoria, p.precio 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.idCategoria 
                     WHERE p.precio <= :precio";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':precio', $precio);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insertarProducto($nombre, $categoria_id, $precio) {
        $consulta = "INSERT INTO productos (nombre, categoria_id, precio) VALUES (:nombre, :categoria_id, :precio)";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->bindParam(':categoria_id', $categoria_id);
        $result->bindParam(':precio', $precio);
        $result->execute();
        return $result;
    }

    public function listarCategorias() {
        $consulta = "SELECT * FROM categorias";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias() {
        $consulta = "SELECT idCategoria, nombreCategoria FROM categorias";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarProducto($idProducto) {
        $consulta = "DELETE FROM productos WHERE idProducto = :idProducto";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':idProducto', $idProducto);
        return $result->execute();
    }

    public function listarUsuarios() {
        $consulta = "SELECT idUser, nombre, apellido, nombreUsuario, nivel_usuario FROM usuarios";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarUsuario($idUser) {
        $consulta = "DELETE FROM usuarios WHERE idUser = :idUser";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':idUser', $idUser);
        return $result->execute();
    }

    public function existeUsuario($nombreUsuario) {
        $consulta = "SELECT COUNT(*) FROM usuarios WHERE LOWER(REPLACE(nombreUsuario, ' ', '')) = LOWER(REPLACE(:nombreUsuario, ' ', ''))";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombreUsuario', $nombreUsuario);
        $result->execute();
        return $result->fetchColumn() > 0;
    }
    
    public function existeProducto($nombre) {
        $consulta = "SELECT COUNT(*) FROM productos WHERE LOWER(REPLACE(nombre, ' ', '')) = LOWER(REPLACE(:nombre, ' ', ''))";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->execute();
        return $result->fetchColumn() > 0;
    }
    
}

