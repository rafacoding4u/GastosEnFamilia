<?php

class GastosModelo {

    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=gastos_db;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function listarGastos() {
        $query = "SELECT * FROM gastos ORDER BY fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarGasto($concepto, $monto, $fecha) {
        $query = "INSERT INTO gastos (concepto, monto, fecha) VALUES (:concepto, :monto, :fecha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':concepto', $concepto);
        $stmt->bindValue(':monto', $monto);
        $stmt->bindValue(':fecha', $fecha);
        return $stmt->execute();
    }

    public function consultarUsuario($nombreUsuario) {
        $query = "SELECT * FROM usuarios WHERE nombreUsuario = :nombreUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombreUsuario', $nombreUsuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeUsuario($nombreUsuario) {
        $query = "SELECT COUNT(*) FROM usuarios WHERE nombreUsuario = :nombreUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombreUsuario', $nombreUsuario);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario) {
        $query = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasenya, nivel_usuario) VALUES (:nombre, :apellido, :nombreUsuario, :contrasenya, :nivel_usuario)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':apellido', $apellido);
        $stmt->bindValue(':nombreUsuario', $nombreUsuario);
        $stmt->bindValue(':contrasenya', $contrasenya);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario);
        return $stmt->execute();
    }

    public function listarUsuarios() {
        $query = "SELECT * FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarUsuario($idUser) {
        $query = "DELETE FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUser', $idUser);
        return $stmt->execute();
    }

    public function eliminarGasto($idGasto) {
        $query = "DELETE FROM gastos WHERE idGasto = :idGasto";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idGasto', $idGasto);
        return $stmt->execute();
    }
}
?>
