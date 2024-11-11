<?php

require_once 'classModelo.php';

class UsuarioGestion
{
    private $modelo;
    private $userId;

    public function __construct($userId)
    {
        $this->modelo = new GastosModelo();
        $this->userId = $userId;
    }

    // Obtener los datos del propio usuario
    public function obtenerDatosUsuario()
    {
        return $this->modelo->obtenerUsuarioPorId($this->userId);
    }

    // Actualizar los datos del propio usuario (ajustado a 7 parámetros)
    public function actualizarDatosUsuario($nuevosDatos)
    {
        $nombre = $nuevosDatos['nombre'] ?? null;
        $apellido = $nuevosDatos['apellido'] ?? null;
        $alias = $nuevosDatos['alias'] ?? null;
        $email = $nuevosDatos['email'] ?? null;
        $telefono = $nuevosDatos['telefono'] ?? null;
        $fecha_nacimiento = $nuevosDatos['fecha_nacimiento'] ?? null;

        // Llamada a actualizarUsuario con 7 parámetros requeridos
        return $this->modelo->actualizarUsuario(
            $this->userId,
            $nombre,
            $apellido,
            $alias,
            $email,
            $telefono,
            $fecha_nacimiento
        );
    }

    // Listar solo los gastos del propio usuario
    public function listarGastosUsuario()
    {
        return $this->modelo->obtenerGastosPorUsuario($this->userId);
    }

    // Agregar un gasto propio
    public function agregarGasto($datosGasto)
    {
        $datosGasto['idUser'] = $this->userId;
        return $this->modelo->insertarGasto($datosGasto);
    }

    // Editar un gasto propio
    public function editarGasto($idGasto, $nuevosDatos)
    {
        $gasto = $this->modelo->obtenerGastoPorId($idGasto);

        if ($gasto && $gasto['idUser'] === $this->userId) {
            return $this->modelo->actualizarGasto($idGasto, $nuevosDatos);
        } else {
            throw new Exception("No tienes permiso para editar este gasto.");
        }
    }

    // Eliminar un gasto propio
    public function eliminarGasto($idGasto)
    {
        $gasto = $this->modelo->obtenerGastoPorId($idGasto);

        if ($gasto && $gasto['idUser'] === $this->userId) {
            return $this->modelo->eliminarGastoPorId($idGasto);
        } else {
            throw new Exception("No tienes permiso para eliminar este gasto.");
        }
    }

    // Listar solo los ingresos del propio usuario
    public function listarIngresosUsuario()
    {
        return $this->modelo->obtenerIngresosPorUsuario($this->userId);
    }

    // Agregar un ingreso propio
    public function agregarIngreso($datosIngreso)
    {
        $datosIngreso['idUser'] = $this->userId;
        return $this->modelo->insertarIngreso($datosIngreso);
    }

    // Editar un ingreso propio
    public function editarIngreso($idIngreso, $nuevosDatos)
    {
        $ingreso = $this->modelo->obtenerIngresoPorId($idIngreso);

        if ($ingreso && $ingreso['idUser'] === $this->userId) {
            return $this->modelo->actualizarIngreso($idIngreso, $nuevosDatos);
        } else {
            throw new Exception("No tienes permiso para editar este ingreso.");
        }
    }

    // Eliminar un ingreso propio
    public function eliminarIngreso($idIngreso)
    {
        $ingreso = $this->modelo->obtenerIngresoPorId($idIngreso);

        if ($ingreso && $ingreso['idUser'] === $this->userId) {
            return $this->modelo->eliminarIngresoPorId($idIngreso);
        } else {
            throw new Exception("No tienes permiso para eliminar este ingreso.");
        }
    }

    // Ver la situación financiera individual del usuario
    public function obtenerSituacionFinanciera()
    {
        return $this->modelo->obtenerSituacionPorUsuario($this->userId);
    }

    // Verificar si el usuario puede unirse a una nueva familia o grupo
    public function puedeUnirseFamilia($idFamilia)
    {
        if ($this->modelo->contarFamiliasUsuario($this->userId) >= 2) {
            throw new Exception("Límite de familias alcanzado.");
        }
        if ($this->modelo->contarUsuariosFamilia($idFamilia) >= 10) {
            throw new Exception("La familia ya tiene el número máximo de usuarios.");
        }
        return true;
    }

    public function puedeUnirseGrupo($idGrupo)
    {
        if ($this->modelo->contarGruposUsuario($this->userId) >= 3) {
            throw new Exception("Límite de grupos alcanzado.");
        }
        if ($this->modelo->contarUsuariosGrupo($idGrupo) >= 200) {
            throw new Exception("El grupo ya tiene el número máximo de usuarios.");
        }
        return true;
    }

    // Obtener el resumen financiero del usuario
    public function obtenerResumenFinanciero()
    {
        return $this->modelo->obtenerResumenFinancieroPorUsuario($this->userId);
    }

    // Obtener un gasto específico por ID para el usuario actual
    public function obtenerGastoPorId($idGasto)
    {
        $gasto = $this->modelo->obtenerGastoPorId($idGasto);
        if ($gasto && $gasto['idUser'] === $this->userId) {
            return $gasto;
        } else {
            throw new Exception("No tienes permiso para acceder a este gasto.");
        }
    }

    // Obtener un ingreso específico por ID para el usuario actual
    public function obtenerIngresoPorId($idIngreso)
    {
        $ingreso = $this->modelo->obtenerIngresoPorId($idIngreso);
        if ($ingreso && $ingreso['idUser'] === $this->userId) {
            return $ingreso;
        } else {
            throw new Exception("No tienes permiso para acceder a este ingreso.");
        }
    }
}
