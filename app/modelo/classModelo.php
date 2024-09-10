<?php

class GastosModelo {

    private $db;

    public function __construct() {
        // Conexión a la base de datos
        $this->db = new PDO('mysql:host=localhost;dbname=GastosEnCasa_bd;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // -------------------------------
    // Métodos relacionados con gastos
    // -------------------------------

    public function listarGastos() {
        $query = "SELECT * FROM gastos ORDER BY fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarGasto($concepto, $monto, $fecha, $idUser) {
        $query = "INSERT INTO gastos (concepto, monto, fecha, idUser) VALUES (:concepto, :monto, :fecha, :idUser)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':concepto', $concepto);
        $stmt->bindValue(':monto', $monto);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':idUser', $idUser);
        return $stmt->execute();
    }

    public function eliminarGasto($idGasto) {
        $query = "DELETE FROM gastos WHERE idGasto = :idGasto";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idGasto', $idGasto);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con usuarios
    // -------------------------------

    public function listarUsuarios() {
        $query = "SELECT * FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario, $fecha_nacimiento, $email, $telefono) {
        $query = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono) 
                  VALUES (:nombre, :apellido, :nombreUsuario, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':apellido', $apellido);
        $stmt->bindValue(':nombreUsuario', $nombreUsuario);
        $stmt->bindValue(':contrasenya', $contrasenya);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario);
        $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telefono', $telefono);
        return $stmt->execute();
    }

    public function eliminarUsuario($idUser) {
        $query = "DELETE FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUser', $idUser);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con refranes
    // -------------------------------

    // Obtener un refrán que no se haya usado en los últimos 365 días
    public function obtenerRefranNoUsado() {
        $query = "SELECT * FROM refranes 
                  WHERE idRefran NOT IN (
                      SELECT idRefran FROM envio_refranes 
                      WHERE fecha_envio > DATE_SUB(NOW(), INTERVAL 365 DAY)
                  ) 
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar el envío de un refrán
    public function registrarEnvioRefran($idRefran, $idUser, $momento) {
        $query = "INSERT INTO envio_refranes (idRefran, idUser, fecha_envio, momento) 
                  VALUES (:idRefran, :idUser, NOW(), :momento)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idRefran', $idRefran);
        $stmt->bindValue(':idUser', $idUser);
        $stmt->bindValue(':momento', $momento);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos adicionales que puedas necesitar
    // -------------------------------

    // Listar los refranes enviados para un usuario
    public function listarRefranesEnviados($idUser) {
        $query = "SELECT r.refran, e.fecha_envio, e.momento 
                  FROM refranes r 
                  JOIN envio_refranes e ON r.idRefran = e.idRefran 
                  WHERE e.idUser = :idUser";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUser', $idUser);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
