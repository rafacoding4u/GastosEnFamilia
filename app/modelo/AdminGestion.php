<?php

require_once 'classModelo.php';

class AdminGestion
{
    private $modelo;
    private $adminId;

    public function __construct($adminId)
    {
        $this->modelo = new GastosModelo();
        $this->adminId = $adminId;
    }

    // Listar solo los usuarios gestionados por el administrador
    public function listarUsuariosGestionados()
    {
        return $this->modelo->obtenerUsuariosGestionados($this->adminId);
    }

    // Crear un nuevo usuario dentro de los límites de familia y grupo
    public function crearUsuario($datosUsuario)
    {
        // Verificar los límites de usuarios en familia y grupo
        $familiaUsuarios = $this->modelo->contarUsuariosFamilia($datosUsuario['idFamilia']);
        $grupoUsuarios = $this->modelo->contarUsuariosGrupo($datosUsuario['idGrupo']);

        if ($familiaUsuarios >= 10) {
            throw new Exception("Límite de 10 usuarios en la familia alcanzado.");
        }

        if ($grupoUsuarios >= 30) {
            throw new Exception("Límite de 30 usuarios en el grupo alcanzado.");
        }

        // Ajuste de la llamada a insertarUsuario en AdminGestion.php
        return $this->modelo->insertarUsuario(
            $datosUsuario['nombre'],
            $datosUsuario['apellido'],
            $datosUsuario['alias'],
            $datosUsuario['email'],
            $datosUsuario['contrasenya'],
            $datosUsuario['fecha_nacimiento'],
            $datosUsuario['telefono'],
            $datosUsuario['idFamilia'] // Ajusta para que haya solo 8 parámetros
        );
    }

    // Editar solo los datos de los usuarios regulares gestionados por el administrador
    public function editarUsuarioRegular($idUser, $nuevosDatos)
    {
        $usuario = $this->modelo->obtenerUsuarioPorId($idUser);

        // Verificar que el usuario sea regular y esté bajo gestión del admin actual
        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            return $this->modelo->actualizarUsuario(
                $idUser,
                $nuevosDatos['nombre'],
                $nuevosDatos['apellido'],
                $nuevosDatos['alias'],
                $nuevosDatos['email'],
                $nuevosDatos['telefono'],
                $nuevosDatos['fecha_nacimiento']
            );
        } else {
            throw new Exception("No tienes permisos para editar este usuario.");
        }
    }

    // Eliminar solo los usuarios regulares bajo la administración del admin
    public function eliminarUsuarioRegular($idUser)
    {
        $usuario = $this->modelo->obtenerUsuarioPorId($idUser);

        // Verificar que el usuario sea regular y esté bajo gestión del admin actual
        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            return $this->modelo->eliminarUsuarioPorId($idUser);
        } else {
            throw new Exception("No tienes permisos para eliminar este usuario.");
        }
    }

    // Verificar si el usuario está gestionado por el admin (pertenece a sus familias o grupos)
    private function esUsuarioGestionado($idUser)
    {
        // Familias y grupos administrados por el admin
        $familiasGestionadas = $this->modelo->obtenerFamiliasAdmin($this->adminId);
        $gruposGestionados = $this->modelo->obtenerGruposAdmin($this->adminId);

        // Verificar si el usuario está en alguna familia o grupo administrado
        foreach ($familiasGestionadas as $familia) {
            if ($this->modelo->usuarioYaEnFamilia($idUser, $familia['idFamilia'])) {
                return true;
            }
        }
        foreach ($gruposGestionados as $grupo) {
            if ($this->modelo->usuarioYaEnGrupo($idUser, $grupo['idGrupo'])) {
                return true;
            }
        }

        return false;
    }

    // Obtener familias administradas por el admin (respetando el límite de 5)
    public function obtenerFamiliasAdministradas()
    {
        $familias = $this->modelo->obtenerFamiliasAdmin($this->adminId);
        if (count($familias) >= 5) {
            throw new Exception("Límite de 5 familias administradas alcanzado.");
        }
        return $familias;
    }

    // Obtener grupos administrados por el admin (respetando el límite de 5)
    public function obtenerGruposAdministrados()
    {
        $grupos = $this->modelo->obtenerGruposAdmin($this->adminId);
        if (count($grupos) >= 5) {
            throw new Exception("Límite de 5 grupos administrados alcanzado.");
        }
        return $grupos;
    }

    public function obtenerUsuarioPorId($idUser)
    {
        return $this->modelo->obtenerUsuarioPorId($idUser);
    }
}
