-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2024 a las 18:22:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gastosencasa_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_familias`
--

CREATE TABLE `administradores_familias` (
  `id` int(11) NOT NULL,
  `idAdmin` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_familias`
--

INSERT INTO `administradores_familias` (`id`, `idAdmin`, `idFamilia`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 4, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_grupos`
--

CREATE TABLE `administradores_grupos` (
  `idAdmin` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_grupos`
--

INSERT INTO `administradores_grupos` (`idAdmin`, `idGrupo`) VALUES
(2, 1),
(3, 2),
(4, 3);

--
-- Disparadores `administradores_grupos`
--
DELIMITER $$
CREATE TRIGGER `before_insert_administradores_grupos` BEFORE INSERT ON `administradores_grupos` FOR EACH ROW BEGIN
  -- Validar si el administrador existe
  IF (SELECT COUNT(*) FROM usuarios WHERE idUser = NEW.idAdmin) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El administrador no existe';
  END IF;

  -- Validar si el grupo existe
  IF (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `idAuditoria` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla_afectada` varchar(255) NOT NULL,
  `idRegistro` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(1, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 14:55:55'),
(2, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 14:55:55'),
(3, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 14:55:55'),
(4, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 14:55:55'),
(5, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 14:55:55'),
(6, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 14:55:55'),
(7, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 14:55:55'),
(8, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 14:55:55'),
(9, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 14:55:55'),
(10, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 14:55:55'),
(11, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 14:55:55'),
(12, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 14:55:55'),
(13, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 14:55:55'),
(14, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 14:57:22'),
(15, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 14:57:22'),
(16, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 14:57:22'),
(17, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 14:57:22'),
(18, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 14:57:22'),
(19, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 14:57:22'),
(20, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 14:57:22'),
(21, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 14:57:22'),
(22, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 14:57:22'),
(23, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 14:57:22'),
(24, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 14:57:22'),
(25, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 14:57:22'),
(26, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 14:57:22'),
(27, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 18:56:57'),
(28, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 18:57:13'),
(29, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 18:57:21'),
(30, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 18:57:27'),
(31, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 18:57:32'),
(32, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 18:57:38'),
(33, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 18:57:43'),
(34, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 18:57:48'),
(35, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 18:57:53'),
(36, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 18:58:09'),
(37, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 18:58:15'),
(38, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 18:58:19'),
(39, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 18:58:24'),
(40, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 18:58:28'),
(41, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 18:58:59'),
(42, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 18:59:17'),
(43, 'INSERT', 'gastos', 14, '2', '2024-10-06 19:09:40'),
(44, 'INSERT', 'gastos', 15, '2', '2024-10-06 19:10:45'),
(45, 'UPDATE', 'gastos', 1, '2', '2024-10-06 19:12:47'),
(46, 'DELETE', 'gastos', 1, '2', '2024-10-06 19:12:49'),
(47, 'INSERT', 'categorias', 15, '2', '2024-10-07 08:41:30'),
(48, 'INSERT', 'categorias', 16, '2', '2024-10-07 08:41:30'),
(49, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:08:10'),
(50, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:08:10'),
(51, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:08:10'),
(52, 'UPDATE', 'usuarios', 9, '9', '2024-10-07 15:08:10'),
(53, 'UPDATE', 'usuarios', 10, '10', '2024-10-07 15:08:10'),
(54, 'UPDATE', 'usuarios', 11, '11', '2024-10-07 15:08:10'),
(55, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:08:10'),
(56, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:08:10'),
(57, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:13:07'),
(58, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:13:07'),
(59, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:13:07'),
(60, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:13:07'),
(61, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:13:07'),
(62, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:13:07'),
(63, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:13:07'),
(64, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:13:07'),
(65, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:13:07'),
(66, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:15:09'),
(67, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:15:09'),
(68, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:15:09'),
(69, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:15:09'),
(70, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:15:09'),
(71, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:15:09'),
(72, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:15:09'),
(73, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:15:09'),
(74, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:15:09'),
(75, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:18:14'),
(76, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:18:14'),
(77, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:18:14'),
(78, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:18:14'),
(79, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:18:14'),
(80, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:18:14'),
(81, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:18:14'),
(82, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:18:14'),
(83, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:18:14'),
(84, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:19:48'),
(85, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:19:48'),
(86, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:19:48'),
(87, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:19:48'),
(88, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:19:48'),
(89, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:19:48'),
(90, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:19:48'),
(91, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:19:48'),
(92, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:19:48'),
(93, 'UPDATE', 'gastos', 4, '2', '2024-10-07 16:06:24'),
(94, 'UPDATE', 'gastos', 9, '3', '2024-10-07 16:06:24'),
(95, 'UPDATE', 'gastos', 14, '2', '2024-10-07 16:06:24'),
(96, 'UPDATE', 'gastos', 15, '2', '2024-10-07 16:06:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos`
--

CREATE TABLE `auditoria_accesos` (
  `idAcceso` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_accesos`
--

INSERT INTO `auditoria_accesos` (`idAcceso`, `idUser`, `accion`, `fecha`) VALUES
(1, 2, 'logout', '2024-10-07 13:34:25'),
(2, 2, 'login', '2024-10-07 13:34:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos_archivo`
--

CREATE TABLE `auditoria_accesos_archivo` (
  `idAcceso` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `idCategoria` int(11) NOT NULL,
  `nombreCategoria` varchar(100) NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `estado_categoria` enum('activo','inactivo') DEFAULT 'activo',
  `tipo_categoria` enum('gasto','ingreso') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`idCategoria`, `nombreCategoria`, `creado_por`, `estado_categoria`, `tipo_categoria`) VALUES
(2, 'Transporte', 1, 'activo', 'gasto'),
(3, 'Vivienda', 1, 'activo', 'gasto'),
(4, 'Alquiler', 1, 'activo', 'gasto'),
(5, 'Compras', 1, 'activo', 'gasto'),
(6, 'Ocio', 1, 'activo', 'gasto'),
(7, 'Educación', 1, 'activo', 'gasto'),
(8, 'Intereses', 1, 'activo', 'ingreso'),
(9, 'Regalos', 1, 'activo', 'ingreso'),
(10, 'Salario', 1, 'activo', 'ingreso'),
(11, 'Venta', 1, 'activo', 'ingreso'),
(12, 'Inversiones', 1, 'activo', 'ingreso'),
(13, 'Premios', 1, 'activo', 'ingreso'),
(14, 'Otros', 1, 'activo', 'ingreso'),
(15, 'Servicios', 2, 'activo', 'gasto'),
(16, 'Dividendos', 2, 'activo', 'ingreso');

--
-- Disparadores `categorias`
--
DELIMITER $$
CREATE TRIGGER `auditar_categoria_insert` AFTER INSERT ON `categorias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'categorias', NEW.idCategoria, NEW.creado_por, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `idConfig` int(11) NOT NULL,
  `clave_config` varchar(100) DEFAULT NULL,
  `valor_config` text DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envio_refranes`
--

CREATE TABLE `envio_refranes` (
  `idEnvio` int(11) NOT NULL,
  `idRefran` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_envio` date NOT NULL,
  `momento` enum('mañana','tarde') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familias`
--

CREATE TABLE `familias` (
  `idFamilia` int(11) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familias`
--

INSERT INTO `familias` (`idFamilia`, `nombre_familia`, `password`) VALUES
(1, 'Familia1', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(2, 'Familia2', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(3, 'Familia3', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(4, 'Familia Perez', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(5, 'Familia Gomez', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(6, 'Familia Garcia', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi');

--
-- Disparadores `familias`
--
DELIMITER $$
CREATE TRIGGER `auditar_familias` AFTER INSERT ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'familias', NEW.idFamilia, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_delete` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'familias', OLD.idFamilia, NULL, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_update` AFTER UPDATE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'familias', NEW.idFamilia, NULL, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `eliminar_gastos_huerfanos` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  DELETE FROM gastos WHERE idFamilia = OLD.idFamilia;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `eliminar_ingresos_huerfanos` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  DELETE FROM ingresos WHERE idFamilia = OLD.idFamilia;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `idGasto` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idGasto`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(2, 2, 100.00, 2, 'efectivo', 'Transporte público', '2024-10-03 22:00:00', 0, 0),
(3, 2, 300.00, 3, 'banco', 'Pago del alquiler', '2024-10-03 22:00:00', 0, 0),
(4, 2, 420.30, 1, 'efectivo', 'Pago de alquiler', '2020-12-21 23:00:00', 0, 0),
(5, 2, 120.15, 2, 'banco', 'Compra de supermercado', '2021-04-14 22:00:00', 0, 0),
(6, 2, 90.00, 3, 'banco', 'Transporte mensual', '2021-04-19 22:00:00', 0, 0),
(7, 2, 50.50, 4, 'efectivo', 'Ocio y entretenimiento', '2021-05-21 22:00:00', 0, 0),
(8, 2, 200.00, 5, 'banco', 'Pago de estudios', '2021-06-11 22:00:00', 0, 0),
(9, 3, 450.30, 1, 'efectivo', 'Pago de alquiler', '2020-12-24 23:00:00', 0, 0),
(10, 3, 100.15, 2, 'banco', 'Compra de supermercado', '2021-04-17 22:00:00', 0, 0),
(11, 3, 85.00, 3, 'banco', 'Transporte mensual', '2021-04-22 22:00:00', 0, 0),
(12, 3, 60.50, 4, 'efectivo', 'Cine y entretenimiento', '2021-05-24 22:00:00', 0, 0),
(13, 3, 220.00, 5, 'banco', 'Matrícula estudios', '2021-06-14 22:00:00', 0, 0),
(14, 2, 150.50, 1, 'efectivo', 'Compra de libros', '2024-10-06 19:09:40', 1, 1),
(15, 2, 150.50, 1, 'efectivo', 'Compra de libros', '2024-10-06 19:10:45', 1, 1);

--
-- Disparadores `gastos`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_gasto` AFTER INSERT ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'gastos', NEW.idGasto, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_delete` AFTER DELETE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'gastos', OLD.idGasto, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_update` AFTER UPDATE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'gastos', NEW.idGasto, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_gasto_asignacion` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NULL AND NEW.idGrupo IS NULL THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Un gasto debe pertenecer a una familia, a un grupo o ser personal.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_consistencia_gasto` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  DECLARE v_familia_exist INT;
  DECLARE v_grupo_exist INT;
  
  -- Validar que el usuario pertenece a la familia, si está asignada
  IF NEW.idFamilia IS NOT NULL THEN
    SELECT COUNT(*) INTO v_familia_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idFamilia = NEW.idFamilia;
    
    IF v_familia_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece a la familia asignada';
    END IF;
  END IF;
  
  -- Validar que el usuario pertenece al grupo, si está asignado
  IF NEW.idGrupo IS NOT NULL THEN
    SELECT COUNT(*) INTO v_grupo_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idGrupo = NEW.idGrupo;
    
    IF v_grupo_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece al grupo asignado';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_consistencia_gasto` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
    -- Verificar que el gasto no sea negativo
    IF NEW.importe < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El importe del gasto no puede ser negativo';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_gasto_familia_grupo` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  DECLARE familia_grupo_valido BOOLEAN;
  
  -- Verificar si el usuario pertenece a la familia o grupo indicado
  SELECT COUNT(*) INTO familia_grupo_valido
  FROM usuarios
  WHERE idUser = NEW.idUser
    AND (idFamilia = NEW.idFamilia OR idGrupo = NEW.idGrupo);
  
  IF familia_grupo_valido = 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'El usuario no pertenece a la familia o grupo indicado';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `idGrupo` int(11) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idGrupo`, `nombre_grupo`, `password`) VALUES
(1, 'Grupo1', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(2, 'Grupo2', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(3, 'Grupo3', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(4, 'Grupo Amigos Perez', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(5, 'Grupo Amigos Gomez', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi'),
(6, 'Grupo Amigos Garcia', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi');

--
-- Disparadores `grupos`
--
DELIMITER $$
CREATE TRIGGER `auditar_grupos` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'grupos', NEW.idGrupo, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'grupos', OLD.idGrupo, NULL, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_insert` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'grupos', NEW.idGrupo, NULL, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_update` AFTER UPDATE ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'grupos', NEW.idGrupo, NULL, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_insert_grupos` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_update_grupos` AFTER UPDATE ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_grupos` BEFORE INSERT ON `grupos` FOR EACH ROW BEGIN
  DECLARE v_grupo_exist INT;
  
  SELECT COUNT(*) INTO v_grupo_exist FROM grupos WHERE nombre_grupo = NEW.nombre_grupo;
  
  IF v_grupo_exist > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo ya existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `idIngreso` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`idIngreso`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(1, 2, 1000.00, NULL, 'banco', 'Salario', '2024-10-03 22:00:00', 0, 0),
(2, 2, 50.00, 2, 'efectivo', 'Intereses', '2024-10-03 22:00:00', 0, 0),
(3, 2, 200.00, 3, 'banco', 'Regalo de cumpleaños', '2024-10-03 22:00:00', 0, 0),
(4, 2, 1200.55, NULL, 'banco', 'Salario mensual', '2021-02-12 23:00:00', 0, 0),
(5, 2, 2450.75, 2, 'efectivo', 'Venta de coche', '2020-08-08 22:00:00', 0, 0),
(6, 2, 110.40, 3, 'efectivo', 'Inversiones', '2021-05-09 22:00:00', 0, 0),
(7, 2, 90.30, 4, 'banco', 'Premio concurso', '2021-06-10 22:00:00', 0, 0),
(8, 2, 250.00, 5, 'banco', 'Otros ingresos', '2021-07-11 22:00:00', 0, 0),
(9, 3, 1300.55, NULL, 'banco', 'Salario mensual', '2021-02-13 23:00:00', 0, 0),
(10, 3, 2250.75, 2, 'efectivo', 'Venta de moto', '2020-08-10 22:00:00', 0, 0),
(11, 3, 210.40, 3, 'efectivo', 'Inversiones', '2021-05-13 22:00:00', 0, 0),
(12, 3, 190.30, 4, 'banco', 'Premio torneo', '2021-06-13 22:00:00', 0, 0),
(13, 3, 350.00, 5, 'banco', 'Otros ingresos', '2021-07-14 22:00:00', 0, 0);

--
-- Disparadores `ingresos`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_ingreso` AFTER INSERT ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'ingresos', NEW.idIngreso, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_delete` AFTER DELETE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'ingresos', OLD.idIngreso, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_update` AFTER UPDATE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'ingresos', NEW.idIngreso, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_ingreso_asignacion` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NULL AND NEW.idGrupo IS NULL THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Un ingreso debe pertenecer a una familia, a un grupo o ser personal.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_consistencia_ingreso` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  DECLARE v_familia_exist INT;
  DECLARE v_grupo_exist INT;
  
  -- Validar que el usuario pertenece a la familia, si está asignada
  IF NEW.idFamilia IS NOT NULL THEN
    SELECT COUNT(*) INTO v_familia_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idFamilia = NEW.idFamilia;
    
    IF v_familia_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece a la familia asignada';
    END IF;
  END IF;
  
  -- Validar que el usuario pertenece al grupo, si está asignado
  IF NEW.idGrupo IS NOT NULL THEN
    SELECT COUNT(*) INTO v_grupo_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idGrupo = NEW.idGrupo;
    
    IF v_grupo_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece al grupo asignado';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_consistencia_ingreso` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  DECLARE familia_invalida INT;
  DECLARE grupo_invalido INT;

  -- Verificar que el usuario pertenece a la familia
  SELECT COUNT(*) INTO familia_invalida
  FROM usuarios
  WHERE idUser = NEW.idUser AND idFamilia = NEW.idFamilia;

  -- Verificar que el usuario pertenece al grupo
  SELECT COUNT(*) INTO grupo_invalido
  FROM usuarios
  WHERE idUser = NEW.idUser AND idGrupo = NEW.idGrupo;

  -- Si el usuario no está asociado correctamente, se cancela la operación
  IF familia_invalida = 0 OR grupo_invalido = 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Inconsistencia detectada: El usuario no está asociado correctamente a la familia o grupo.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_ingreso_familia_grupo` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  DECLARE familia_grupo_valido BOOLEAN;
  
  -- Verificar si el usuario pertenece a la familia o grupo indicado
  SELECT COUNT(*) INTO familia_grupo_valido
  FROM usuarios
  WHERE idUser = NEW.idUser
    AND (idFamilia = NEW.idFamilia OR idGrupo = NEW.idGrupo);
  
  IF familia_grupo_valido = 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'El usuario no pertenece a la familia o grupo indicado';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_letter_envios`
--

CREATE TABLE `news_letter_envios` (
  `idEnvio` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `idRefran` int(11) NOT NULL,
  `saldo_total` decimal(10,2) NOT NULL,
  `gastos_totales` decimal(10,2) NOT NULL,
  `ingresos_totales` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refranes`
--

CREATE TABLE `refranes` (
  `idRefran` int(11) NOT NULL,
  `refran` text NOT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `fecha_ultimo_uso` timestamp NULL DEFAULT NULL,
  `texto_refran` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `refranes`
--

INSERT INTO `refranes` (`idRefran`, `refran`, `autor`, `pais`, `fecha_ultimo_uso`, `texto_refran`, `fecha_creacion`) VALUES
(1, 'A caballo regalado no le mires el diente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(2, 'A quien madruga, Dios le ayuda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(3, 'Más vale tarde que nunca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(4, 'No por mucho madrugar, amanece más temprano', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(5, 'A buen entendedor, pocas palabras bastan', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(6, 'En boca cerrada no entran moscas', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(7, 'Perro ladrador, poco mordedor', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(8, 'Camarón que se duerme, se lo lleva la corriente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(9, 'Al mal tiempo, buena cara', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(10, 'Ojos que no ven, corazón que no siente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(11, 'Más vale pájaro en mano que ciento volando', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(12, 'Dime con quién andas y te diré quién eres', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(13, 'El que mucho abarca, poco aprieta', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(14, 'Cría cuervos y te sacarán los ojos', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(15, 'A quien buen árbol se arrima, buena sombra le cobija', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(16, 'Agua que no has de beber, déjala correr', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(17, 'Quien siembra vientos, recoge tempestades', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(18, 'En casa del herrero, cuchillo de palo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(19, 'Matar dos pájaros de un tiro', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(20, 'No hay mal que por bien no venga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(21, 'Más vale prevenir que curar', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(22, 'Zapatero a tus zapatos', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(23, 'Quien tiene boca, se equivoca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(24, 'El hábito no hace al monje', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(25, 'Lo que no mata, engorda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(26, 'Roma no se hizo en un día', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(27, 'No hay mal que dure cien años', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(28, 'Más sabe el diablo por viejo que por diablo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(29, 'De tal palo, tal astilla', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(30, 'A falta de pan, buenas son tortas', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(31, 'Al que buen árbol se arrima, buena sombra le cobija', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(32, 'Cuando el río suena, agua lleva', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(33, 'Cada oveja con su pareja', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(34, 'Barriga llena, corazón contento', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(35, 'Al pan, pan, y al vino, vino', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(36, 'No hay peor sordo que el que no quiere oír', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(37, 'Quien mucho abarca, poco aprieta', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(38, 'Más vale tarde que nunca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(39, 'Crea fama y échate a dormir', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(40, 'No hay mal que por bien no venga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(41, 'Aunque la mona se vista de seda, mona se queda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(42, 'Dime de qué presumes y te diré de qué careces', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(43, 'El que la sigue, la consigue', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(44, 'Más vale solo que mal acompañado', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(45, 'En la variedad está el gusto', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(46, 'Cuando una puerta se cierra, otra se abre', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(47, 'No es oro todo lo que reluce', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(48, 'El que no arriesga, no gana', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(49, 'El tiempo lo cura todo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(50, 'La avaricia rompe el saco', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(51, 'A quien Dios se la dé, San Pedro se la bendiga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(52, 'El viaje de mil millas comienza con un solo paso', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(53, 'El que pregunta es tonto por cinco minutos, pero el que no pregunta sigue siendo tonto siempre', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(54, 'Si quieres que algo se haga, encárgalo a una persona ocupada', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(55, 'Saber y no hacer es no saber', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(56, 'Antes de iniciar la labor de cambiar el mundo, da tres vueltas por tu propia casa', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(57, 'No temas crecer lentamente, teme solo quedarte quieto', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(58, 'El agua demasiado pura no tiene peces', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(59, 'No importa lo lento que vayas, siempre y cuando no te detengas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(60, 'Los grandes árboles nacen de semillas pequeñas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(61, 'Un hombre sabio se adapta a las circunstancias como el agua se adapta a la forma del recipiente que la contiene', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(62, 'No puedes evitar que el pájaro de la tristeza vuele sobre tu cabeza, pero sí puedes evitar que anide en tu cabellera', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(63, 'El sabio no dice lo que sabe, y el necio no sabe lo que dice', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(64, 'El bambú que se dobla es más fuerte que el roble que resiste', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(65, 'Quien teme sufrir, ya sufre el temor', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(66, 'Las grandes almas tienen voluntades; las débiles solo deseos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(67, 'El que estudia diez años en la oscuridad será universalmente conocido como alguien que ha logrado el éxito en una sola vez', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(68, 'Un hombre sin sonrisa no debe abrir una tienda', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(69, 'Caer no es una derrota, rendirse sí lo es', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(70, 'Si te caes siete veces, levántate ocho', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(71, 'La enseñanza es el oficio del alma', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(72, 'Es fácil ser valiente desde una distancia segura', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(73, 'La paciencia es una flor que no crece en todos los jardines', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(74, 'Un problema no es para siempre', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(75, 'La puerta mejor cerrada es aquella que puede dejarse abierta', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(76, 'El que domina a los otros es fuerte; el que se domina a sí mismo es poderoso', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(77, 'Si das pescado a un hombre hambriento, le nutres una vez. Si le enseñas a pescar, le nutrirás toda su vida', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(78, 'El que no tiene enemigos, no tiene amigos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(79, 'El tiempo es un río que arrastra los días y las horas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(80, 'Mejor ser un perro en tiempos de paz, que un hombre en tiempos de guerra', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(81, 'No busques la verdad fuera de ti, está en tu interior', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(82, 'Una palabra amable no cuesta nada, pero vale mucho', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(83, 'La vida es realmente simple, pero insistimos en hacerla complicada', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(84, 'El jade necesita ser tallado antes de ser una joya', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(85, 'Es mejor encender una vela que maldecir la oscuridad', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(86, 'Los sabios no necesitan una larga explicación, los tontos no comprenderán por más que se les explique', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(87, 'La perseverancia supera la inteligencia', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(88, 'Si caminas solo, irás más rápido; si caminas acompañado, llegarás más lejos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(89, 'Cada sonrisa que se da a los demás, vuelve a ti', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(90, 'El hombre superior es modesto en su discurso, pero excede en sus acciones', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(91, 'Escucha el viento, y sabrás la dirección de tu destino', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(92, 'Aquel que sabe cuándo ha tenido suficiente es rico', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(93, 'Es mejor ser engañado que desconfiar de todos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(94, 'El que se aparta de la sabiduría envejece rápidamente', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(95, 'Quien no comprende una mirada, tampoco comprenderá una larga explicación', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(96, 'El silencio es un amigo que nunca traiciona', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(97, 'Es más fácil saber hacer una cosa que hacerla', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(98, 'Con una mentira se puede ir muy lejos, pero sin esperanzas de volver', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(99, 'El hombre que ha cometido un error y no lo corrige, comete otro error mayor', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(100, 'A palabras necias, oídos sordos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(101, 'Al que no le sobra pan, no críe can', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(102, 'Amor con amor se paga', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(103, 'Ande yo caliente y ríase la gente', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(104, 'A todo cerdo le llega su San Martín', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(105, 'Burro grande, ande o no ande', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(106, 'Cada loco con su tema', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(107, 'Coge la ocasión por los pelos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(108, 'Con pan y vino se anda el camino', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(109, 'Cuentas claras, amistades largas', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(110, 'Dar en el clavo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(111, 'De noche todos los gatos son pardos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(112, 'Del dicho al hecho hay mucho trecho', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(113, 'Dios los cría y ellos se juntan', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(114, 'Donde hay patrón no manda marinero', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(115, 'El buen paño en el arca se vende', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(116, 'El que no corre, vuela', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(117, 'El que no llora, no mama', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(118, 'El que tiene padrino, se bautiza', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(119, 'El que vive de ilusiones, muere de desengaños', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(120, 'En abril, aguas mil', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(121, 'En martes, ni te cases ni te embarques', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(122, 'En tiempos de guerra, cualquier agujero es trinchera', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(123, 'Entre broma y broma, la verdad asoma', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(124, 'Gato con guantes no caza ratones', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(125, 'Haz bien y no mires a quién', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(126, 'Hombre prevenido vale por dos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(127, 'La cabra siempre tira al monte', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(128, 'La dicha de la fea, la hermosa la desea', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(129, 'La mujer del César no solo debe ser honrada, sino parecerlo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(130, 'Las apariencias engañan', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(131, 'Lo cortés no quita lo valiente', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(132, 'Lo prometido es deuda', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(133, 'Loro viejo no aprende a hablar', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(134, 'Mal de muchos, consuelo de tontos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(135, 'Más vale maña que fuerza', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(136, 'Más vale un toma que dos te daré', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(137, 'Nadie es profeta en su tierra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(138, 'No hay enemigo pequeño', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(139, 'No hay que vender la piel del oso antes de cazarlo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(140, 'No se ganó Zamora en una hora', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(141, 'Nunca digas de esta agua no beberé', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(142, 'Perro que ladra no muerde', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(143, 'Quien algo quiere, algo le cuesta', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(144, 'Quien mucho habla, mucho yerra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(145, 'Quien ríe último, ríe mejor', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(146, 'Sarna con gusto no pica', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(147, 'Soldado avisado no muere en guerra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(148, 'Sobre gustos no hay nada escrito', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombreRol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

CREATE TABLE `roles_permisos` (
  `idPermiso` int(11) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `nombrePermiso` varchar(255) DEFAULT NULL,
  `tipoPermiso` enum('leer','escribir','eliminar') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacion`
--

CREATE TABLE `situacion` (
  `idSituacion` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `rango_fechas` enum('mes','semana','año','intervalo') DEFAULT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `total_gastos` decimal(10,2) NOT NULL,
  `total_ingresos` decimal(10,2) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenya` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `nivel_usuario` enum('superadmin','admin','usuario') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `idFamilia` int(11) DEFAULT NULL,
  `idGrupo` int(11) DEFAULT NULL,
  `estado_usuario` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `alias`, `email`, `contrasenya`, `fecha_nacimiento`, `telefono`, `nivel_usuario`, `fecha_registro`, `idFamilia`, `idGrupo`, `estado_usuario`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'superadmin@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1970-01-01', '625418965', 'superadmin', '2024-10-04 10:03:48', NULL, NULL, 'activo'),
(2, 'Admin1', 'Family1', 'admin1', 'admin1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1980-01-01', '625418965', 'admin', '2024-10-04 10:04:06', 1, 1, 'activo'),
(3, 'Admin2', 'Family2', 'admin2', 'admin2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1985-01-01', '625418965', 'admin', '2024-10-04 10:04:06', NULL, NULL, 'activo'),
(4, 'Admin3', 'Family3', 'admin3', 'admin3@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1990-01-01', '625418965', 'admin', '2024-10-04 10:04:06', NULL, NULL, 'activo'),
(5, 'Pareja1', 'Family1', 'pareja1', 'pareja1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1982-02-02', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(6, 'Hijo1', 'Family1', 'hijo1', 'hijo1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2005-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(7, 'Hijo2', 'Family1', 'hijo2', 'hijo2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2007-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(8, 'Hijo3', 'Family1', 'hijo3', 'hijo3@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2010-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(9, 'Amigo1', 'Grupo1', 'amigo1', 'amigo1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1990-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(10, 'Amigo2', 'Grupo1', 'amigo2', 'amigo2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1991-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(11, 'Amigo3', 'Grupo1', 'amigo3', 'amigo3@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1992-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(12, 'Normal1', 'Ind', 'normal1', 'normal1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1987-05-05', '625418965', 'usuario', '2024-10-04 10:06:02', NULL, NULL, 'activo'),
(13, 'Normal2', 'Ind', 'normal2', 'normal2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1988-05-05', '625418965', 'usuario', '2024-10-04 10:06:02', NULL, NULL, 'activo');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_usuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_delete` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'usuarios', OLD.idUser, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_update` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_usuario` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
  -- Validar si la familia existe
  IF NEW.idFamilia IS NOT NULL AND 
     (SELECT COUNT(*) FROM familias WHERE idFamilia = NEW.idFamilia) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La familia no existe';
  END IF;

  -- Validar si el grupo existe
  IF NEW.idGrupo IS NOT NULL AND 
     (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_familias`
--

CREATE TABLE `usuarios_familias` (
  `idUser` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_familias`
--

INSERT INTO `usuarios_familias` (`idUser`, `idFamilia`) VALUES
(2, 1),
(5, 1),
(6, 1);

--
-- Disparadores `usuarios_familias`
--
DELIMITER $$
CREATE TRIGGER `before_insert_usuarios_familias` BEFORE INSERT ON `usuarios_familias` FOR EACH ROW BEGIN
  IF (SELECT COUNT(*) FROM familias WHERE idFamilia = NEW.idFamilia) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La familia no existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `idUser` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`idUser`, `idGrupo`) VALUES
(2, 1),
(2, 2),
(9, 1),
(10, 1);

--
-- Disparadores `usuarios_grupos`
--
DELIMITER $$
CREATE TRIGGER `before_insert_usuarios_grupos` BEFORE INSERT ON `usuarios_grupos` FOR EACH ROW BEGIN
  IF (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idAdmin` (`idAdmin`,`idFamilia`);

--
-- Indices de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD PRIMARY KEY (`idAdmin`,`idGrupo`),
  ADD KEY `idGrupo` (`idGrupo`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`idAuditoria`);

--
-- Indices de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  ADD PRIMARY KEY (`idAcceso`),
  ADD KEY `idx_acceso_user_fecha` (`idUser`,`fecha`);

--
-- Indices de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  ADD PRIMARY KEY (`idAcceso`),
  ADD KEY `idx_acceso_user_fecha` (`idUser`,`fecha`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`idCategoria`),
  ADD UNIQUE KEY `nombreCategoria` (`nombreCategoria`,`creado_por`,`tipo_categoria`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`idConfig`),
  ADD UNIQUE KEY `clave_config` (`clave_config`);

--
-- Indices de la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  ADD PRIMARY KEY (`idEnvio`),
  ADD UNIQUE KEY `unique_envio` (`idUser`,`fecha_envio`,`momento`),
  ADD KEY `idRefran` (`idRefran`);

--
-- Indices de la tabla `familias`
--
ALTER TABLE `familias`
  ADD PRIMARY KEY (`idFamilia`),
  ADD UNIQUE KEY `nombre_familia` (`nombre_familia`),
  ADD KEY `idx_nombre_familia` (`nombre_familia`),
  ADD KEY `idx_familias_password` (`password`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idGasto`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `fk_gasto_familia` (`idFamilia`),
  ADD KEY `fk_gasto_grupo` (`idGrupo`),
  ADD KEY `idx_gastos_fecha_importe_concepto` (`fecha`,`importe`,`concepto`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`idGrupo`),
  ADD UNIQUE KEY `nombre_grupo` (`nombre_grupo`),
  ADD KEY `idx_nombre_grupo` (`nombre_grupo`),
  ADD KEY `idx_grupos_password` (`password`),
  ADD KEY `idx_grupos_nombre` (`nombre_grupo`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`idIngreso`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `idx_ingresos_fecha_importe` (`fecha`,`importe`),
  ADD KEY `idx_ingresos_concepto` (`concepto`),
  ADD KEY `idx_ingresos_fecha` (`fecha`),
  ADD KEY `idx_ingresos_importe_fecha` (`importe`,`fecha`),
  ADD KEY `fk_ingreso_familia` (`idFamilia`),
  ADD KEY `fk_ingreso_grupo` (`idGrupo`),
  ADD KEY `idx_ingresos_fecha_importe_concepto` (`fecha`,`importe`,`concepto`);

--
-- Indices de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD PRIMARY KEY (`idEnvio`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idRefran` (`idRefran`);

--
-- Indices de la tabla `refranes`
--
ALTER TABLE `refranes`
  ADD PRIMARY KEY (`idRefran`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD PRIMARY KEY (`idPermiso`),
  ADD KEY `fk_rol` (`idRol`);

--
-- Indices de la tabla `situacion`
--
ALTER TABLE `situacion`
  ADD PRIMARY KEY (`idSituacion`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idx_situacion_user_fechas` (`idUser`,`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_alias_email` (`alias`,`email`),
  ADD KEY `fk_familia` (`idFamilia`),
  ADD KEY `fk_grupo` (`idGrupo`),
  ADD KEY `idx_usuario_nivel` (`nivel_usuario`,`idGrupo`,`idFamilia`),
  ADD KEY `idx_usuarios_nivel_familia_grupo` (`nivel_usuario`,`idFamilia`,`idGrupo`),
  ADD KEY `idx_usuarios_nivel_fecha` (`nivel_usuario`,`fecha_registro`),
  ADD KEY `idx_usuarios_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_nombre` (`nombre`),
  ADD KEY `idx_usuarios_apellido` (`apellido`),
  ADD KEY `idx_usuarios_familia_grupo` (`idFamilia`,`idGrupo`),
  ADD KEY `idx_idUser` (`idUser`);

--
-- Indices de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD PRIMARY KEY (`idUser`,`idFamilia`),
  ADD KEY `fk_usuario_familia_familia` (`idFamilia`);

--
-- Indices de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`idUser`,`idGrupo`),
  ADD KEY `fk_usuario_grupo_grupo` (`idGrupo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `idIngreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `refranes`
--
ALTER TABLE `refranes`
  MODIFY `idRefran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `idPermiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `idSituacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD CONSTRAINT `fk_admin_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  ADD CONSTRAINT `fk_user_acceso` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  ADD CONSTRAINT `envio_refranes_ibfk_1` FOREIGN KEY (`idRefran`) REFERENCES `refranes` (`idRefran`),
  ADD CONSTRAINT `envio_refranes_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_gasto_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gasto_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `fk_ingreso_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ingreso_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ingresos_categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE SET NULL,
  ADD CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE SET NULL;

--
-- Filtros para la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD CONSTRAINT `news_letter_envios_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_letter_envios_ibfk_2` FOREIGN KEY (`idRefran`) REFERENCES `refranes` (`idRefran`);

--
-- Filtros para la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `situacion`
--
ALTER TABLE `situacion`
  ADD CONSTRAINT `situacion_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_usuario_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD CONSTRAINT `fk_usuario_familia_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario_familia_usuario` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_usuario_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario_grupo_usuario` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
