-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2024 a las 10:19:58
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: gastosencasa_bd
--

-- --------------------------------------------------------

-- Estructura de tabla para la tabla administradores_familias
CREATE TABLE administradores_familias (
  idAdmin int(11) NOT NULL,
  idFamilia int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla administradores_grupos
CREATE TABLE administradores_grupos (
  idAdmin int(11) NOT NULL,
  idGrupo int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla categorias_gastos
CREATE TABLE categorias_gastos (
  idCategoria int(11) NOT NULL,
  nombreCategoria varchar(100) NOT NULL,
  creado_por int(11) DEFAULT NULL,
  estado_categoria enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla categorias_ingresos
CREATE TABLE categorias_ingresos (
  idCategoria int(11) NOT NULL,
  nombreCategoria varchar(100) NOT NULL,
  creado_por int(11) DEFAULT NULL,
  estado_categoria enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla envio_refranes
CREATE TABLE envio_refranes (
  idEnvio int(11) NOT NULL,
  idRefran int(11) NOT NULL,
  idUser int(11) NOT NULL,
  fecha_envio date NOT NULL,
  momento enum('mañana','tarde') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla familias
CREATE TABLE familias (
  idFamilia int(11) NOT NULL,
  nombre_familia varchar(100) NOT NULL,
  password varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla gastos
CREATE TABLE gastos (
  idGasto int(11) NOT NULL,
  idUser int(11) DEFAULT NULL,
  importe decimal(10,2) NOT NULL,
  idCategoria int(11) DEFAULT NULL,
  origen enum('banco','efectivo') NOT NULL,
  concepto varchar(100) NOT NULL,
  fecha date DEFAULT curdate(),
  idGrupo int(11) DEFAULT NULL,
  idFamilia int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla grupos
CREATE TABLE grupos (
  idGrupo int(11) NOT NULL,
  nombre_grupo varchar(100) NOT NULL,
  password varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla ingresos
CREATE TABLE ingresos (
  idIngreso int(11) NOT NULL,
  idUser int(11) DEFAULT NULL,
  importe decimal(10,2) NOT NULL,
  idCategoria int(11) DEFAULT NULL,
  origen enum('banco','efectivo') NOT NULL,
  concepto varchar(100) NOT NULL,
  fecha date DEFAULT curdate(),
  idGrupo int(11) DEFAULT NULL,
  idFamilia int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla refranes
CREATE TABLE refranes (
  idRefran int(11) NOT NULL,
  refran text NOT NULL,
  autor varchar(255) DEFAULT NULL,
  pais varchar(100) DEFAULT NULL,
  fecha_ultimo_uso timestamp NULL DEFAULT NULL,
  texto_refran varchar(255) DEFAULT NULL,
  fecha_creacion timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla situacion
CREATE TABLE situacion (
  idSituacion int(11) NOT NULL,
  idUser int(11) DEFAULT NULL,
  rango_fechas enum('mes','semana','año','intervalo') DEFAULT NULL,
  saldo decimal(10,2) NOT NULL,
  total_gastos decimal(10,2) NOT NULL,
  total_ingresos decimal(10,2) NOT NULL,
  fecha_inicio date DEFAULT NULL,
  fecha_fin date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla usuarios
CREATE TABLE usuarios (
  idUser int(11) NOT NULL,
  nombre varchar(100) NOT NULL,
  apellido varchar(100) NOT NULL,
  alias varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  contrasenya varchar(255) DEFAULT NULL,
  fecha_nacimiento date NOT NULL,
  telefono varchar(15) NOT NULL,
  nivel_usuario enum('superadmin','admin','usuario') DEFAULT 'usuario',
  fecha_registro timestamp NOT NULL DEFAULT current_timestamp(),
  idFamilia int(11) DEFAULT NULL,
  idGrupo int(11) DEFAULT NULL,
  estado_usuario enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índices para tablas volcadas
-- Indices de la tabla administradores_familias
ALTER TABLE administradores_familias
  ADD PRIMARY KEY (idAdmin,idFamilia),
  ADD KEY idFamilia (idFamilia);

-- Indices de la tabla administradores_grupos
ALTER TABLE administradores_grupos
  ADD PRIMARY KEY (idAdmin,idGrupo),
  ADD KEY idGrupo (idGrupo);

-- Indices de la tabla categorias_gastos
ALTER TABLE categorias_gastos
  ADD PRIMARY KEY (idCategoria),
  ADD KEY fk_categorias_gastos_usuario (creado_por);

-- Indices de la tabla categorias_ingresos
ALTER TABLE categorias_ingresos
  ADD PRIMARY KEY (idCategoria),
  ADD KEY fk_categorias_ingresos_usuario (creado_por);

-- Indices de la tabla envio_refranes
ALTER TABLE envio_refranes
  ADD PRIMARY KEY (idEnvio),
  ADD UNIQUE KEY unique_envio (idUser,fecha_envio,momento),
  ADD KEY idRefran (idRefran);

-- Indices de la tabla familias
ALTER TABLE familias
  ADD PRIMARY KEY (idFamilia),
  ADD KEY idx_nombre_familia (nombre_familia);

-- Indices de la tabla gastos
ALTER TABLE gastos
  ADD PRIMARY KEY (idGasto),
  ADD KEY idUser (idUser),
  ADD KEY idCategoria (idCategoria);

-- Indices de la tabla grupos
ALTER TABLE grupos
  ADD PRIMARY KEY (idGrupo),
  ADD KEY idx_nombre_grupo (nombre_grupo);

-- Indices de la tabla ingresos
ALTER TABLE ingresos
  ADD PRIMARY KEY (idIngreso),
  ADD KEY idUser (idUser),
  ADD KEY idCategoria (idCategoria);

-- Indices de la tabla refranes
ALTER TABLE refranes
  ADD PRIMARY KEY (idRefran);

-- Indices de la tabla situacion
ALTER TABLE situacion
  ADD PRIMARY KEY (idSituacion),
  ADD KEY idUser (idUser);

-- Indices de la tabla usuarios
ALTER TABLE usuarios
  ADD PRIMARY KEY (idUser),
  ADD UNIQUE KEY email (email),
  ADD KEY fk_familia (idFamilia),
  ADD KEY fk_grupo (idGrupo),
  ADD KEY idx_usuario_nivel (nivel_usuario,idGrupo,idFamilia);

-- AUTO_INCREMENT de las tablas volcadas
-- AUTO_INCREMENT de la tabla categorias_gastos
ALTER TABLE categorias_gastos
  MODIFY idCategoria int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla categorias_ingresos
ALTER TABLE categorias_ingresos
  MODIFY idCategoria int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla envio_refranes
ALTER TABLE envio_refranes
  MODIFY idEnvio int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla familias
ALTER TABLE familias
  MODIFY idFamilia int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla gastos
ALTER TABLE gastos
  MODIFY idGasto int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla grupos
ALTER TABLE grupos
  MODIFY idGrupo int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla ingresos
ALTER TABLE ingresos
  MODIFY idIngreso int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla refranes
ALTER TABLE refranes
  MODIFY idRefran int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla situacion
ALTER TABLE situacion
  MODIFY idSituacion int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla usuarios
ALTER TABLE usuarios
  MODIFY idUser int(11) NOT NULL AUTO_INCREMENT;

-- Restricciones para tablas volcadas
-- Filtros para la tabla administradores_familias
ALTER TABLE administradores_familias
  ADD CONSTRAINT administradores_familias_ibfk_1 FOREIGN KEY (idAdmin) REFERENCES usuarios (idUser) ON DELETE CASCADE,
  ADD CONSTRAINT administradores_familias_ibfk_2 FOREIGN KEY (idFamilia) REFERENCES familias (idFamilia) ON DELETE CASCADE;

-- Filtros para la tabla administradores_grupos
ALTER TABLE administradores_grupos
  ADD CONSTRAINT administradores_grupos_ibfk_1 FOREIGN KEY (idAdmin) REFERENCES usuarios (idUser) ON DELETE CASCADE,
  ADD CONSTRAINT administradores_grupos_ibfk_2 FOREIGN KEY (idGrupo) REFERENCES grupos (idGrupo) ON DELETE CASCADE;

-- Filtros para la tabla categorias_gastos
ALTER TABLE categorias_gastos
  ADD CONSTRAINT fk_categorias_gastos_usuario FOREIGN KEY (creado_por) REFERENCES usuarios (idUser);

-- Filtros para la tabla categorias_ingresos
ALTER TABLE categorias_ingresos
  ADD CONSTRAINT fk_categorias_ingresos_usuario FOREIGN KEY (creado_por) REFERENCES usuarios (idUser);

-- Filtros para la tabla envio_refranes
ALTER TABLE envio_refranes
  ADD CONSTRAINT envio_refranes_ibfk_1 FOREIGN KEY (idRefran) REFERENCES refranes (idRefran),
  ADD CONSTRAINT envio_refranes_ibfk_2 FOREIGN KEY (idUser) REFERENCES usuarios (idUser);

-- Filtros para la tabla gastos
ALTER TABLE gastos
  ADD CONSTRAINT gastos_ibfk_1 FOREIGN KEY (idUser) REFERENCES usuarios (idUser) ON DELETE SET NULL,
  ADD CONSTRAINT gastos_ibfk_2 FOREIGN KEY (idCategoria) REFERENCES categorias_gastos (idCategoria);

-- Filtros para la tabla ingresos
ALTER TABLE ingresos
  ADD CONSTRAINT ingresos_ibfk_1 FOREIGN KEY (idUser) REFERENCES usuarios (idUser) ON DELETE SET NULL,
  ADD CONSTRAINT ingresos_ibfk_2 FOREIGN KEY (idCategoria) REFERENCES categorias_ingresos (idCategoria);

-- Filtros para la tabla situacion
ALTER TABLE situacion
  ADD CONSTRAINT situacion_ibfk_1 FOREIGN KEY (idUser) REFERENCES usuarios (idUser);

-- Filtros para la tabla usuarios
ALTER TABLE usuarios
  ADD CONSTRAINT fk_familia FOREIGN KEY (idFamilia) REFERENCES familias (idFamilia) ON DELETE SET NULL,
  ADD CONSTRAINT fk_grupo FOREIGN KEY (idGrupo) REFERENCES grupos (idGrupo) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
