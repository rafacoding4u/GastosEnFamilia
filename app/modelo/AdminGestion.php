<?php

require_once 'classModelo.php';

class AdminGestion extends GastosModelo
{
    protected $conexion;
    private $adminId;

    public function __construct($adminId)
    {
        parent::__construct(); // Inicializa la conexión a la base de datos en la clase principal
        $this->conexion = $this->getConexion();
        $this->adminId = $adminId;
    }

    // Listar solo los usuarios gestionados por el administrador
    public function listarUsuariosGestionados()
    {
        return $this->obtenerUsuariosGestionados($this->adminId);
    }

    public function obtenerUsuariosGestionados($idAdmin, $filtros = [])
    {
        try {
            $sql = "
            SELECT u.*, 
                GROUP_CONCAT(DISTINCT f.nombre_familia ORDER BY f.nombre_familia ASC SEPARATOR ', ') AS familias,
                GROUP_CONCAT(DISTINCT g.nombre_grupo ORDER BY g.nombre_grupo ASC SEPARATOR ', ') AS grupos
            FROM usuarios u
            LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
            LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
            LEFT JOIN administradores_familias af ON af.idFamilia = f.idFamilia
            LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
            LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
            LEFT JOIN administradores_grupos ag ON ag.idGrupo = g.idGrupo
            WHERE (af.idAdmin = :idAdmin OR ag.idAdmin = :idAdmin OR u.idUser = :idAdmin)
            ";

            // Añadir filtros dinámicos si están presentes
            if (!empty($filtros['nombre'])) {
                $sql .= " AND u.nombre LIKE :nombre";
            }
            if (!empty($filtros['apellido'])) {
                $sql .= " AND u.apellido LIKE :apellido";
            }
            if (!empty($filtros['alias'])) {
                $sql .= " AND u.alias LIKE :alias";
            }
            if (!empty($filtros['email'])) {
                $sql .= " AND u.email LIKE :email";
            }
            if (!empty($filtros['nivel_usuario'])) {
                $sql .= " AND u.nivel_usuario = :nivel_usuario";
            }

            $sql .= " GROUP BY u.idUser";

            // Preparación de la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);

            // Añadir parámetros de los filtros si están presentes
            if (!empty($filtros['nombre'])) {
                $stmt->bindValue(':nombre', '%' . $filtros['nombre'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['apellido'])) {
                $stmt->bindValue(':apellido', '%' . $filtros['apellido'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['alias'])) {
                $stmt->bindValue(':alias', '%' . $filtros['alias'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['email'])) {
                $stmt->bindValue(':email', '%' . $filtros['email'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['nivel_usuario'])) {
                $stmt->bindValue(':nivel_usuario', $filtros['nivel_usuario'], PDO::PARAM_STR);
            }

            // Ejecutar la consulta y obtener resultados
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Resultado de obtenerUsuariosGestionados: " . json_encode($result));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuariosGestionados(): " . $e->getMessage());
            throw new Exception('Error al listar los usuarios gestionados.');
        }
    }

    // Crear un nuevo usuario dentro de los límites de familia y grupo
    public function crearUsuario($datosUsuario)
    {
        $familiaUsuarios = $this->contarUsuariosFamilia($datosUsuario['idFamilia']);
        $grupoUsuarios = $this->contarUsuariosGrupo($datosUsuario['idGrupo']);

        if ($familiaUsuarios >= 10) {
            throw new Exception("Límite de 10 usuarios en la familia alcanzado.");
        }

        if ($grupoUsuarios >= 30) {
            throw new Exception("Límite de 30 usuarios en el grupo alcanzado.");
        }

        return $this->insertarUsuario(
            $datosUsuario['nombre'],
            $datosUsuario['apellido'],
            $datosUsuario['alias'],
            $datosUsuario['email'],
            $datosUsuario['contrasenya'],
            $datosUsuario['fecha_nacimiento'],
            $datosUsuario['telefono'],
            $datosUsuario['idFamilia']
        );
    }

    // Editar solo los datos de los usuarios regulares gestionados por el administrador
    public function editarUsuarioRegular($idUser, $nuevosDatos)
    {
        $usuario = $this->obtenerUsuarioPorId($idUser);

        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            $this->conexion->beginTransaction();

            try {
                $this->actualizarUsuario(
                    $idUser,
                    $nuevosDatos['nombre'],
                    $nuevosDatos['apellido'],
                    $nuevosDatos['alias'],
                    $nuevosDatos['email'],
                    $nuevosDatos['telefono'],
                    $nuevosDatos['nivel_usuario'],
                    $nuevosDatos['fecha_nacimiento']
                );

                $this->eliminarFamiliasDeUsuario($idUser);
                if (!empty($nuevosDatos['idFamilia'])) {
                    foreach ($nuevosDatos['idFamilia'] as $idFamilia) {
                        $this->asignarUsuarioAFamilia($idUser, $idFamilia);
                    }
                }

                $this->eliminarGruposDeUsuario($idUser);
                if (!empty($nuevosDatos['idGrupo'])) {
                    foreach ($nuevosDatos['idGrupo'] as $idGrupo) {
                        $this->asignarUsuarioAGrupo($idUser, $idGrupo);
                    }
                }

                $this->conexion->commit();
                return true;
            } catch (Exception $e) {
                $this->conexion->rollBack();
                error_log("Error en editarUsuarioRegular: " . $e->getMessage());
                throw new Exception("Error al editar el usuario.");
            }
        } else {
            throw new Exception("No tienes permisos para editar este usuario.");
        }
    }

    // Eliminar solo los usuarios regulares bajo la administración del admin
    public function eliminarUsuarioRegular($idUser)
    {
        $usuario = $this->obtenerUsuarioPorId($idUser);

        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            return $this->eliminarUsuarioPorId($idUser);
        } else {
            throw new Exception("No tienes permisos para eliminar este usuario.");
        }
    }

    // Verificar si el usuario está gestionado por el admin
    private function esUsuarioGestionado($idUser)
    {
        $familiasGestionadas = $this->obtenerFamiliasAdmin($this->adminId);
        $gruposGestionados = $this->obtenerGruposAdmin($this->adminId);

        foreach ($familiasGestionadas as $familia) {
            if ($this->usuarioYaEnFamilia($idUser, $familia['idFamilia'])) {
                return true;
            }
        }
        foreach ($gruposGestionados as $grupo) {
            if ($this->usuarioYaEnGrupo($idUser, $grupo['idGrupo'])) {
                return true;
            }
        }

        return false;
    }

    // Métodos de manejo de familias y grupos
    public function obtenerFamiliasAdministradas()
    {
        $familias = $this->obtenerFamiliasAdmin($this->adminId);
        if (count($familias) >= 5) {
            throw new Exception("Límite de 5 familias administradas alcanzado.");
        }
        return $familias;
    }

    public function obtenerGruposAdministrados()
    {
        $grupos = $this->obtenerGruposAdmin($this->adminId);
        if (count($grupos) >= 5) {
            throw new Exception("Límite de 5 grupos administrados alcanzado.");
        }
        return $grupos;
    }

    public function eliminarFamiliasDeUsuario($idUser)
    {
        try {
            $sql = "DELETE FROM usuarios_familias WHERE idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Asociaciones de familias eliminadas para el usuario $idUser");
        } catch (Exception $e) {
            error_log("Error en eliminarFamiliasDeUsuario: " . $e->getMessage());
            throw new Exception("Error al eliminar familias del usuario.");
        }
    }

    public function eliminarGruposDeUsuario($idUser)
    {
        try {
            $sql = "DELETE FROM usuarios_grupos WHERE idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Asociaciones de grupos eliminadas para el usuario $idUser");
        } catch (Exception $e) {
            error_log("Error en eliminarGruposDeUsuario: " . $e->getMessage());
            throw new Exception("Error al eliminar grupos del usuario.");
        }
    }

    public function asignarUsuarioAFamilia($idUser, $idFamilia)
    {
        try {
            $sqlVerificar = "SELECT COUNT(*) FROM usuarios_familias WHERE idUser = :idUser AND idFamilia = :idFamilia";
            $stmtVerificar = $this->conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtVerificar->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmtVerificar->execute();

            if ($stmtVerificar->fetchColumn() > 0) {
                error_log("El usuario $idUser ya está asignado a la familia $idFamilia");
                return;
            }

            $sql = "INSERT INTO usuarios_familias (idUser, idFamilia) VALUES (:idUser, :idFamilia)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Usuario $idUser asignado a la familia $idFamilia exitosamente.");
        } catch (Exception $e) {
            error_log("Error en asignarUsuarioAFamilia: " . $e->getMessage());
            throw new Exception("Error al asignar usuario a la familia.");
        }
    }

    public function asignarUsuarioAGrupo($idUser, $idGrupo)
    {
        try {
            $sqlVerificar = "SELECT COUNT(*) FROM usuarios_grupos WHERE idUser = :idUser AND idGrupo = :idGrupo";
            $stmtVerificar = $this->conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtVerificar->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmtVerificar->execute();

            if ($stmtVerificar->fetchColumn() > 0) {
                error_log("El usuario $idUser ya está asignado al grupo $idGrupo");
                return false;
            }

            $sql = "INSERT INTO usuarios_grupos (idUser, idGrupo) VALUES (:idUser, :idGrupo)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Usuario $idUser asignado al grupo $idGrupo exitosamente.");
            return true;
        } catch (Exception $e) {
            error_log("Error en asignarUsuarioAGrupo: " . $e->getMessage());
            throw new Exception("Error al asignar usuario al grupo.");
        }
    }
}
