-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2024 a las 12:48:06
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

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `verificar_permiso_cacheado`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `verificar_permiso_cacheado` (IN `idUser` INT, IN `nombrePermiso` VARCHAR(255), IN `tipoPermiso` VARCHAR(255))   BEGIN
    DECLARE tiene_permiso TINYINT DEFAULT 0;

    -- Buscar permiso en la cache
    SELECT tiene_permiso 
    INTO tiene_permiso 
    FROM permisos_cache 
    WHERE idUser = idUser 
      AND nombrePermiso = nombrePermiso
      AND tipoPermiso = tipoPermiso
      AND TIMESTAMPDIFF(MINUTE, fecha_cache, NOW()) < 60;

    -- Si no existe en cache o está desactualizado, recalcular el permiso
    IF tiene_permiso IS NULL THEN
        SELECT COUNT(*)
        INTO tiene_permiso
        FROM usuarios u
        JOIN roles_permisos rp ON u.nivel_usuario = rp.idRol
        WHERE u.idUser = idUser
          AND rp.nombrePermiso = nombrePermiso
          AND rp.tipoPermiso = tipoPermiso;

        -- Actualizar la cache
        INSERT INTO permisos_cache (idUser, nombrePermiso, tipoPermiso, tiene_permiso)
        VALUES (idUser, nombrePermiso, tipoPermiso, tiene_permiso)
        ON DUPLICATE KEY UPDATE tiene_permiso = VALUES(tiene_permiso), fecha_cache = NOW();
    END IF;

    -- Devolver el resultado
    SELECT tiene_permiso;
END$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `verificar_permiso`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `verificar_permiso` (`idUser` INT, `tipoPermiso` VARCHAR(255), `nombrePermiso` VARCHAR(255)) RETURNS TINYINT(4) DETERMINISTIC BEGIN
    DECLARE tiene_permiso TINYINT DEFAULT 0;

    -- Verifica si el usuario tiene el permiso
    SELECT COUNT(*)
    INTO tiene_permiso
    FROM usuarios u
    JOIN roles_permisos rp ON u.nivel_usuario = rp.idRol
    WHERE u.idUser = idUser
      AND rp.nombrePermiso = nombrePermiso
      AND rp.tipoPermiso = tipoPermiso;

    RETURN tiene_permiso;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_familias`
--

DROP TABLE IF EXISTS `administradores_familias`;
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
(11, 16, 16),
(14, 20, 23),
(15, 20, 24),
(18, 27, 30),
(28, 39, 48),
(27, 60, 47),
(26, 61, 46),
(22, 65, 34),
(23, 65, 35),
(24, 65, 36),
(25, 65, 37),
(29, 70, 1),
(31, 75, 59),
(30, 75, 60),
(33, 87, 60),
(32, 87, 62),
(34, 89, 63),
(35, 96, 65),
(36, 103, 66),
(37, 106, 67),
(38, 110, 70),
(39, 112, 71),
(40, 116, 72),
(41, 118, 73),
(42, 118, 74),
(43, 119, 75),
(44, 119, 76),
(45, 119, 77),
(46, 120, 78),
(47, 120, 79),
(48, 120, 80),
(49, 120, 81),
(50, 120, 82),
(51, 127, 83),
(52, 127, 84),
(53, 127, 85),
(54, 128, 86),
(55, 128, 87),
(56, 130, 88),
(57, 131, 89),
(58, 132, 90),
(59, 132, 91),
(60, 133, 92),
(61, 133, 93),
(62, 134, 94),
(63, 135, 95),
(64, 135, 96);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_grupos`
--

DROP TABLE IF EXISTS `administradores_grupos`;
CREATE TABLE `administradores_grupos` (
  `idAdmin` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_grupos`
--

INSERT INTO `administradores_grupos` (`idAdmin`, `idGrupo`, `id`) VALUES
(2, 1, 1),
(65, 17, 2),
(65, 18, 3),
(65, 19, 4),
(65, 20, 5),
(75, 23, 6),
(75, 22, 7),
(87, 25, 8),
(87, 23, 9),
(89, 26, 10),
(96, 27, 11),
(103, 28, 12),
(106, 29, 13),
(110, 32, 14),
(112, 33, 15),
(116, 34, 16),
(119, 35, 17),
(119, 36, 18),
(119, 37, 19),
(120, 38, 20),
(120, 39, 21),
(120, 40, 22),
(120, 41, 23),
(120, 42, 24),
(120, 43, 25),
(120, 44, 26),
(120, 45, 27),
(120, 46, 28),
(120, 47, 29),
(127, 48, 30),
(127, 49, 31),
(127, 50, 32),
(128, 51, 33),
(128, 52, 34),
(130, 53, 35),
(131, 54, 36),
(132, 55, 37),
(132, 56, 38),
(133, 57, 39),
(133, 58, 40),
(135, 59, 41),
(135, 60, 42);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
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
(96, 'UPDATE', 'gastos', 15, '2', '2024-10-07 16:06:24'),
(97, 'UPDATE', 'gastos', 2, '2', '2024-10-08 17:19:53'),
(98, 'UPDATE', 'gastos', 3, '2', '2024-10-08 17:19:53'),
(99, 'UPDATE', 'gastos', 4, '2', '2024-10-08 17:19:53'),
(100, 'UPDATE', 'gastos', 5, '2', '2024-10-08 17:19:53'),
(101, 'UPDATE', 'gastos', 6, '2', '2024-10-08 17:19:53'),
(102, 'UPDATE', 'gastos', 7, '2', '2024-10-08 17:19:53'),
(103, 'UPDATE', 'gastos', 8, '2', '2024-10-08 17:19:53'),
(104, 'UPDATE', 'ingresos', 1, '2', '2024-10-08 17:20:53'),
(105, 'UPDATE', 'ingresos', 2, '2', '2024-10-08 17:20:53'),
(106, 'UPDATE', 'ingresos', 3, '2', '2024-10-08 17:20:53'),
(107, 'UPDATE', 'ingresos', 4, '2', '2024-10-08 17:20:53'),
(108, 'UPDATE', 'ingresos', 5, '2', '2024-10-08 17:20:53'),
(109, 'UPDATE', 'ingresos', 6, '2', '2024-10-08 17:20:53'),
(110, 'UPDATE', 'ingresos', 7, '2', '2024-10-08 17:20:53'),
(111, 'UPDATE', 'ingresos', 8, '2', '2024-10-08 17:20:53'),
(112, 'UPDATE', 'gastos', 9, '3', '2024-10-08 17:22:01'),
(113, 'UPDATE', 'gastos', 10, '3', '2024-10-08 17:22:24'),
(114, 'UPDATE', 'gastos', 11, '3', '2024-10-08 17:24:28'),
(115, 'UPDATE', 'gastos', 12, '3', '2024-10-08 17:24:28'),
(116, 'UPDATE', 'gastos', 13, '3', '2024-10-08 17:24:28'),
(117, 'UPDATE', 'ingresos', 9, '3', '2024-10-08 17:24:42'),
(118, 'UPDATE', 'ingresos', 10, '3', '2024-10-08 17:24:42'),
(119, 'UPDATE', 'ingresos', 11, '3', '2024-10-08 17:24:42'),
(120, 'UPDATE', 'ingresos', 12, '3', '2024-10-08 17:24:42'),
(121, 'UPDATE', 'ingresos', 13, '3', '2024-10-08 17:24:42'),
(122, 'UPDATE', 'usuarios', 3, '3', '2024-10-08 17:43:14'),
(123, 'UPDATE', 'usuarios', 4, '4', '2024-10-08 17:43:14'),
(124, 'INSERT', 'gastos', 16, '3', '2024-10-08 17:43:32'),
(125, 'INSERT', 'ingresos', 15, '4', '2024-10-08 17:46:09'),
(126, 'INSERT', 'gastos', 17, '2', '2024-10-08 17:57:28'),
(127, 'INSERT', 'ingresos', 16, '3', '2024-10-08 17:57:43'),
(128, 'UPDATE', 'ingresos', 1, '2', '2024-10-08 18:23:01'),
(129, 'UPDATE', 'ingresos', 4, '2', '2024-10-08 18:23:01'),
(130, 'UPDATE', 'ingresos', 9, '3', '2024-10-08 18:23:01'),
(131, 'UPDATE', 'usuarios', 2, '2', '2024-10-08 18:47:47'),
(132, 'DELETE', 'usuarios', 3, '3', '2024-10-08 19:34:46'),
(133, 'DELETE', 'usuarios', 4, '4', '2024-10-08 19:34:52'),
(134, 'DELETE', 'gastos', 2, '2', '2024-10-08 19:37:50'),
(135, 'DELETE', 'gastos', 3, '2', '2024-10-08 19:37:50'),
(136, 'DELETE', 'gastos', 4, '2', '2024-10-08 19:37:50'),
(137, 'DELETE', 'gastos', 5, '2', '2024-10-08 19:37:50'),
(138, 'DELETE', 'gastos', 6, '2', '2024-10-08 19:37:50'),
(139, 'DELETE', 'gastos', 7, '2', '2024-10-08 19:37:50'),
(140, 'DELETE', 'gastos', 8, '2', '2024-10-08 19:37:50'),
(141, 'DELETE', 'gastos', 9, '3', '2024-10-08 19:37:50'),
(142, 'DELETE', 'gastos', 10, '3', '2024-10-08 19:37:50'),
(143, 'DELETE', 'gastos', 11, '3', '2024-10-08 19:37:50'),
(144, 'DELETE', 'gastos', 12, '3', '2024-10-08 19:37:50'),
(145, 'DELETE', 'gastos', 13, '3', '2024-10-08 19:37:50'),
(146, 'DELETE', 'gastos', 14, '2', '2024-10-08 19:37:50'),
(147, 'DELETE', 'gastos', 15, '2', '2024-10-08 19:37:50'),
(148, 'DELETE', 'gastos', 16, '3', '2024-10-08 19:37:50'),
(149, 'DELETE', 'gastos', 17, '2', '2024-10-08 19:37:50'),
(150, 'DELETE', 'ingresos', 1, '2', '2024-10-08 19:38:10'),
(151, 'DELETE', 'ingresos', 2, '2', '2024-10-08 19:38:10'),
(152, 'DELETE', 'ingresos', 3, '2', '2024-10-08 19:38:10'),
(153, 'DELETE', 'ingresos', 4, '2', '2024-10-08 19:38:10'),
(154, 'DELETE', 'ingresos', 5, '2', '2024-10-08 19:38:10'),
(155, 'DELETE', 'ingresos', 6, '2', '2024-10-08 19:38:10'),
(156, 'DELETE', 'ingresos', 7, '2', '2024-10-08 19:38:10'),
(157, 'DELETE', 'ingresos', 8, '2', '2024-10-08 19:38:10'),
(158, 'UPDATE', 'ingresos', 9, '2', '2024-10-08 19:39:40'),
(159, 'UPDATE', 'ingresos', 10, '2', '2024-10-08 19:39:54'),
(160, 'UPDATE', 'ingresos', 11, '2', '2024-10-08 19:40:05'),
(161, 'UPDATE', 'ingresos', 13, '2', '2024-10-08 19:40:18'),
(162, 'UPDATE', 'ingresos', 12, '2', '2024-10-08 19:40:31'),
(163, 'UPDATE', 'ingresos', 15, '2', '2024-10-08 19:40:42'),
(164, 'UPDATE', 'ingresos', 16, '2', '2024-10-08 19:40:54'),
(165, 'DELETE', 'ingresos', 9, '2', '2024-10-08 19:41:13'),
(166, 'DELETE', 'ingresos', 10, '2', '2024-10-08 19:41:13'),
(167, 'DELETE', 'ingresos', 11, '2', '2024-10-08 19:41:13'),
(168, 'DELETE', 'ingresos', 12, '2', '2024-10-08 19:41:13'),
(169, 'DELETE', 'ingresos', 13, '2', '2024-10-08 19:41:13'),
(170, 'DELETE', 'ingresos', 15, '2', '2024-10-08 19:41:13'),
(171, 'DELETE', 'ingresos', 16, '2', '2024-10-08 19:41:13'),
(172, 'DELETE', 'usuarios', 12, '12', '2024-10-08 19:49:41'),
(173, 'DELETE', 'usuarios', 13, '13', '2024-10-08 19:49:41'),
(174, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 19:52:33'),
(175, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 19:52:40'),
(176, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 20:09:40'),
(177, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 20:09:52'),
(178, 'INSERT', 'categorias', 17, '2', '2024-10-08 20:23:39'),
(179, 'INSERT', 'categorias', 18, '2', '2024-10-08 20:23:53'),
(180, 'INSERT', 'categorias', 19, '2', '2024-10-08 20:24:02'),
(181, 'INSERT', 'categorias', 20, '2', '2024-10-08 20:24:08'),
(182, 'INSERT', 'categorias', 23, '2', '2024-10-08 20:25:51'),
(183, 'INSERT', 'categorias', 24, '2', '2024-10-08 20:26:00'),
(184, 'INSERT', 'categorias', 25, '2', '2024-10-08 20:26:05'),
(185, 'INSERT', 'categorias', 26, '2', '2024-10-08 20:26:09'),
(186, 'INSERT', 'categorias', 29, '2', '2024-10-08 20:49:33'),
(187, 'INSERT', 'categorias', 30, '2', '2024-10-08 20:49:41'),
(188, 'INSERT', 'categorias', 31, '2', '2024-10-08 20:49:46'),
(189, 'INSERT', 'categorias', 32, '2', '2024-10-08 20:49:56'),
(190, 'INSERT', 'gastos', 18, '2', '2024-10-08 20:59:42'),
(191, 'INSERT', 'gastos', 19, '2', '2024-10-08 21:00:25'),
(192, 'INSERT', 'gastos', 20, '2', '2024-10-08 21:00:54'),
(193, 'INSERT', 'gastos', 21, '2', '2024-10-08 21:01:19'),
(194, 'INSERT', 'ingresos', 17, '2', '2024-10-08 21:04:31'),
(195, 'INSERT', 'ingresos', 18, '2', '2024-10-08 21:05:12'),
(196, 'INSERT', 'ingresos', 19, '2', '2024-10-08 21:05:42'),
(197, 'INSERT', 'ingresos', 20, '2', '2024-10-08 21:06:05'),
(198, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 23:53:27'),
(199, 'UPDATE', 'usuarios', 1, '1', '2024-10-10 14:06:43'),
(200, 'UPDATE', 'usuarios', 11, '11', '2024-10-10 14:49:32'),
(201, 'INSERT', 'usuarios', 15, '15', '2024-10-10 16:24:33'),
(202, 'INSERT', 'usuarios', 15, '15', '2024-10-10 16:24:33'),
(203, 'INSERT', 'usuarios', 15, 'sistema', '2024-10-10 16:24:33'),
(204, 'UPDATE', 'usuarios', 15, '15', '2024-10-10 16:30:27'),
(205, 'DELETE', 'usuarios', 15, '15', '2024-10-10 16:30:38'),
(206, 'INSERT', 'usuarios', 16, '16', '2024-10-10 16:31:32'),
(207, 'INSERT', 'usuarios', 16, '16', '2024-10-10 16:31:32'),
(208, 'INSERT', 'usuarios', 16, 'sistema', '2024-10-10 16:31:32'),
(209, 'UPDATE', 'usuarios', 16, '16', '2024-10-10 16:31:43'),
(210, 'INSERT', 'familias', 7, 'sistema', '2024-10-10 17:12:31'),
(211, 'delete', 'familias', 7, '', '2024-10-10 17:48:13'),
(212, 'INSERT', 'familias', 8, 'sistema', '2024-10-10 17:49:13'),
(213, 'delete', 'familias', 8, '', '2024-10-10 18:12:47'),
(214, 'INSERT', 'familias', 9, 'sistema', '2024-10-10 18:32:53'),
(215, 'INSERT', 'familias', 10, 'sistema', '2024-10-11 08:04:44'),
(216, 'INSERT', 'familias', 11, 'sistema', '2024-10-11 11:10:25'),
(217, 'delete', 'familias', 9, '', '2024-10-11 12:50:17'),
(218, 'delete', 'familias', 10, '', '2024-10-11 12:50:20'),
(219, 'delete', 'familias', 11, '', '2024-10-11 12:50:23'),
(220, 'INSERT', 'familias', 12, 'sistema', '2024-10-11 13:04:03'),
(221, 'delete', 'familias', 12, '', '2024-10-11 13:46:21'),
(222, 'INSERT', 'familias', 13, 'sistema', '2024-10-11 13:47:29'),
(223, 'delete', 'familias', 13, '', '2024-10-11 13:49:41'),
(224, 'INSERT', 'familias', 14, 'sistema', '2024-10-11 13:50:02'),
(225, 'INSERT', 'familias', 15, 'sistema', '2024-10-11 14:05:01'),
(226, 'delete', 'familias', 14, '', '2024-10-11 15:05:55'),
(227, 'delete', 'familias', 15, '', '2024-10-11 15:05:58'),
(228, 'INSERT', 'familias', 16, 'sistema', '2024-10-11 15:07:27'),
(229, 'INSERT', 'familias', 17, 'sistema', '2024-10-11 16:29:11'),
(230, 'INSERT', 'familias', 18, 'sistema', '2024-10-11 17:01:50'),
(231, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:02:16'),
(232, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:03:47'),
(233, 'UPDATE', 'familias', 17, 'sistema', '2024-10-11 17:04:10'),
(234, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:04:21'),
(235, 'INSERT', 'familias', 19, 'sistema', '2024-10-11 17:05:17'),
(236, 'UPDATE', 'familias', 19, 'sistema', '2024-10-11 17:05:30'),
(237, 'INSERT', 'usuarios', 17, '17', '2024-10-12 11:13:29'),
(238, 'INSERT', 'usuarios', 17, '17', '2024-10-12 11:13:29'),
(239, 'INSERT', 'usuarios', 17, 'sistema', '2024-10-12 11:13:29'),
(240, 'INSERT', 'grupos', 9, 'Sistema', '2024-10-12 11:58:29'),
(241, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:20:59'),
(242, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:21:29'),
(243, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:21:46'),
(244, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:22:01'),
(245, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:23:12'),
(246, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:57:39'),
(247, 'INSERT', 'grupos', 10, 'Sistema', '2024-10-12 12:58:27'),
(248, 'INSERT', 'familias', 20, 'sistema', '2024-10-12 12:59:39'),
(249, 'UPDATE', 'familias', 20, 'sistema', '2024-10-12 12:59:49'),
(250, 'UPDATE', 'grupos', 10, 'Sistema', '2024-10-12 14:09:48'),
(251, 'DELETE', 'grupos', 10, '', '2024-10-12 14:09:55'),
(252, 'INSERT', 'grupos', 11, 'Sistema', '2024-10-12 14:10:06'),
(253, 'INSERT', 'categorias', 33, '1', '2024-10-12 16:32:52'),
(254, 'INSERT', 'categorias', 35, '1', '2024-10-12 16:33:49'),
(255, 'INSERT', 'categorias', 36, '1', '2024-10-12 16:48:43'),
(256, 'INSERT', 'categorias', 37, '1', '2024-10-12 16:49:14'),
(257, 'INSERT', 'categorias', 38, '1', '2024-10-12 16:50:23'),
(258, 'INSERT', 'categorias', 39, '1', '2024-10-12 16:50:41'),
(259, 'UPDATE', 'usuarios', 17, '17', '2024-10-12 16:53:00'),
(260, 'INSERT', 'usuarios', 18, '18', '2024-10-12 17:42:45'),
(261, 'INSERT', 'usuarios', 18, '18', '2024-10-12 17:42:45'),
(262, 'INSERT', 'usuarios', 18, 'sistema', '2024-10-12 17:42:45'),
(263, 'UPDATE', 'usuarios', 18, '18', '2024-10-12 18:02:40'),
(264, 'UPDATE', 'usuarios', 18, '18', '2024-10-12 18:03:01'),
(265, 'UPDATE', 'usuarios', 11, '11', '2024-10-12 18:04:16'),
(266, 'INSERT', 'familias', 21, 'sistema', '2024-10-12 18:28:50'),
(267, 'INSERT', 'usuarios', 19, '19', '2024-10-12 18:28:50'),
(268, 'INSERT', 'usuarios', 19, '19', '2024-10-12 18:28:50'),
(269, 'INSERT', 'usuarios', 19, 'sistema', '2024-10-12 18:28:50'),
(270, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 08:00:16'),
(271, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 08:00:55'),
(272, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 08:01:42'),
(273, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 08:02:07'),
(274, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 08:17:31'),
(275, 'UPDATE', 'familias', 21, 'sistema', '2024-10-13 08:18:08'),
(276, 'INSERT', 'grupos', 12, 'Sistema', '2024-10-13 08:22:55'),
(277, 'INSERT', 'grupos', 13, 'Sistema', '2024-10-13 08:28:06'),
(278, 'INSERT', 'usuarios', 20, '20', '2024-10-13 08:28:06'),
(279, 'INSERT', 'usuarios', 20, '20', '2024-10-13 08:28:06'),
(280, 'INSERT', 'usuarios', 20, 'sistema', '2024-10-13 08:28:06'),
(281, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 08:31:55'),
(282, 'INSERT', 'familias', 22, 'sistema', '2024-10-13 08:34:52'),
(283, 'INSERT', 'usuarios', 21, '21', '2024-10-13 08:34:52'),
(284, 'INSERT', 'usuarios', 21, '21', '2024-10-13 08:34:52'),
(285, 'INSERT', 'usuarios', 21, 'sistema', '2024-10-13 08:34:52'),
(286, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 08:38:59'),
(287, 'delete', 'familias', 17, '', '2024-10-13 08:44:28'),
(288, 'delete', 'familias', 21, '', '2024-10-13 08:44:42'),
(289, 'delete', 'familias', 19, '', '2024-10-13 08:44:49'),
(290, 'delete', 'familias', 18, '', '2024-10-13 08:44:54'),
(291, 'delete', 'familias', 20, '', '2024-10-13 08:44:59'),
(292, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 08:45:31'),
(293, 'UPDATE', 'familias', 1, 'sistema', '2024-10-13 14:01:26'),
(294, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 14:01:26'),
(295, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 14:01:26'),
(296, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-13 14:01:26'),
(297, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-13 14:01:26'),
(298, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 14:01:26'),
(299, 'UPDATE', 'grupos', 12, 'Sistema', '2024-10-13 14:01:26'),
(300, 'UPDATE', 'grupos', 13, 'Sistema', '2024-10-13 14:01:26'),
(301, 'UPDATE', 'usuarios', 1, '1', '2024-10-13 14:01:26'),
(302, 'UPDATE', 'usuarios', 2, '2', '2024-10-13 14:01:26'),
(303, 'UPDATE', 'usuarios', 5, '5', '2024-10-13 14:01:26'),
(304, 'UPDATE', 'usuarios', 6, '6', '2024-10-13 14:01:26'),
(305, 'UPDATE', 'usuarios', 7, '7', '2024-10-13 14:01:26'),
(306, 'UPDATE', 'usuarios', 8, '8', '2024-10-13 14:01:26'),
(307, 'UPDATE', 'usuarios', 9, '9', '2024-10-13 14:01:26'),
(308, 'UPDATE', 'usuarios', 10, '10', '2024-10-13 14:01:26'),
(309, 'UPDATE', 'usuarios', 11, '11', '2024-10-13 14:01:26'),
(310, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 14:01:26'),
(311, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 14:01:26'),
(312, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 14:01:26'),
(313, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 14:01:26'),
(314, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:01:26'),
(315, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:01:26'),
(316, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:02:17'),
(317, 'INSERT', 'familias', 23, 'sistema', '2024-10-13 14:03:11'),
(318, 'INSERT', 'familias', 24, 'sistema', '2024-10-13 14:07:18'),
(319, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:10:30'),
(320, 'UPDATE', 'familias', 23, 'sistema', '2024-10-13 14:12:50'),
(321, 'UPDATE', 'familias', 24, 'sistema', '2024-10-13 14:13:01'),
(322, 'INSERT', 'familias', 25, 'sistema', '2024-10-13 14:37:54'),
(323, 'INSERT', 'usuarios', 22, '22', '2024-10-13 14:37:54'),
(324, 'INSERT', 'usuarios', 22, '22', '2024-10-13 14:37:54'),
(325, 'INSERT', 'usuarios', 22, 'sistema', '2024-10-13 14:37:54'),
(326, 'UPDATE', 'familias', 1, 'sistema', '2024-10-13 14:39:01'),
(327, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 14:39:01'),
(328, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 14:39:01'),
(329, 'UPDATE', 'familias', 23, 'sistema', '2024-10-13 14:39:01'),
(330, 'UPDATE', 'familias', 24, 'sistema', '2024-10-13 14:39:01'),
(331, 'UPDATE', 'familias', 25, 'sistema', '2024-10-13 14:39:01'),
(332, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-13 14:39:01'),
(333, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-13 14:39:01'),
(334, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 14:39:01'),
(335, 'UPDATE', 'grupos', 12, 'Sistema', '2024-10-13 14:39:01'),
(336, 'UPDATE', 'grupos', 13, 'Sistema', '2024-10-13 14:39:01'),
(337, 'UPDATE', 'usuarios', 1, '1', '2024-10-13 14:39:01'),
(338, 'UPDATE', 'usuarios', 2, '2', '2024-10-13 14:39:01'),
(339, 'UPDATE', 'usuarios', 5, '5', '2024-10-13 14:39:01'),
(340, 'UPDATE', 'usuarios', 6, '6', '2024-10-13 14:39:01'),
(341, 'UPDATE', 'usuarios', 7, '7', '2024-10-13 14:39:01'),
(342, 'UPDATE', 'usuarios', 8, '8', '2024-10-13 14:39:01'),
(343, 'UPDATE', 'usuarios', 9, '9', '2024-10-13 14:39:01'),
(344, 'UPDATE', 'usuarios', 10, '10', '2024-10-13 14:39:01'),
(345, 'UPDATE', 'usuarios', 11, '11', '2024-10-13 14:39:01'),
(346, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 14:39:01'),
(347, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 14:39:01'),
(348, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 14:39:01'),
(349, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 14:39:01'),
(350, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:39:01'),
(351, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:39:01'),
(352, 'UPDATE', 'usuarios', 22, '22', '2024-10-13 14:39:01'),
(353, 'INSERT', 'usuarios', 23, '23', '2024-10-13 14:44:17'),
(354, 'INSERT', 'usuarios', 23, '23', '2024-10-13 14:44:17'),
(355, 'INSERT', 'usuarios', 23, 'sistema', '2024-10-13 14:44:17'),
(356, 'INSERT', 'familias', 26, 'sistema', '2024-10-13 15:45:19'),
(357, 'INSERT', 'usuarios', 24, '24', '2024-10-13 15:59:43'),
(358, 'INSERT', 'usuarios', 24, '24', '2024-10-13 15:59:43'),
(359, 'INSERT', 'usuarios', 24, 'sistema', '2024-10-13 15:59:43'),
(360, 'INSERT', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(361, 'INSERT', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(362, 'INSERT', 'usuarios', 25, 'sistema', '2024-10-13 17:07:26'),
(363, 'INSERT', 'familias', 28, 'sistema', '2024-10-13 17:07:26'),
(364, 'UPDATE', 'familias', 28, 'sistema', '2024-10-13 17:07:26'),
(365, 'UPDATE', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(366, 'UPDATE', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(367, 'INSERT', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(368, 'INSERT', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(369, 'INSERT', 'usuarios', 26, 'sistema', '2024-10-13 17:39:57'),
(370, 'INSERT', 'familias', 29, 'sistema', '2024-10-13 17:39:57'),
(371, 'UPDATE', 'familias', 29, 'sistema', '2024-10-13 17:39:57'),
(372, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(373, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(374, 'INSERT', 'categorias', 40, '26', '2024-10-13 17:43:49'),
(375, 'INSERT', 'categorias', 41, '26', '2024-10-13 17:44:16'),
(376, 'DELETE', 'usuarios', 25, '25', '2024-10-13 18:17:41'),
(377, 'DELETE', 'usuarios', 24, '24', '2024-10-13 18:17:46'),
(378, 'delete', 'familias', 26, '', '2024-10-13 18:18:22'),
(379, 'delete', 'familias', 28, '', '2024-10-13 18:18:25'),
(380, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 18:19:46'),
(381, 'delete', 'familias', 29, '', '2024-10-13 18:20:47'),
(382, 'delete', 'familias', 25, '', '2024-10-13 18:20:57'),
(383, 'DELETE', 'grupos', 12, '', '2024-10-13 18:21:14'),
(384, 'DELETE', 'grupos', 13, '', '2024-10-13 18:21:17'),
(385, 'DELETE', 'usuarios', 22, '22', '2024-10-13 18:22:18'),
(386, 'DELETE', 'usuarios', 23, '23', '2024-10-13 18:22:18'),
(387, 'INSERT', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(388, 'INSERT', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(389, 'INSERT', 'usuarios', 27, 'sistema', '2024-10-13 18:28:47'),
(390, 'INSERT', 'familias', 30, 'sistema', '2024-10-13 18:28:47'),
(391, 'UPDATE', 'familias', 30, 'sistema', '2024-10-13 18:28:47'),
(392, 'UPDATE', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(393, 'UPDATE', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(394, 'INSERT', 'gastos', 22, '27', '2024-10-13 18:42:54'),
(395, 'INSERT', 'gastos', 23, '27', '2024-10-13 18:49:01'),
(396, 'INSERT', 'gastos', 24, '27', '2024-10-13 19:07:20'),
(397, 'INSERT', 'gastos', 25, '27', '2024-10-13 19:14:43'),
(398, 'INSERT', 'gastos', 26, '27', '2024-10-13 19:27:37'),
(399, 'INSERT', 'gastos', 27, '27', '2024-10-13 19:41:43'),
(400, 'INSERT', 'gastos', 28, '27', '2024-10-13 19:50:15'),
(401, 'INSERT', 'ingresos', 22, '27', '2024-10-13 21:49:23'),
(402, 'INSERT', 'ingresos', 23, '27', '2024-10-13 21:52:30'),
(403, 'INSERT', 'ingresos', 24, '27', '2024-10-13 21:53:00'),
(404, 'INSERT', 'categorias', 42, '27', '2024-10-13 21:54:13'),
(405, 'INSERT', 'usuarios', 28, '28', '2024-10-14 16:28:08'),
(406, 'INSERT', 'usuarios', 28, '28', '2024-10-14 16:28:08'),
(407, 'INSERT', 'usuarios', 28, 'sistema', '2024-10-14 16:28:08'),
(408, 'UPDATE', 'familias', 1, 'sistema', '2024-10-14 16:28:24'),
(409, 'UPDATE', 'familias', 16, 'sistema', '2024-10-14 16:28:24'),
(410, 'UPDATE', 'familias', 22, 'sistema', '2024-10-14 16:28:24'),
(411, 'UPDATE', 'familias', 23, 'sistema', '2024-10-14 16:28:24'),
(412, 'UPDATE', 'familias', 24, 'sistema', '2024-10-14 16:28:24'),
(413, 'UPDATE', 'familias', 30, 'sistema', '2024-10-14 16:28:24'),
(414, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-14 16:28:24'),
(415, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-14 16:28:24'),
(416, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-14 16:28:24'),
(417, 'UPDATE', 'usuarios', 1, '1', '2024-10-14 16:28:24'),
(418, 'UPDATE', 'usuarios', 2, '2', '2024-10-14 16:28:24'),
(419, 'UPDATE', 'usuarios', 5, '5', '2024-10-14 16:28:24'),
(420, 'UPDATE', 'usuarios', 6, '6', '2024-10-14 16:28:24'),
(421, 'UPDATE', 'usuarios', 7, '7', '2024-10-14 16:28:24'),
(422, 'UPDATE', 'usuarios', 8, '8', '2024-10-14 16:28:24'),
(423, 'UPDATE', 'usuarios', 9, '9', '2024-10-14 16:28:24'),
(424, 'UPDATE', 'usuarios', 10, '10', '2024-10-14 16:28:24'),
(425, 'UPDATE', 'usuarios', 11, '11', '2024-10-14 16:28:24'),
(426, 'UPDATE', 'usuarios', 16, '16', '2024-10-14 16:28:24'),
(427, 'UPDATE', 'usuarios', 17, '17', '2024-10-14 16:28:24'),
(428, 'UPDATE', 'usuarios', 18, '18', '2024-10-14 16:28:24'),
(429, 'UPDATE', 'usuarios', 19, '19', '2024-10-14 16:28:24'),
(430, 'UPDATE', 'usuarios', 20, '20', '2024-10-14 16:28:24'),
(431, 'UPDATE', 'usuarios', 21, '21', '2024-10-14 16:28:24'),
(432, 'UPDATE', 'usuarios', 26, '26', '2024-10-14 16:28:24'),
(433, 'UPDATE', 'usuarios', 27, '27', '2024-10-14 16:28:24'),
(434, 'UPDATE', 'usuarios', 28, '28', '2024-10-14 16:28:24'),
(435, 'INSERT', 'usuarios', 29, '29', '2024-10-14 16:30:53'),
(436, 'INSERT', 'usuarios', 29, '29', '2024-10-14 16:30:53'),
(437, 'INSERT', 'usuarios', 29, 'sistema', '2024-10-14 16:30:53'),
(438, 'UPDATE', 'usuarios', 29, '29', '2024-10-14 16:30:54'),
(439, 'INSERT', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(440, 'INSERT', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(441, 'INSERT', 'usuarios', 30, 'sistema', '2024-10-14 17:24:50'),
(442, 'UPDATE', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(443, 'INSERT', 'usuarios', 31, '31', '2024-10-14 18:11:36'),
(444, 'INSERT', 'usuarios', 31, '31', '2024-10-14 18:11:36'),
(445, 'INSERT', 'usuarios', 31, 'sistema', '2024-10-14 18:11:36'),
(446, 'UPDATE', 'usuarios', 31, '31', '2024-10-14 18:11:37'),
(447, 'UPDATE', 'usuarios', 31, '31', '2024-10-14 18:11:37'),
(448, 'INSERT', 'ingresos', 25, '31', '2024-10-14 18:18:30'),
(449, 'INSERT', 'ingresos', 26, '31', '2024-10-14 18:19:11'),
(450, 'INSERT', 'gastos', 29, '31', '2024-10-14 18:19:50'),
(451, 'INSERT', 'gastos', 30, '31', '2024-10-14 18:21:01'),
(452, 'DELETE', 'ingresos', 22, '27', '2024-10-15 17:11:45'),
(453, 'UPDATE', 'usuarios', 19, '19', '2024-10-16 16:14:53'),
(454, 'UPDATE', 'usuarios', 18, '18', '2024-10-16 16:15:24'),
(455, 'DELETE', 'usuarios', 17, '17', '2024-10-18 16:53:32'),
(456, 'INSERT', 'usuarios', 32, '32', '2024-10-20 09:21:21'),
(457, 'INSERT', 'usuarios', 32, '32', '2024-10-20 09:21:21'),
(458, 'INSERT', 'usuarios', 32, 'sistema', '2024-10-20 09:21:21'),
(459, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:21:37'),
(460, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:21:37'),
(461, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:21:37'),
(462, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:21:37'),
(463, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:21:37'),
(464, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:21:37'),
(465, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:21:37'),
(466, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:21:37'),
(467, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:21:37'),
(468, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:21:37'),
(469, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:21:37'),
(470, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:21:37'),
(471, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:21:37'),
(472, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:21:37'),
(473, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:21:37'),
(474, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:21:37'),
(475, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:21:37'),
(476, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:37'),
(477, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:37'),
(478, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:21:37'),
(479, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:21:37'),
(480, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:37'),
(481, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:37'),
(482, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(483, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(484, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(485, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:21:37'),
(486, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:21:37'),
(487, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:21:37'),
(488, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:21:37'),
(489, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:21:37'),
(490, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:21:37'),
(491, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:21:37'),
(492, 'UPDATE', 'usuarios', 32, '32', '2024-10-20 09:21:37'),
(493, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:21:39'),
(494, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:21:39'),
(495, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:21:39'),
(496, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:21:39'),
(497, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:21:39'),
(498, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:21:39'),
(499, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:21:39'),
(500, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:21:39'),
(501, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:21:39'),
(502, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:21:39'),
(503, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:21:39'),
(504, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:21:39'),
(505, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:21:39'),
(506, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:21:39'),
(507, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:21:39'),
(508, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:21:39'),
(509, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:21:39'),
(510, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:39'),
(511, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:39'),
(512, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:21:39'),
(513, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:21:39'),
(514, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:39'),
(515, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:39'),
(516, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(517, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(518, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(519, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:21:39'),
(520, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:21:39'),
(521, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:21:39'),
(522, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:21:39'),
(523, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:21:39'),
(524, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:21:39'),
(525, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:21:39'),
(526, 'UPDATE', 'usuarios', 32, '32', '2024-10-20 09:21:39'),
(527, 'DELETE', 'usuarios', 32, '32', '2024-10-20 09:24:17'),
(528, 'INSERT', 'usuarios', 33, '33', '2024-10-20 09:25:11'),
(529, 'INSERT', 'usuarios', 33, '33', '2024-10-20 09:25:11'),
(530, 'INSERT', 'usuarios', 33, 'sistema', '2024-10-20 09:25:11'),
(531, 'INSERT', 'usuarios', 34, '34', '2024-10-20 09:27:59'),
(532, 'INSERT', 'usuarios', 34, '34', '2024-10-20 09:27:59'),
(533, 'INSERT', 'usuarios', 34, 'sistema', '2024-10-20 09:27:59'),
(534, 'INSERT', 'familias', 31, 'sistema', '2024-10-20 09:27:59'),
(535, 'INSERT', 'usuarios', 35, '35', '2024-10-20 09:29:23'),
(536, 'INSERT', 'usuarios', 35, '35', '2024-10-20 09:29:23'),
(537, 'INSERT', 'usuarios', 35, 'sistema', '2024-10-20 09:29:23'),
(538, 'INSERT', 'usuarios', 36, '36', '2024-10-20 09:31:36'),
(539, 'INSERT', 'usuarios', 36, '36', '2024-10-20 09:31:36'),
(540, 'INSERT', 'usuarios', 36, 'sistema', '2024-10-20 09:31:36'),
(541, 'INSERT', 'usuarios', 37, '37', '2024-10-20 09:34:04'),
(542, 'INSERT', 'usuarios', 37, '37', '2024-10-20 09:34:04'),
(543, 'INSERT', 'usuarios', 37, 'sistema', '2024-10-20 09:34:04'),
(544, 'INSERT', 'grupos', 14, 'Sistema', '2024-10-20 09:34:04'),
(545, 'INSERT', 'usuarios', 38, '38', '2024-10-20 09:36:05'),
(546, 'INSERT', 'usuarios', 38, '38', '2024-10-20 09:36:05'),
(547, 'INSERT', 'usuarios', 38, 'sistema', '2024-10-20 09:36:05'),
(548, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:36:20'),
(549, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:36:20'),
(550, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:36:20'),
(551, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:36:20'),
(552, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:36:20'),
(553, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:36:20'),
(554, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 09:36:20'),
(555, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:36:20'),
(556, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:36:20'),
(557, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:36:20'),
(558, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 09:36:20'),
(559, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:36:20'),
(560, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:36:20'),
(561, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:36:20'),
(562, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:36:20'),
(563, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:36:20'),
(564, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:36:20'),
(565, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:36:20'),
(566, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:36:20'),
(567, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:36:20'),
(568, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:36:20'),
(569, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:36:20'),
(570, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:36:20'),
(571, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:36:20'),
(572, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:36:20'),
(573, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(574, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(575, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(576, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:36:20'),
(577, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:36:20'),
(578, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:36:20'),
(579, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:36:20'),
(580, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:36:20'),
(581, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:36:20'),
(582, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:36:20'),
(583, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 09:36:20'),
(584, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 09:36:20'),
(585, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 09:36:20'),
(586, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 09:36:20'),
(587, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 09:36:20'),
(588, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 09:36:20'),
(589, 'INSERT', 'usuarios', 39, '39', '2024-10-20 09:40:26'),
(590, 'INSERT', 'usuarios', 39, '39', '2024-10-20 09:40:26'),
(591, 'INSERT', 'usuarios', 39, 'sistema', '2024-10-20 09:40:26'),
(592, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:40:49'),
(593, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:40:49'),
(594, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:40:49'),
(595, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:40:49'),
(596, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:40:49'),
(597, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:40:49'),
(598, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 09:40:49'),
(599, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:40:49'),
(600, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:40:49'),
(601, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:40:49'),
(602, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 09:40:49'),
(603, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:40:49'),
(604, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:40:49'),
(605, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:40:49'),
(606, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:40:49'),
(607, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:40:49'),
(608, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:40:49'),
(609, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:40:49'),
(610, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:40:49'),
(611, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:40:49'),
(612, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:40:49'),
(613, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:40:49'),
(614, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:40:49'),
(615, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:40:49'),
(616, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:40:49'),
(617, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(618, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(619, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(620, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:40:49'),
(621, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:40:49'),
(622, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:40:49'),
(623, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:40:49'),
(624, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:40:49'),
(625, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:40:49'),
(626, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:40:49'),
(627, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 09:40:49'),
(628, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 09:40:49'),
(629, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 09:40:49'),
(630, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 09:40:49'),
(631, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 09:40:49'),
(632, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 09:40:49'),
(633, 'UPDATE', 'usuarios', 39, '39', '2024-10-20 09:40:49'),
(634, 'INSERT', 'usuarios', 40, '40', '2024-10-20 09:42:58'),
(635, 'INSERT', 'usuarios', 40, '40', '2024-10-20 09:42:58'),
(636, 'INSERT', 'usuarios', 40, 'sistema', '2024-10-20 09:42:58'),
(637, 'INSERT', 'usuarios', 41, '41', '2024-10-20 09:56:31'),
(638, 'INSERT', 'usuarios', 41, '41', '2024-10-20 09:56:31'),
(639, 'INSERT', 'usuarios', 41, 'sistema', '2024-10-20 09:56:31'),
(640, 'INSERT', 'usuarios', 42, '42', '2024-10-20 10:10:40'),
(641, 'INSERT', 'usuarios', 42, '42', '2024-10-20 10:10:40'),
(642, 'INSERT', 'usuarios', 42, 'sistema', '2024-10-20 10:10:40'),
(643, 'INSERT', 'usuarios', 43, '43', '2024-10-20 10:14:01'),
(644, 'INSERT', 'usuarios', 43, '43', '2024-10-20 10:14:01'),
(645, 'INSERT', 'usuarios', 43, 'sistema', '2024-10-20 10:14:01'),
(646, 'UPDATE', 'usuarios', 43, '43', '2024-10-20 10:14:02'),
(647, 'INSERT', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(648, 'INSERT', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(649, 'INSERT', 'usuarios', 44, 'sistema', '2024-10-20 10:20:19'),
(650, 'UPDATE', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(651, 'INSERT', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(652, 'INSERT', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(653, 'INSERT', 'usuarios', 45, 'sistema', '2024-10-20 10:47:20'),
(654, 'UPDATE', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(655, 'INSERT', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(656, 'INSERT', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(657, 'INSERT', 'usuarios', 46, 'sistema', '2024-10-20 10:56:38'),
(658, 'UPDATE', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(659, 'INSERT', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(660, 'INSERT', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(661, 'INSERT', 'usuarios', 47, 'sistema', '2024-10-20 11:10:56'),
(662, 'UPDATE', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(663, 'INSERT', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(664, 'INSERT', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(665, 'INSERT', 'usuarios', 48, 'sistema', '2024-10-20 11:17:56'),
(666, 'UPDATE', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(667, 'INSERT', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(668, 'INSERT', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(669, 'INSERT', 'usuarios', 49, 'sistema', '2024-10-20 11:24:30'),
(670, 'UPDATE', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(671, 'INSERT', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(672, 'INSERT', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(673, 'INSERT', 'usuarios', 50, 'sistema', '2024-10-20 11:35:17'),
(674, 'UPDATE', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(675, 'INSERT', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(676, 'INSERT', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(677, 'INSERT', 'usuarios', 51, 'sistema', '2024-10-20 11:49:59'),
(678, 'UPDATE', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(679, 'INSERT', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(680, 'INSERT', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(681, 'INSERT', 'usuarios', 52, 'sistema', '2024-10-20 11:51:53'),
(682, 'UPDATE', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(683, 'INSERT', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(684, 'INSERT', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(685, 'INSERT', 'usuarios', 53, 'sistema', '2024-10-20 12:02:41'),
(686, 'UPDATE', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(687, 'INSERT', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(688, 'INSERT', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(689, 'INSERT', 'usuarios', 54, 'sistema', '2024-10-20 13:29:53'),
(690, 'UPDATE', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(691, 'INSERT', 'usuarios', 55, '55', '2024-10-20 13:33:15'),
(692, 'INSERT', 'usuarios', 55, '55', '2024-10-20 13:33:15'),
(693, 'INSERT', 'usuarios', 55, 'sistema', '2024-10-20 13:33:15'),
(694, 'INSERT', 'familias', 32, 'sistema', '2024-10-20 13:33:15'),
(695, 'INSERT', 'grupos', 15, 'Sistema', '2024-10-20 13:33:15'),
(696, 'INSERT', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(697, 'INSERT', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(698, 'INSERT', 'usuarios', 56, 'sistema', '2024-10-20 13:38:38'),
(699, 'UPDATE', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(700, 'INSERT', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(701, 'INSERT', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(702, 'INSERT', 'usuarios', 57, 'sistema', '2024-10-20 13:45:49'),
(703, 'UPDATE', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(704, 'INSERT', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(705, 'INSERT', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(706, 'INSERT', 'usuarios', 58, 'sistema', '2024-10-20 13:55:11'),
(707, 'UPDATE', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(708, 'INSERT', 'usuarios', 59, '59', '2024-10-20 13:58:07'),
(709, 'INSERT', 'usuarios', 59, '59', '2024-10-20 13:58:07'),
(710, 'INSERT', 'usuarios', 59, 'sistema', '2024-10-20 13:58:07'),
(711, 'INSERT', 'familias', 33, 'sistema', '2024-10-20 13:58:07'),
(712, 'INSERT', 'grupos', 16, 'Sistema', '2024-10-20 13:58:07'),
(713, 'INSERT', 'usuarios', 60, '60', '2024-10-20 14:03:29'),
(714, 'INSERT', 'usuarios', 60, '60', '2024-10-20 14:03:29'),
(715, 'INSERT', 'usuarios', 60, 'sistema', '2024-10-20 14:03:29'),
(716, 'INSERT', 'usuarios', 61, '61', '2024-10-20 14:05:27'),
(717, 'INSERT', 'usuarios', 61, '61', '2024-10-20 14:05:27'),
(718, 'INSERT', 'usuarios', 61, 'sistema', '2024-10-20 14:05:27'),
(719, 'INSERT', 'familias', 34, 'sistema', '2024-10-20 14:05:27'),
(720, 'INSERT', 'grupos', 17, 'Sistema', '2024-10-20 14:05:27'),
(721, 'INSERT', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(722, 'INSERT', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(723, 'INSERT', 'usuarios', 62, 'sistema', '2024-10-20 14:08:36'),
(724, 'UPDATE', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(725, 'INSERT', 'usuarios', 63, '63', '2024-10-20 14:55:14'),
(726, 'INSERT', 'usuarios', 63, '63', '2024-10-20 14:55:14'),
(727, 'INSERT', 'usuarios', 63, 'sistema', '2024-10-20 14:55:14'),
(728, 'INSERT', 'familias', 35, 'sistema', '2024-10-20 14:55:14'),
(729, 'INSERT', 'grupos', 18, 'Sistema', '2024-10-20 14:55:15'),
(730, 'INSERT', 'usuarios', 64, '64', '2024-10-20 15:27:05'),
(731, 'INSERT', 'usuarios', 64, '64', '2024-10-20 15:27:05'),
(732, 'INSERT', 'usuarios', 64, 'sistema', '2024-10-20 15:27:05'),
(733, 'INSERT', 'usuarios', 65, '65', '2024-10-20 15:28:56'),
(734, 'INSERT', 'usuarios', 65, '65', '2024-10-20 15:28:56'),
(735, 'INSERT', 'usuarios', 65, 'sistema', '2024-10-20 15:28:56'),
(736, 'INSERT', 'familias', 36, 'sistema', '2024-10-20 15:30:47'),
(737, 'INSERT', 'familias', 37, 'sistema', '2024-10-20 15:30:47'),
(738, 'INSERT', 'grupos', 19, 'Sistema', '2024-10-20 15:31:44'),
(739, 'INSERT', 'grupos', 20, 'Sistema', '2024-10-20 15:31:44'),
(740, 'INSERT', 'familias', 38, 'sistema', '2024-10-20 17:10:59'),
(741, 'INSERT', 'familias', 39, 'sistema', '2024-10-20 17:10:59'),
(742, 'INSERT', 'familias', 40, 'sistema', '2024-10-20 17:10:59'),
(743, 'INSERT', 'familias', 41, 'sistema', '2024-10-20 17:10:59'),
(744, 'INSERT', 'familias', 43, 'sistema', '2024-10-20 17:18:56'),
(745, 'INSERT', 'familias', 44, 'sistema', '2024-10-20 17:18:56'),
(746, 'INSERT', 'familias', 45, 'sistema', '2024-10-20 17:29:14'),
(747, 'INSERT', 'familias', 46, 'sistema', '2024-10-20 17:48:59'),
(748, 'INSERT', 'familias', 47, 'sistema', '2024-10-20 18:42:31'),
(749, 'INSERT', 'familias', 48, 'sistema', '2024-10-20 18:44:27'),
(750, 'INSERT', 'familias', 50, 'sistema', '2024-10-20 19:22:33'),
(751, 'INSERT', 'usuarios', 66, '66', '2024-10-20 19:26:49'),
(752, 'INSERT', 'usuarios', 66, '66', '2024-10-20 19:26:49'),
(753, 'INSERT', 'usuarios', 66, 'sistema', '2024-10-20 19:26:49'),
(754, 'INSERT', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(755, 'INSERT', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(756, 'INSERT', 'usuarios', 67, 'sistema', '2024-10-20 19:28:40'),
(757, 'UPDATE', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(758, 'INSERT', 'familias', 51, 'sistema', '2024-10-20 19:31:23'),
(759, 'INSERT', 'familias', 53, 'sistema', '2024-10-20 19:42:16'),
(760, 'INSERT', 'familias', 55, 'sistema', '2024-10-20 20:34:34'),
(761, 'INSERT', 'familias', 56, 'sistema', '2024-10-20 20:40:56'),
(762, 'INSERT', 'usuarios', 68, '68', '2024-10-20 21:07:01'),
(763, 'INSERT', 'usuarios', 68, '68', '2024-10-20 21:07:01'),
(764, 'INSERT', 'usuarios', 68, 'sistema', '2024-10-20 21:07:01'),
(765, 'INSERT', 'usuarios', 70, '70', '2024-10-20 21:08:55'),
(766, 'INSERT', 'usuarios', 70, '70', '2024-10-20 21:08:55'),
(767, 'INSERT', 'usuarios', 70, 'sistema', '2024-10-20 21:08:55'),
(768, 'INSERT', 'usuarios', 71, '71', '2024-10-20 21:23:39'),
(769, 'INSERT', 'usuarios', 71, '71', '2024-10-20 21:23:39'),
(770, 'INSERT', 'usuarios', 71, 'sistema', '2024-10-20 21:23:39'),
(771, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 21:23:50'),
(772, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 21:23:50'),
(773, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 21:23:50'),
(774, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 21:23:50'),
(775, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 21:23:50'),
(776, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 21:23:50'),
(777, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 21:23:50'),
(778, 'UPDATE', 'familias', 32, 'sistema', '2024-10-20 21:23:50'),
(779, 'UPDATE', 'familias', 33, 'sistema', '2024-10-20 21:23:50'),
(780, 'UPDATE', 'familias', 34, 'sistema', '2024-10-20 21:23:50'),
(781, 'UPDATE', 'familias', 35, 'sistema', '2024-10-20 21:23:50'),
(782, 'UPDATE', 'familias', 36, 'sistema', '2024-10-20 21:23:50'),
(783, 'UPDATE', 'familias', 37, 'sistema', '2024-10-20 21:23:50'),
(784, 'UPDATE', 'familias', 38, 'sistema', '2024-10-20 21:23:50'),
(785, 'UPDATE', 'familias', 39, 'sistema', '2024-10-20 21:23:50'),
(786, 'UPDATE', 'familias', 40, 'sistema', '2024-10-20 21:23:50'),
(787, 'UPDATE', 'familias', 41, 'sistema', '2024-10-20 21:23:50'),
(788, 'UPDATE', 'familias', 43, 'sistema', '2024-10-20 21:23:50'),
(789, 'UPDATE', 'familias', 44, 'sistema', '2024-10-20 21:23:50'),
(790, 'UPDATE', 'familias', 45, 'sistema', '2024-10-20 21:23:50'),
(791, 'UPDATE', 'familias', 46, 'sistema', '2024-10-20 21:23:50'),
(792, 'UPDATE', 'familias', 47, 'sistema', '2024-10-20 21:23:50'),
(793, 'UPDATE', 'familias', 48, 'sistema', '2024-10-20 21:23:50'),
(794, 'UPDATE', 'familias', 50, 'sistema', '2024-10-20 21:23:50'),
(795, 'UPDATE', 'familias', 51, 'sistema', '2024-10-20 21:23:50'),
(796, 'UPDATE', 'familias', 53, 'sistema', '2024-10-20 21:23:50'),
(797, 'UPDATE', 'familias', 55, 'sistema', '2024-10-20 21:23:50'),
(798, 'UPDATE', 'familias', 56, 'sistema', '2024-10-20 21:23:50'),
(799, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 21:23:50'),
(800, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 21:23:50'),
(801, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 21:23:50'),
(802, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 21:23:50'),
(803, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-20 21:23:50'),
(804, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-20 21:23:50'),
(805, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-20 21:23:50'),
(806, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-20 21:23:50'),
(807, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-20 21:23:50'),
(808, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-20 21:23:50'),
(809, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 21:23:50'),
(810, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 21:23:50'),
(811, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 21:23:50'),
(812, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 21:23:50'),
(813, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 21:23:50'),
(814, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 21:23:50'),
(815, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 21:23:50'),
(816, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 21:23:50'),
(817, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 21:23:50'),
(818, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 21:23:50'),
(819, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 21:23:50'),
(820, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 21:23:50'),
(821, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 21:23:50'),
(822, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 21:23:50'),
(823, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50'),
(824, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50');
INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(825, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50'),
(826, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 21:23:50'),
(827, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 21:23:50'),
(828, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 21:23:50'),
(829, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 21:23:50'),
(830, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 21:23:50'),
(831, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 21:23:50'),
(832, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 21:23:50'),
(833, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 21:23:50'),
(834, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 21:23:50'),
(835, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 21:23:50'),
(836, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 21:23:50'),
(837, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 21:23:50'),
(838, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 21:23:50'),
(839, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 21:23:50'),
(840, 'UPDATE', 'usuarios', 39, '39', '2024-10-20 21:23:50'),
(841, 'UPDATE', 'usuarios', 40, '40', '2024-10-20 21:23:50'),
(842, 'UPDATE', 'usuarios', 41, '41', '2024-10-20 21:23:50'),
(843, 'UPDATE', 'usuarios', 41, '41', '2024-10-20 21:23:50'),
(844, 'UPDATE', 'usuarios', 42, '42', '2024-10-20 21:23:50'),
(845, 'UPDATE', 'usuarios', 43, '43', '2024-10-20 21:23:50'),
(846, 'UPDATE', 'usuarios', 44, '44', '2024-10-20 21:23:50'),
(847, 'UPDATE', 'usuarios', 45, '45', '2024-10-20 21:23:50'),
(848, 'UPDATE', 'usuarios', 46, '46', '2024-10-20 21:23:50'),
(849, 'UPDATE', 'usuarios', 47, '47', '2024-10-20 21:23:50'),
(850, 'UPDATE', 'usuarios', 48, '48', '2024-10-20 21:23:50'),
(851, 'UPDATE', 'usuarios', 49, '49', '2024-10-20 21:23:50'),
(852, 'UPDATE', 'usuarios', 50, '50', '2024-10-20 21:23:50'),
(853, 'UPDATE', 'usuarios', 51, '51', '2024-10-20 21:23:50'),
(854, 'UPDATE', 'usuarios', 52, '52', '2024-10-20 21:23:50'),
(855, 'UPDATE', 'usuarios', 53, '53', '2024-10-20 21:23:50'),
(856, 'UPDATE', 'usuarios', 54, '54', '2024-10-20 21:23:50'),
(857, 'UPDATE', 'usuarios', 55, '55', '2024-10-20 21:23:50'),
(858, 'UPDATE', 'usuarios', 56, '56', '2024-10-20 21:23:50'),
(859, 'UPDATE', 'usuarios', 57, '57', '2024-10-20 21:23:50'),
(860, 'UPDATE', 'usuarios', 58, '58', '2024-10-20 21:23:50'),
(861, 'UPDATE', 'usuarios', 59, '59', '2024-10-20 21:23:50'),
(862, 'UPDATE', 'usuarios', 60, '60', '2024-10-20 21:23:50'),
(863, 'UPDATE', 'usuarios', 61, '61', '2024-10-20 21:23:50'),
(864, 'UPDATE', 'usuarios', 61, '61', '2024-10-20 21:23:50'),
(865, 'UPDATE', 'usuarios', 62, '62', '2024-10-20 21:23:50'),
(866, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(867, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(868, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(869, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(870, 'UPDATE', 'usuarios', 64, '64', '2024-10-20 21:23:50'),
(871, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(872, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(873, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(874, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(875, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(876, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(877, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(878, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(879, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(880, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(881, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(882, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(883, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(884, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(885, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(886, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(887, 'UPDATE', 'usuarios', 66, '66', '2024-10-20 21:23:50'),
(888, 'UPDATE', 'usuarios', 67, '67', '2024-10-20 21:23:50'),
(889, 'UPDATE', 'usuarios', 68, '68', '2024-10-20 21:23:50'),
(890, 'UPDATE', 'usuarios', 70, '70', '2024-10-20 21:23:50'),
(891, 'UPDATE', 'usuarios', 71, '71', '2024-10-20 21:23:50'),
(892, 'INSERT', 'usuarios', 72, '72', '2024-10-20 21:25:22'),
(893, 'INSERT', 'usuarios', 72, '72', '2024-10-20 21:25:22'),
(894, 'INSERT', 'usuarios', 72, 'sistema', '2024-10-20 21:25:22'),
(895, 'INSERT', 'familias', 57, 'sistema', '2024-10-20 21:26:47'),
(896, 'DELETE', 'usuarios', 71, '71', '2024-10-20 21:34:55'),
(897, 'DELETE', 'usuarios', 68, '68', '2024-10-20 21:35:09'),
(898, 'INSERT', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(899, 'INSERT', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(900, 'INSERT', 'usuarios', 73, 'sistema', '2024-10-21 07:42:36'),
(901, 'UPDATE', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(902, 'UPDATE', 'usuarios', 73, '73', '2024-10-21 07:51:19'),
(903, 'INSERT', 'familias', 58, 'sistema', '2024-10-21 07:51:59'),
(904, 'INSERT', 'grupos', 21, 'Sistema', '2024-10-21 07:54:35'),
(905, 'INSERT', 'usuarios', 74, '74', '2024-10-21 08:02:32'),
(906, 'INSERT', 'usuarios', 74, '74', '2024-10-21 08:02:32'),
(907, 'INSERT', 'usuarios', 74, 'sistema', '2024-10-21 08:02:32'),
(908, 'INSERT', 'familias', 59, 'sistema', '2024-10-21 08:02:32'),
(909, 'INSERT', 'grupos', 22, 'Sistema', '2024-10-21 08:02:32'),
(910, 'INSERT', 'usuarios', 75, '75', '2024-10-21 09:03:38'),
(911, 'INSERT', 'usuarios', 75, '75', '2024-10-21 09:03:38'),
(912, 'INSERT', 'usuarios', 75, 'sistema', '2024-10-21 09:03:38'),
(913, 'INSERT', 'familias', 60, 'sistema', '2024-10-21 09:03:38'),
(914, 'INSERT', 'grupos', 23, 'Sistema', '2024-10-21 09:03:39'),
(915, 'INSERT', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(916, 'INSERT', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(917, 'INSERT', 'usuarios', 76, 'sistema', '2024-10-21 10:45:37'),
(918, 'UPDATE', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(919, 'INSERT', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(920, 'INSERT', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(921, 'INSERT', 'usuarios', 77, 'sistema', '2024-10-21 10:51:30'),
(922, 'UPDATE', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(923, 'INSERT', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(924, 'INSERT', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(925, 'INSERT', 'usuarios', 78, 'sistema', '2024-10-21 11:20:39'),
(926, 'UPDATE', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(927, 'INSERT', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(928, 'INSERT', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(929, 'INSERT', 'usuarios', 79, 'sistema', '2024-10-21 11:42:34'),
(930, 'UPDATE', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(931, 'INSERT', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(932, 'INSERT', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(933, 'INSERT', 'usuarios', 80, 'sistema', '2024-10-21 12:07:21'),
(934, 'UPDATE', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(935, 'INSERT', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(936, 'INSERT', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(937, 'INSERT', 'usuarios', 81, 'sistema', '2024-10-21 12:18:43'),
(938, 'UPDATE', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(939, 'INSERT', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(940, 'INSERT', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(941, 'INSERT', 'usuarios', 82, 'sistema', '2024-10-21 12:36:35'),
(942, 'UPDATE', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(943, 'INSERT', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(944, 'INSERT', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(945, 'INSERT', 'usuarios', 83, 'sistema', '2024-10-21 14:10:04'),
(946, 'UPDATE', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(947, 'INSERT', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(948, 'INSERT', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(949, 'INSERT', 'usuarios', 84, 'sistema', '2024-10-21 14:15:26'),
(950, 'UPDATE', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(951, 'INSERT', 'usuarios', 85, '85', '2024-10-21 15:27:11'),
(952, 'INSERT', 'usuarios', 85, '85', '2024-10-21 15:27:11'),
(953, 'INSERT', 'usuarios', 85, 'sistema', '2024-10-21 15:27:11'),
(954, 'INSERT', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(955, 'INSERT', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(956, 'INSERT', 'usuarios', 86, 'sistema', '2024-10-21 15:42:33'),
(957, 'UPDATE', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(958, 'DELETE', 'usuarios', 83, '83', '2024-10-21 15:45:25'),
(959, 'DELETE', 'usuarios', 84, '84', '2024-10-21 15:45:33'),
(960, 'INSERT', 'familias', 61, 'sistema', '2024-10-21 15:47:50'),
(961, 'INSERT', 'grupos', 24, 'Sistema', '2024-10-21 15:49:11'),
(962, 'INSERT', 'usuarios', 87, '87', '2024-10-21 15:59:34'),
(963, 'INSERT', 'usuarios', 87, '87', '2024-10-21 15:59:34'),
(964, 'INSERT', 'usuarios', 87, 'sistema', '2024-10-21 15:59:34'),
(965, 'INSERT', 'familias', 62, 'sistema', '2024-10-21 15:59:34'),
(966, 'INSERT', 'grupos', 25, 'Sistema', '2024-10-21 15:59:34'),
(967, 'INSERT', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(968, 'INSERT', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(969, 'INSERT', 'usuarios', 88, 'sistema', '2024-10-21 18:07:12'),
(970, 'UPDATE', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(971, 'INSERT', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(972, 'INSERT', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(973, 'INSERT', 'usuarios', 89, 'sistema', '2024-10-21 18:15:54'),
(974, 'INSERT', 'familias', 63, 'sistema', '2024-10-21 18:15:54'),
(975, 'INSERT', 'grupos', 26, 'Sistema', '2024-10-21 18:15:54'),
(976, 'UPDATE', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(977, 'INSERT', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(978, 'INSERT', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(979, 'INSERT', 'usuarios', 90, 'sistema', '2024-10-21 18:21:43'),
(980, 'UPDATE', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(981, 'INSERT', 'usuarios', 91, '91', '2024-10-21 18:24:58'),
(982, 'INSERT', 'usuarios', 91, '91', '2024-10-21 18:24:58'),
(983, 'INSERT', 'usuarios', 91, 'sistema', '2024-10-21 18:24:58'),
(984, 'UPDATE', 'usuarios', 91, '91', '2024-10-21 18:24:58'),
(985, 'INSERT', 'usuarios', 92, '92', '2024-10-22 14:24:26'),
(986, 'INSERT', 'usuarios', 92, '92', '2024-10-22 14:24:26'),
(987, 'INSERT', 'usuarios', 92, 'sistema', '2024-10-22 14:24:26'),
(988, 'UPDATE', 'usuarios', 92, '92', '2024-10-22 14:24:26'),
(989, 'INSERT', 'usuarios', 93, '93', '2024-10-22 14:32:57'),
(990, 'INSERT', 'usuarios', 93, '93', '2024-10-22 14:32:57'),
(991, 'INSERT', 'usuarios', 93, 'sistema', '2024-10-22 14:32:57'),
(992, 'INSERT', 'familias', 64, 'sistema', '2024-10-22 14:32:57'),
(993, 'UPDATE', 'usuarios', 93, '93', '2024-10-22 14:32:57'),
(994, 'INSERT', 'usuarios', 94, '94', '2024-10-22 14:36:43'),
(995, 'INSERT', 'usuarios', 94, '94', '2024-10-22 14:36:43'),
(996, 'INSERT', 'usuarios', 94, 'sistema', '2024-10-22 14:36:43'),
(997, 'UPDATE', 'usuarios', 94, '94', '2024-10-22 14:36:43'),
(998, 'INSERT', 'usuarios', 95, '95', '2024-10-22 15:07:58'),
(999, 'INSERT', 'usuarios', 95, '95', '2024-10-22 15:07:58'),
(1000, 'INSERT', 'usuarios', 95, 'sistema', '2024-10-22 15:07:58'),
(1001, 'UPDATE', 'usuarios', 95, '95', '2024-10-22 15:07:58'),
(1002, 'INSERT', 'usuarios', 96, '96', '2024-10-22 15:14:35'),
(1003, 'INSERT', 'usuarios', 96, '96', '2024-10-22 15:14:35'),
(1004, 'INSERT', 'usuarios', 96, 'sistema', '2024-10-22 15:14:35'),
(1005, 'INSERT', 'familias', 65, 'sistema', '2024-10-22 15:14:35'),
(1006, 'INSERT', 'grupos', 27, 'Sistema', '2024-10-22 15:14:35'),
(1007, 'UPDATE', 'usuarios', 96, '96', '2024-10-22 15:14:35'),
(1008, 'INSERT', 'usuarios', 97, '97', '2024-10-22 15:23:38'),
(1009, 'INSERT', 'usuarios', 97, '97', '2024-10-22 15:23:38'),
(1010, 'INSERT', 'usuarios', 97, 'sistema', '2024-10-22 15:23:38'),
(1011, 'UPDATE', 'usuarios', 97, '97', '2024-10-22 15:23:38'),
(1012, 'INSERT', 'usuarios', 98, '98', '2024-10-22 15:35:30'),
(1013, 'INSERT', 'usuarios', 98, '98', '2024-10-22 15:35:30'),
(1014, 'INSERT', 'usuarios', 98, 'sistema', '2024-10-22 15:35:30'),
(1015, 'UPDATE', 'usuarios', 98, '98', '2024-10-22 15:35:30'),
(1016, 'INSERT', 'usuarios', 99, '99', '2024-10-22 15:38:36'),
(1017, 'INSERT', 'usuarios', 99, '99', '2024-10-22 15:38:36'),
(1018, 'INSERT', 'usuarios', 99, 'sistema', '2024-10-22 15:38:36'),
(1019, 'UPDATE', 'usuarios', 99, '99', '2024-10-22 15:38:36'),
(1020, 'UPDATE', 'familias', 1, 'sistema', '2024-10-22 15:43:08'),
(1021, 'UPDATE', 'familias', 16, 'sistema', '2024-10-22 15:43:08'),
(1022, 'UPDATE', 'familias', 22, 'sistema', '2024-10-22 15:43:08'),
(1023, 'UPDATE', 'familias', 23, 'sistema', '2024-10-22 15:43:08'),
(1024, 'UPDATE', 'familias', 24, 'sistema', '2024-10-22 15:43:08'),
(1025, 'UPDATE', 'familias', 30, 'sistema', '2024-10-22 15:43:08'),
(1026, 'UPDATE', 'familias', 31, 'sistema', '2024-10-22 15:43:08'),
(1027, 'UPDATE', 'familias', 32, 'sistema', '2024-10-22 15:43:08'),
(1028, 'UPDATE', 'familias', 33, 'sistema', '2024-10-22 15:43:08'),
(1029, 'UPDATE', 'familias', 34, 'sistema', '2024-10-22 15:43:08'),
(1030, 'UPDATE', 'familias', 35, 'sistema', '2024-10-22 15:43:08'),
(1031, 'UPDATE', 'familias', 36, 'sistema', '2024-10-22 15:43:08'),
(1032, 'UPDATE', 'familias', 37, 'sistema', '2024-10-22 15:43:08'),
(1033, 'UPDATE', 'familias', 38, 'sistema', '2024-10-22 15:43:08'),
(1034, 'UPDATE', 'familias', 39, 'sistema', '2024-10-22 15:43:08'),
(1035, 'UPDATE', 'familias', 40, 'sistema', '2024-10-22 15:43:08'),
(1036, 'UPDATE', 'familias', 41, 'sistema', '2024-10-22 15:43:08'),
(1037, 'UPDATE', 'familias', 43, 'sistema', '2024-10-22 15:43:08'),
(1038, 'UPDATE', 'familias', 44, 'sistema', '2024-10-22 15:43:08'),
(1039, 'UPDATE', 'familias', 45, 'sistema', '2024-10-22 15:43:08'),
(1040, 'UPDATE', 'familias', 46, 'sistema', '2024-10-22 15:43:08'),
(1041, 'UPDATE', 'familias', 47, 'sistema', '2024-10-22 15:43:08'),
(1042, 'UPDATE', 'familias', 48, 'sistema', '2024-10-22 15:43:08'),
(1043, 'UPDATE', 'familias', 50, 'sistema', '2024-10-22 15:43:08'),
(1044, 'UPDATE', 'familias', 51, 'sistema', '2024-10-22 15:43:08'),
(1045, 'UPDATE', 'familias', 53, 'sistema', '2024-10-22 15:43:08'),
(1046, 'UPDATE', 'familias', 55, 'sistema', '2024-10-22 15:43:08'),
(1047, 'UPDATE', 'familias', 56, 'sistema', '2024-10-22 15:43:08'),
(1048, 'UPDATE', 'familias', 57, 'sistema', '2024-10-22 15:43:08'),
(1049, 'UPDATE', 'familias', 58, 'sistema', '2024-10-22 15:43:08'),
(1050, 'UPDATE', 'familias', 59, 'sistema', '2024-10-22 15:43:08'),
(1051, 'UPDATE', 'familias', 60, 'sistema', '2024-10-22 15:43:08'),
(1052, 'UPDATE', 'familias', 61, 'sistema', '2024-10-22 15:43:08'),
(1053, 'UPDATE', 'familias', 62, 'sistema', '2024-10-22 15:43:08'),
(1054, 'UPDATE', 'familias', 63, 'sistema', '2024-10-22 15:43:08'),
(1055, 'UPDATE', 'familias', 64, 'sistema', '2024-10-22 15:43:08'),
(1056, 'UPDATE', 'familias', 65, 'sistema', '2024-10-22 15:43:08'),
(1057, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-22 15:43:08'),
(1058, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-22 15:43:08'),
(1059, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-22 15:43:08'),
(1060, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-22 15:43:08'),
(1061, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-22 15:43:08'),
(1062, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-22 15:43:08'),
(1063, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-22 15:43:08'),
(1064, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-22 15:43:08'),
(1065, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-22 15:43:08'),
(1066, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-22 15:43:08'),
(1067, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-22 15:43:08'),
(1068, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-22 15:43:08'),
(1069, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-22 15:43:08'),
(1070, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-22 15:43:08'),
(1071, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-22 15:43:08'),
(1072, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-22 15:43:08'),
(1073, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-22 15:43:08'),
(1074, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 15:43:08'),
(1075, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 15:43:08'),
(1076, 'UPDATE', 'usuarios', 5, '5', '2024-10-22 15:43:08'),
(1077, 'UPDATE', 'usuarios', 6, '6', '2024-10-22 15:43:08'),
(1078, 'UPDATE', 'usuarios', 7, '7', '2024-10-22 15:43:08'),
(1079, 'UPDATE', 'usuarios', 8, '8', '2024-10-22 15:43:08'),
(1080, 'UPDATE', 'usuarios', 9, '9', '2024-10-22 15:43:08'),
(1081, 'UPDATE', 'usuarios', 10, '10', '2024-10-22 15:43:08'),
(1082, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 15:43:08'),
(1083, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 15:43:08'),
(1084, 'UPDATE', 'usuarios', 16, '16', '2024-10-22 15:43:08'),
(1085, 'UPDATE', 'usuarios', 18, '18', '2024-10-22 15:43:08'),
(1086, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 15:43:08'),
(1087, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 15:43:08'),
(1088, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 15:43:08'),
(1089, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 15:43:08'),
(1090, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 15:43:08'),
(1091, 'UPDATE', 'usuarios', 21, '21', '2024-10-22 15:43:08'),
(1092, 'UPDATE', 'usuarios', 26, '26', '2024-10-22 15:43:08'),
(1093, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 15:43:08'),
(1094, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 15:43:08'),
(1095, 'UPDATE', 'usuarios', 28, '28', '2024-10-22 15:43:08'),
(1096, 'UPDATE', 'usuarios', 29, '29', '2024-10-22 15:43:08'),
(1097, 'UPDATE', 'usuarios', 30, '30', '2024-10-22 15:43:08'),
(1098, 'UPDATE', 'usuarios', 31, '31', '2024-10-22 15:43:08'),
(1099, 'UPDATE', 'usuarios', 33, '33', '2024-10-22 15:43:08'),
(1100, 'UPDATE', 'usuarios', 34, '34', '2024-10-22 15:43:08'),
(1101, 'UPDATE', 'usuarios', 35, '35', '2024-10-22 15:43:08'),
(1102, 'UPDATE', 'usuarios', 36, '36', '2024-10-22 15:43:08'),
(1103, 'UPDATE', 'usuarios', 37, '37', '2024-10-22 15:43:08'),
(1104, 'UPDATE', 'usuarios', 38, '38', '2024-10-22 15:43:08'),
(1105, 'UPDATE', 'usuarios', 39, '39', '2024-10-22 15:43:08'),
(1106, 'UPDATE', 'usuarios', 40, '40', '2024-10-22 15:43:08'),
(1107, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 15:43:08'),
(1108, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 15:43:08'),
(1109, 'UPDATE', 'usuarios', 42, '42', '2024-10-22 15:43:08'),
(1110, 'UPDATE', 'usuarios', 43, '43', '2024-10-22 15:43:08'),
(1111, 'UPDATE', 'usuarios', 44, '44', '2024-10-22 15:43:08'),
(1112, 'UPDATE', 'usuarios', 45, '45', '2024-10-22 15:43:08'),
(1113, 'UPDATE', 'usuarios', 46, '46', '2024-10-22 15:43:08'),
(1114, 'UPDATE', 'usuarios', 47, '47', '2024-10-22 15:43:08'),
(1115, 'UPDATE', 'usuarios', 48, '48', '2024-10-22 15:43:08'),
(1116, 'UPDATE', 'usuarios', 49, '49', '2024-10-22 15:43:08'),
(1117, 'UPDATE', 'usuarios', 50, '50', '2024-10-22 15:43:08'),
(1118, 'UPDATE', 'usuarios', 51, '51', '2024-10-22 15:43:08'),
(1119, 'UPDATE', 'usuarios', 52, '52', '2024-10-22 15:43:08'),
(1120, 'UPDATE', 'usuarios', 53, '53', '2024-10-22 15:43:08'),
(1121, 'UPDATE', 'usuarios', 54, '54', '2024-10-22 15:43:08'),
(1122, 'UPDATE', 'usuarios', 55, '55', '2024-10-22 15:43:08'),
(1123, 'UPDATE', 'usuarios', 56, '56', '2024-10-22 15:43:08'),
(1124, 'UPDATE', 'usuarios', 57, '57', '2024-10-22 15:43:08'),
(1125, 'UPDATE', 'usuarios', 58, '58', '2024-10-22 15:43:08'),
(1126, 'UPDATE', 'usuarios', 59, '59', '2024-10-22 15:43:08'),
(1127, 'UPDATE', 'usuarios', 60, '60', '2024-10-22 15:43:08'),
(1128, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 15:43:08'),
(1129, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 15:43:08'),
(1130, 'UPDATE', 'usuarios', 62, '62', '2024-10-22 15:43:08'),
(1131, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 15:43:08'),
(1132, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 15:43:08'),
(1133, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 15:43:08'),
(1134, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 15:43:08'),
(1135, 'UPDATE', 'usuarios', 64, '64', '2024-10-22 15:43:08'),
(1136, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1137, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1138, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1139, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1140, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1141, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1142, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1143, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1144, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1145, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1146, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1147, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1148, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1149, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1150, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1151, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 15:43:08'),
(1152, 'UPDATE', 'usuarios', 66, '66', '2024-10-22 15:43:08'),
(1153, 'UPDATE', 'usuarios', 67, '67', '2024-10-22 15:43:08'),
(1154, 'UPDATE', 'usuarios', 70, '70', '2024-10-22 15:43:08'),
(1155, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 15:43:08'),
(1156, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 15:43:08'),
(1157, 'UPDATE', 'usuarios', 73, '73', '2024-10-22 15:43:08'),
(1158, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 15:43:08'),
(1159, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 15:43:08'),
(1160, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 15:43:08'),
(1161, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 15:43:08'),
(1162, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 15:43:08'),
(1163, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 15:43:08'),
(1164, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 15:43:08'),
(1165, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 15:43:08'),
(1166, 'UPDATE', 'usuarios', 76, '76', '2024-10-22 15:43:08'),
(1167, 'UPDATE', 'usuarios', 77, '77', '2024-10-22 15:43:08'),
(1168, 'UPDATE', 'usuarios', 78, '78', '2024-10-22 15:43:08'),
(1169, 'UPDATE', 'usuarios', 79, '79', '2024-10-22 15:43:08'),
(1170, 'UPDATE', 'usuarios', 80, '80', '2024-10-22 15:43:08'),
(1171, 'UPDATE', 'usuarios', 81, '81', '2024-10-22 15:43:08'),
(1172, 'UPDATE', 'usuarios', 82, '82', '2024-10-22 15:43:08'),
(1173, 'UPDATE', 'usuarios', 85, '85', '2024-10-22 15:43:08'),
(1174, 'UPDATE', 'usuarios', 86, '86', '2024-10-22 15:43:08'),
(1175, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 15:43:08'),
(1176, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 15:43:08'),
(1177, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 15:43:08'),
(1178, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 15:43:08'),
(1179, 'UPDATE', 'usuarios', 88, '88', '2024-10-22 15:43:08'),
(1180, 'UPDATE', 'usuarios', 89, '89', '2024-10-22 15:43:08'),
(1181, 'UPDATE', 'usuarios', 90, '90', '2024-10-22 15:43:08'),
(1182, 'UPDATE', 'usuarios', 91, '91', '2024-10-22 15:43:08'),
(1183, 'UPDATE', 'usuarios', 92, '92', '2024-10-22 15:43:08'),
(1184, 'UPDATE', 'usuarios', 93, '93', '2024-10-22 15:43:08'),
(1185, 'UPDATE', 'usuarios', 94, '94', '2024-10-22 15:43:08'),
(1186, 'UPDATE', 'usuarios', 95, '95', '2024-10-22 15:43:08'),
(1187, 'UPDATE', 'usuarios', 96, '96', '2024-10-22 15:43:08'),
(1188, 'UPDATE', 'usuarios', 97, '97', '2024-10-22 15:43:08'),
(1189, 'UPDATE', 'usuarios', 98, '98', '2024-10-22 15:43:08'),
(1190, 'UPDATE', 'usuarios', 99, '99', '2024-10-22 15:43:08'),
(1191, 'INSERT', 'usuarios', 100, '100', '2024-10-22 15:44:47'),
(1192, 'INSERT', 'usuarios', 100, '100', '2024-10-22 15:44:47'),
(1193, 'INSERT', 'usuarios', 100, 'sistema', '2024-10-22 15:44:47'),
(1194, 'UPDATE', 'usuarios', 100, '100', '2024-10-22 15:44:47'),
(1195, 'INSERT', 'usuarios', 101, '101', '2024-10-22 15:50:44'),
(1196, 'INSERT', 'usuarios', 101, '101', '2024-10-22 15:50:44'),
(1197, 'INSERT', 'usuarios', 101, 'sistema', '2024-10-22 15:50:44'),
(1198, 'UPDATE', 'usuarios', 101, '101', '2024-10-22 15:50:44'),
(1199, 'INSERT', 'usuarios', 102, '102', '2024-10-22 16:10:37'),
(1200, 'INSERT', 'usuarios', 102, '102', '2024-10-22 16:10:37'),
(1201, 'INSERT', 'usuarios', 102, 'sistema', '2024-10-22 16:10:37'),
(1202, 'UPDATE', 'usuarios', 102, '102', '2024-10-22 16:10:37'),
(1203, 'INSERT', 'usuarios', 103, '103', '2024-10-22 16:19:26'),
(1204, 'INSERT', 'usuarios', 103, '103', '2024-10-22 16:19:26'),
(1205, 'INSERT', 'usuarios', 103, 'sistema', '2024-10-22 16:19:26'),
(1206, 'INSERT', 'familias', 66, 'sistema', '2024-10-22 16:19:26'),
(1207, 'INSERT', 'grupos', 28, 'Sistema', '2024-10-22 16:19:26'),
(1208, 'UPDATE', 'usuarios', 103, '103', '2024-10-22 16:19:26'),
(1209, 'INSERT', 'usuarios', 104, '104', '2024-10-22 16:23:50'),
(1210, 'INSERT', 'usuarios', 104, '104', '2024-10-22 16:23:50'),
(1211, 'INSERT', 'usuarios', 104, 'sistema', '2024-10-22 16:23:50'),
(1212, 'UPDATE', 'usuarios', 104, '104', '2024-10-22 16:23:50'),
(1213, 'INSERT', 'usuarios', 105, '105', '2024-10-22 16:27:00'),
(1214, 'INSERT', 'usuarios', 105, '105', '2024-10-22 16:27:00'),
(1215, 'INSERT', 'usuarios', 105, 'sistema', '2024-10-22 16:27:00'),
(1216, 'UPDATE', 'usuarios', 105, '105', '2024-10-22 16:27:00'),
(1217, 'INSERT', 'usuarios', 106, '106', '2024-10-22 16:31:58'),
(1218, 'INSERT', 'usuarios', 106, '106', '2024-10-22 16:31:58'),
(1219, 'INSERT', 'usuarios', 106, 'sistema', '2024-10-22 16:31:58'),
(1220, 'INSERT', 'familias', 67, 'sistema', '2024-10-22 16:31:59'),
(1221, 'INSERT', 'grupos', 29, 'Sistema', '2024-10-22 16:31:59'),
(1222, 'UPDATE', 'usuarios', 106, '106', '2024-10-22 16:31:59'),
(1223, 'INSERT', 'usuarios', 107, '107', '2024-10-22 16:38:05'),
(1224, 'INSERT', 'usuarios', 107, '107', '2024-10-22 16:38:05'),
(1225, 'INSERT', 'usuarios', 107, 'sistema', '2024-10-22 16:38:05'),
(1226, 'INSERT', 'familias', 68, 'sistema', '2024-10-22 16:38:05'),
(1227, 'INSERT', 'grupos', 30, 'Sistema', '2024-10-22 16:38:05'),
(1228, 'UPDATE', 'usuarios', 107, '107', '2024-10-22 16:38:05'),
(1229, 'INSERT', 'usuarios', 108, '108', '2024-10-22 16:41:16'),
(1230, 'INSERT', 'usuarios', 108, '108', '2024-10-22 16:41:16'),
(1231, 'INSERT', 'usuarios', 108, 'sistema', '2024-10-22 16:41:16'),
(1232, 'UPDATE', 'usuarios', 108, '108', '2024-10-22 16:41:16'),
(1233, 'INSERT', 'usuarios', 109, '109', '2024-10-22 17:00:16'),
(1234, 'INSERT', 'usuarios', 109, '109', '2024-10-22 17:00:16'),
(1235, 'INSERT', 'usuarios', 109, 'sistema', '2024-10-22 17:00:16'),
(1236, 'INSERT', 'familias', 69, 'sistema', '2024-10-22 17:00:17'),
(1237, 'INSERT', 'grupos', 31, 'Sistema', '2024-10-22 17:00:17'),
(1238, 'UPDATE', 'usuarios', 109, '109', '2024-10-22 17:00:17'),
(1239, 'INSERT', 'usuarios', 110, '110', '2024-10-22 17:41:09'),
(1240, 'INSERT', 'usuarios', 110, '110', '2024-10-22 17:41:09'),
(1241, 'INSERT', 'usuarios', 110, 'sistema', '2024-10-22 17:41:09'),
(1242, 'INSERT', 'familias', 70, 'sistema', '2024-10-22 17:41:09'),
(1243, 'INSERT', 'grupos', 32, 'Sistema', '2024-10-22 17:41:09'),
(1244, 'UPDATE', 'usuarios', 110, '110', '2024-10-22 17:41:09'),
(1245, 'INSERT', 'usuarios', 111, '111', '2024-10-22 18:20:18'),
(1246, 'INSERT', 'usuarios', 111, '111', '2024-10-22 18:20:18'),
(1247, 'INSERT', 'usuarios', 111, 'sistema', '2024-10-22 18:20:18'),
(1248, 'UPDATE', 'usuarios', 111, '111', '2024-10-22 18:20:18'),
(1249, 'INSERT', 'usuarios', 112, '112', '2024-10-22 18:23:25'),
(1250, 'INSERT', 'usuarios', 112, '112', '2024-10-22 18:23:25'),
(1251, 'INSERT', 'usuarios', 112, 'sistema', '2024-10-22 18:23:25'),
(1252, 'INSERT', 'familias', 71, 'sistema', '2024-10-22 18:23:26'),
(1253, 'INSERT', 'grupos', 33, 'Sistema', '2024-10-22 18:23:26'),
(1254, 'UPDATE', 'usuarios', 112, '112', '2024-10-22 18:23:26'),
(1255, 'INSERT', 'usuarios', 113, '113', '2024-10-22 18:27:06'),
(1256, 'INSERT', 'usuarios', 113, '113', '2024-10-22 18:27:06'),
(1257, 'INSERT', 'usuarios', 113, 'sistema', '2024-10-22 18:27:06'),
(1258, 'UPDATE', 'usuarios', 113, '113', '2024-10-22 18:27:06'),
(1259, 'INSERT', 'usuarios', 114, '114', '2024-10-22 18:35:04'),
(1260, 'INSERT', 'usuarios', 114, '114', '2024-10-22 18:35:04'),
(1261, 'INSERT', 'usuarios', 114, 'sistema', '2024-10-22 18:35:04'),
(1262, 'UPDATE', 'usuarios', 114, '114', '2024-10-22 18:35:04'),
(1263, 'INSERT', 'usuarios', 115, '115', '2024-10-22 19:32:58'),
(1264, 'INSERT', 'usuarios', 115, '115', '2024-10-22 19:32:58'),
(1265, 'INSERT', 'usuarios', 115, 'sistema', '2024-10-22 19:32:58'),
(1266, 'UPDATE', 'usuarios', 115, '115', '2024-10-22 19:32:58'),
(1267, 'INSERT', 'usuarios', 116, '116', '2024-10-22 20:06:58'),
(1268, 'INSERT', 'usuarios', 116, '116', '2024-10-22 20:06:58'),
(1269, 'INSERT', 'usuarios', 116, 'sistema', '2024-10-22 20:06:58'),
(1270, 'INSERT', 'familias', 72, 'sistema', '2024-10-22 20:06:58'),
(1271, 'INSERT', 'grupos', 34, 'Sistema', '2024-10-22 20:06:58'),
(1272, 'UPDATE', 'usuarios', 116, '116', '2024-10-22 20:06:58'),
(1273, 'INSERT', 'usuarios', 117, '117', '2024-10-22 20:09:38'),
(1274, 'INSERT', 'usuarios', 117, '117', '2024-10-22 20:09:38'),
(1275, 'INSERT', 'usuarios', 117, 'sistema', '2024-10-22 20:09:38'),
(1276, 'UPDATE', 'usuarios', 117, '117', '2024-10-22 20:09:38'),
(1277, 'INSERT', 'usuarios', 118, '118', '2024-10-22 20:24:11'),
(1278, 'INSERT', 'usuarios', 118, '118', '2024-10-22 20:24:11'),
(1279, 'INSERT', 'usuarios', 118, 'sistema', '2024-10-22 20:24:11'),
(1280, 'INSERT', 'familias', 73, 'sistema', '2024-10-22 20:24:11'),
(1281, 'INSERT', 'familias', 74, 'sistema', '2024-10-22 20:24:11'),
(1282, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 20:24:11'),
(1283, 'INSERT', 'usuarios', 119, '119', '2024-10-22 20:41:01'),
(1284, 'INSERT', 'usuarios', 119, '119', '2024-10-22 20:41:01'),
(1285, 'INSERT', 'usuarios', 119, 'sistema', '2024-10-22 20:41:01'),
(1286, 'INSERT', 'familias', 75, 'sistema', '2024-10-22 20:41:01'),
(1287, 'INSERT', 'familias', 76, 'sistema', '2024-10-22 20:41:01'),
(1288, 'INSERT', 'familias', 77, 'sistema', '2024-10-22 20:41:01'),
(1289, 'INSERT', 'grupos', 35, 'Sistema', '2024-10-22 20:41:01'),
(1290, 'INSERT', 'grupos', 36, 'Sistema', '2024-10-22 20:41:01'),
(1291, 'INSERT', 'grupos', 37, 'Sistema', '2024-10-22 20:41:01'),
(1292, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 20:41:01'),
(1293, 'INSERT', 'usuarios', 120, '120', '2024-10-22 21:10:04'),
(1294, 'INSERT', 'usuarios', 120, '120', '2024-10-22 21:10:04'),
(1295, 'INSERT', 'usuarios', 120, 'sistema', '2024-10-22 21:10:04'),
(1296, 'INSERT', 'familias', 78, 'sistema', '2024-10-22 21:10:04'),
(1297, 'INSERT', 'familias', 79, 'sistema', '2024-10-22 21:10:04'),
(1298, 'INSERT', 'familias', 80, 'sistema', '2024-10-22 21:10:04'),
(1299, 'INSERT', 'familias', 81, 'sistema', '2024-10-22 21:10:04'),
(1300, 'INSERT', 'familias', 82, 'sistema', '2024-10-22 21:10:04'),
(1301, 'INSERT', 'grupos', 38, 'Sistema', '2024-10-22 21:10:04'),
(1302, 'INSERT', 'grupos', 39, 'Sistema', '2024-10-22 21:10:05'),
(1303, 'INSERT', 'grupos', 40, 'Sistema', '2024-10-22 21:10:05'),
(1304, 'INSERT', 'grupos', 41, 'Sistema', '2024-10-22 21:10:05'),
(1305, 'INSERT', 'grupos', 42, 'Sistema', '2024-10-22 21:10:05'),
(1306, 'INSERT', 'grupos', 43, 'Sistema', '2024-10-22 21:10:05'),
(1307, 'INSERT', 'grupos', 44, 'Sistema', '2024-10-22 21:10:05'),
(1308, 'INSERT', 'grupos', 45, 'Sistema', '2024-10-22 21:10:05'),
(1309, 'INSERT', 'grupos', 46, 'Sistema', '2024-10-22 21:10:05'),
(1310, 'INSERT', 'grupos', 47, 'Sistema', '2024-10-22 21:10:05'),
(1311, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 21:10:05'),
(1312, 'INSERT', 'usuarios', 121, '121', '2024-10-22 21:21:45'),
(1313, 'INSERT', 'usuarios', 121, '121', '2024-10-22 21:21:45'),
(1314, 'INSERT', 'usuarios', 121, 'sistema', '2024-10-22 21:21:45'),
(1315, 'UPDATE', 'usuarios', 121, '121', '2024-10-22 21:21:45'),
(1316, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:12:57'),
(1317, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:14:04'),
(1318, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 22:14:31'),
(1319, 'UPDATE', 'familias', 1, 'sistema', '2024-10-22 22:18:31'),
(1320, 'UPDATE', 'familias', 16, 'sistema', '2024-10-22 22:18:31'),
(1321, 'UPDATE', 'familias', 22, 'sistema', '2024-10-22 22:18:31'),
(1322, 'UPDATE', 'familias', 23, 'sistema', '2024-10-22 22:18:31'),
(1323, 'UPDATE', 'familias', 24, 'sistema', '2024-10-22 22:18:31'),
(1324, 'UPDATE', 'familias', 30, 'sistema', '2024-10-22 22:18:31'),
(1325, 'UPDATE', 'familias', 31, 'sistema', '2024-10-22 22:18:31'),
(1326, 'UPDATE', 'familias', 32, 'sistema', '2024-10-22 22:18:31'),
(1327, 'UPDATE', 'familias', 33, 'sistema', '2024-10-22 22:18:31'),
(1328, 'UPDATE', 'familias', 34, 'sistema', '2024-10-22 22:18:31'),
(1329, 'UPDATE', 'familias', 35, 'sistema', '2024-10-22 22:18:31'),
(1330, 'UPDATE', 'familias', 36, 'sistema', '2024-10-22 22:18:32'),
(1331, 'UPDATE', 'familias', 37, 'sistema', '2024-10-22 22:18:32'),
(1332, 'UPDATE', 'familias', 38, 'sistema', '2024-10-22 22:18:32'),
(1333, 'UPDATE', 'familias', 39, 'sistema', '2024-10-22 22:18:32'),
(1334, 'UPDATE', 'familias', 40, 'sistema', '2024-10-22 22:18:32'),
(1335, 'UPDATE', 'familias', 41, 'sistema', '2024-10-22 22:18:32'),
(1336, 'UPDATE', 'familias', 43, 'sistema', '2024-10-22 22:18:32'),
(1337, 'UPDATE', 'familias', 44, 'sistema', '2024-10-22 22:18:32'),
(1338, 'UPDATE', 'familias', 45, 'sistema', '2024-10-22 22:18:32'),
(1339, 'UPDATE', 'familias', 46, 'sistema', '2024-10-22 22:18:32'),
(1340, 'UPDATE', 'familias', 47, 'sistema', '2024-10-22 22:18:32'),
(1341, 'UPDATE', 'familias', 48, 'sistema', '2024-10-22 22:18:32'),
(1342, 'UPDATE', 'familias', 50, 'sistema', '2024-10-22 22:18:32'),
(1343, 'UPDATE', 'familias', 51, 'sistema', '2024-10-22 22:18:32'),
(1344, 'UPDATE', 'familias', 53, 'sistema', '2024-10-22 22:18:32'),
(1345, 'UPDATE', 'familias', 55, 'sistema', '2024-10-22 22:18:32'),
(1346, 'UPDATE', 'familias', 56, 'sistema', '2024-10-22 22:18:32'),
(1347, 'UPDATE', 'familias', 57, 'sistema', '2024-10-22 22:18:32'),
(1348, 'UPDATE', 'familias', 58, 'sistema', '2024-10-22 22:18:32'),
(1349, 'UPDATE', 'familias', 59, 'sistema', '2024-10-22 22:18:32'),
(1350, 'UPDATE', 'familias', 60, 'sistema', '2024-10-22 22:18:32'),
(1351, 'UPDATE', 'familias', 61, 'sistema', '2024-10-22 22:18:32'),
(1352, 'UPDATE', 'familias', 62, 'sistema', '2024-10-22 22:18:32'),
(1353, 'UPDATE', 'familias', 63, 'sistema', '2024-10-22 22:18:32'),
(1354, 'UPDATE', 'familias', 64, 'sistema', '2024-10-22 22:18:32'),
(1355, 'UPDATE', 'familias', 65, 'sistema', '2024-10-22 22:18:32'),
(1356, 'UPDATE', 'familias', 66, 'sistema', '2024-10-22 22:18:32'),
(1357, 'UPDATE', 'familias', 67, 'sistema', '2024-10-22 22:18:32'),
(1358, 'UPDATE', 'familias', 68, 'sistema', '2024-10-22 22:18:32'),
(1359, 'UPDATE', 'familias', 69, 'sistema', '2024-10-22 22:18:32'),
(1360, 'UPDATE', 'familias', 70, 'sistema', '2024-10-22 22:18:32'),
(1361, 'UPDATE', 'familias', 71, 'sistema', '2024-10-22 22:18:32'),
(1362, 'UPDATE', 'familias', 72, 'sistema', '2024-10-22 22:18:32'),
(1363, 'UPDATE', 'familias', 73, 'sistema', '2024-10-22 22:18:32'),
(1364, 'UPDATE', 'familias', 74, 'sistema', '2024-10-22 22:18:32'),
(1365, 'UPDATE', 'familias', 75, 'sistema', '2024-10-22 22:18:32'),
(1366, 'UPDATE', 'familias', 76, 'sistema', '2024-10-22 22:18:32'),
(1367, 'UPDATE', 'familias', 77, 'sistema', '2024-10-22 22:18:32'),
(1368, 'UPDATE', 'familias', 78, 'sistema', '2024-10-22 22:18:32'),
(1369, 'UPDATE', 'familias', 79, 'sistema', '2024-10-22 22:18:32'),
(1370, 'UPDATE', 'familias', 80, 'sistema', '2024-10-22 22:18:32'),
(1371, 'UPDATE', 'familias', 81, 'sistema', '2024-10-22 22:18:32'),
(1372, 'UPDATE', 'familias', 82, 'sistema', '2024-10-22 22:18:32'),
(1373, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-22 22:18:32'),
(1374, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-22 22:18:32'),
(1375, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-22 22:18:32'),
(1376, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-22 22:18:32'),
(1377, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-22 22:18:32'),
(1378, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-22 22:18:32'),
(1379, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-22 22:18:32'),
(1380, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-22 22:18:32'),
(1381, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-22 22:18:32'),
(1382, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-22 22:18:32'),
(1383, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-22 22:18:32'),
(1384, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-22 22:18:32'),
(1385, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-22 22:18:32'),
(1386, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-22 22:18:32'),
(1387, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-22 22:18:32'),
(1388, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-22 22:18:32'),
(1389, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-22 22:18:32'),
(1390, 'UPDATE', 'grupos', 28, 'Sistema', '2024-10-22 22:18:32'),
(1391, 'UPDATE', 'grupos', 29, 'Sistema', '2024-10-22 22:18:32'),
(1392, 'UPDATE', 'grupos', 30, 'Sistema', '2024-10-22 22:18:32'),
(1393, 'UPDATE', 'grupos', 31, 'Sistema', '2024-10-22 22:18:32'),
(1394, 'UPDATE', 'grupos', 32, 'Sistema', '2024-10-22 22:18:32'),
(1395, 'UPDATE', 'grupos', 33, 'Sistema', '2024-10-22 22:18:32'),
(1396, 'UPDATE', 'grupos', 34, 'Sistema', '2024-10-22 22:18:32'),
(1397, 'UPDATE', 'grupos', 35, 'Sistema', '2024-10-22 22:18:32'),
(1398, 'UPDATE', 'grupos', 36, 'Sistema', '2024-10-22 22:18:32'),
(1399, 'UPDATE', 'grupos', 37, 'Sistema', '2024-10-22 22:18:32'),
(1400, 'UPDATE', 'grupos', 38, 'Sistema', '2024-10-22 22:18:32'),
(1401, 'UPDATE', 'grupos', 39, 'Sistema', '2024-10-22 22:18:32'),
(1402, 'UPDATE', 'grupos', 40, 'Sistema', '2024-10-22 22:18:32'),
(1403, 'UPDATE', 'grupos', 41, 'Sistema', '2024-10-22 22:18:32'),
(1404, 'UPDATE', 'grupos', 42, 'Sistema', '2024-10-22 22:18:32'),
(1405, 'UPDATE', 'grupos', 43, 'Sistema', '2024-10-22 22:18:32'),
(1406, 'UPDATE', 'grupos', 44, 'Sistema', '2024-10-22 22:18:32'),
(1407, 'UPDATE', 'grupos', 45, 'Sistema', '2024-10-22 22:18:32'),
(1408, 'UPDATE', 'grupos', 46, 'Sistema', '2024-10-22 22:18:32'),
(1409, 'UPDATE', 'grupos', 47, 'Sistema', '2024-10-22 22:18:32'),
(1410, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:18:32'),
(1411, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 22:18:32'),
(1412, 'UPDATE', 'usuarios', 5, '5', '2024-10-22 22:18:32'),
(1413, 'UPDATE', 'usuarios', 6, '6', '2024-10-22 22:18:32'),
(1414, 'UPDATE', 'usuarios', 7, '7', '2024-10-22 22:18:32'),
(1415, 'UPDATE', 'usuarios', 8, '8', '2024-10-22 22:18:32'),
(1416, 'UPDATE', 'usuarios', 9, '9', '2024-10-22 22:18:32'),
(1417, 'UPDATE', 'usuarios', 10, '10', '2024-10-22 22:18:32'),
(1418, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:18:32'),
(1419, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:18:32'),
(1420, 'UPDATE', 'usuarios', 16, '16', '2024-10-22 22:18:32'),
(1421, 'UPDATE', 'usuarios', 18, '18', '2024-10-22 22:18:32'),
(1422, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:18:32'),
(1423, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:18:32'),
(1424, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:18:32'),
(1425, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:18:32'),
(1426, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:18:32'),
(1427, 'UPDATE', 'usuarios', 21, '21', '2024-10-22 22:18:32'),
(1428, 'UPDATE', 'usuarios', 26, '26', '2024-10-22 22:18:32'),
(1429, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:18:32'),
(1430, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:18:32'),
(1431, 'UPDATE', 'usuarios', 28, '28', '2024-10-22 22:18:32'),
(1432, 'UPDATE', 'usuarios', 29, '29', '2024-10-22 22:18:32'),
(1433, 'UPDATE', 'usuarios', 30, '30', '2024-10-22 22:18:32'),
(1434, 'UPDATE', 'usuarios', 31, '31', '2024-10-22 22:18:32'),
(1435, 'UPDATE', 'usuarios', 33, '33', '2024-10-22 22:18:32'),
(1436, 'UPDATE', 'usuarios', 34, '34', '2024-10-22 22:18:32'),
(1437, 'UPDATE', 'usuarios', 35, '35', '2024-10-22 22:18:32'),
(1438, 'UPDATE', 'usuarios', 36, '36', '2024-10-22 22:18:32'),
(1439, 'UPDATE', 'usuarios', 37, '37', '2024-10-22 22:18:32'),
(1440, 'UPDATE', 'usuarios', 38, '38', '2024-10-22 22:18:32'),
(1441, 'UPDATE', 'usuarios', 39, '39', '2024-10-22 22:18:32'),
(1442, 'UPDATE', 'usuarios', 40, '40', '2024-10-22 22:18:32'),
(1443, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:18:32'),
(1444, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:18:32'),
(1445, 'UPDATE', 'usuarios', 42, '42', '2024-10-22 22:18:32'),
(1446, 'UPDATE', 'usuarios', 43, '43', '2024-10-22 22:18:32'),
(1447, 'UPDATE', 'usuarios', 44, '44', '2024-10-22 22:18:32'),
(1448, 'UPDATE', 'usuarios', 45, '45', '2024-10-22 22:18:32'),
(1449, 'UPDATE', 'usuarios', 46, '46', '2024-10-22 22:18:32'),
(1450, 'UPDATE', 'usuarios', 47, '47', '2024-10-22 22:18:32'),
(1451, 'UPDATE', 'usuarios', 48, '48', '2024-10-22 22:18:32'),
(1452, 'UPDATE', 'usuarios', 49, '49', '2024-10-22 22:18:32'),
(1453, 'UPDATE', 'usuarios', 50, '50', '2024-10-22 22:18:32'),
(1454, 'UPDATE', 'usuarios', 51, '51', '2024-10-22 22:18:32'),
(1455, 'UPDATE', 'usuarios', 52, '52', '2024-10-22 22:18:32'),
(1456, 'UPDATE', 'usuarios', 53, '53', '2024-10-22 22:18:32'),
(1457, 'UPDATE', 'usuarios', 54, '54', '2024-10-22 22:18:32'),
(1458, 'UPDATE', 'usuarios', 55, '55', '2024-10-22 22:18:32'),
(1459, 'UPDATE', 'usuarios', 56, '56', '2024-10-22 22:18:32'),
(1460, 'UPDATE', 'usuarios', 57, '57', '2024-10-22 22:18:32'),
(1461, 'UPDATE', 'usuarios', 58, '58', '2024-10-22 22:18:32'),
(1462, 'UPDATE', 'usuarios', 59, '59', '2024-10-22 22:18:32'),
(1463, 'UPDATE', 'usuarios', 60, '60', '2024-10-22 22:18:32'),
(1464, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:18:32'),
(1465, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:18:32'),
(1466, 'UPDATE', 'usuarios', 62, '62', '2024-10-22 22:18:32'),
(1467, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:18:32'),
(1468, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:18:32'),
(1469, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:18:32'),
(1470, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:18:32'),
(1471, 'UPDATE', 'usuarios', 64, '64', '2024-10-22 22:18:32'),
(1472, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1473, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1474, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1475, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1476, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1477, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1478, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1479, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1480, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1481, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1482, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1483, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1484, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1485, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1486, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1487, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:18:32'),
(1488, 'UPDATE', 'usuarios', 66, '66', '2024-10-22 22:18:32'),
(1489, 'UPDATE', 'usuarios', 67, '67', '2024-10-22 22:18:32'),
(1490, 'UPDATE', 'usuarios', 70, '70', '2024-10-22 22:18:32'),
(1491, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:18:32'),
(1492, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:18:32'),
(1493, 'UPDATE', 'usuarios', 73, '73', '2024-10-22 22:18:32'),
(1494, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:18:32'),
(1495, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:18:32'),
(1496, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:18:32'),
(1497, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:18:32'),
(1498, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:18:32'),
(1499, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:18:32'),
(1500, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:18:32'),
(1501, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:18:32'),
(1502, 'UPDATE', 'usuarios', 76, '76', '2024-10-22 22:18:32'),
(1503, 'UPDATE', 'usuarios', 77, '77', '2024-10-22 22:18:32'),
(1504, 'UPDATE', 'usuarios', 78, '78', '2024-10-22 22:18:32'),
(1505, 'UPDATE', 'usuarios', 79, '79', '2024-10-22 22:18:32'),
(1506, 'UPDATE', 'usuarios', 80, '80', '2024-10-22 22:18:32'),
(1507, 'UPDATE', 'usuarios', 81, '81', '2024-10-22 22:18:32'),
(1508, 'UPDATE', 'usuarios', 82, '82', '2024-10-22 22:18:32'),
(1509, 'UPDATE', 'usuarios', 85, '85', '2024-10-22 22:18:32'),
(1510, 'UPDATE', 'usuarios', 86, '86', '2024-10-22 22:18:32'),
(1511, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:18:32'),
(1512, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:18:32'),
(1513, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:18:32'),
(1514, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:18:32'),
(1515, 'UPDATE', 'usuarios', 88, '88', '2024-10-22 22:18:32'),
(1516, 'UPDATE', 'usuarios', 89, '89', '2024-10-22 22:18:32'),
(1517, 'UPDATE', 'usuarios', 90, '90', '2024-10-22 22:18:32'),
(1518, 'UPDATE', 'usuarios', 91, '91', '2024-10-22 22:18:32'),
(1519, 'UPDATE', 'usuarios', 92, '92', '2024-10-22 22:18:32'),
(1520, 'UPDATE', 'usuarios', 93, '93', '2024-10-22 22:18:32'),
(1521, 'UPDATE', 'usuarios', 94, '94', '2024-10-22 22:18:32'),
(1522, 'UPDATE', 'usuarios', 95, '95', '2024-10-22 22:18:32'),
(1523, 'UPDATE', 'usuarios', 96, '96', '2024-10-22 22:18:32'),
(1524, 'UPDATE', 'usuarios', 97, '97', '2024-10-22 22:18:32'),
(1525, 'UPDATE', 'usuarios', 98, '98', '2024-10-22 22:18:32'),
(1526, 'UPDATE', 'usuarios', 99, '99', '2024-10-22 22:18:32'),
(1527, 'UPDATE', 'usuarios', 100, '100', '2024-10-22 22:18:32'),
(1528, 'UPDATE', 'usuarios', 101, '101', '2024-10-22 22:18:32'),
(1529, 'UPDATE', 'usuarios', 102, '102', '2024-10-22 22:18:32'),
(1530, 'UPDATE', 'usuarios', 103, '103', '2024-10-22 22:18:32'),
(1531, 'UPDATE', 'usuarios', 104, '104', '2024-10-22 22:18:32'),
(1532, 'UPDATE', 'usuarios', 105, '105', '2024-10-22 22:18:32'),
(1533, 'UPDATE', 'usuarios', 106, '106', '2024-10-22 22:18:32'),
(1534, 'UPDATE', 'usuarios', 107, '107', '2024-10-22 22:18:32'),
(1535, 'UPDATE', 'usuarios', 108, '108', '2024-10-22 22:18:32'),
(1536, 'UPDATE', 'usuarios', 109, '109', '2024-10-22 22:18:32'),
(1537, 'UPDATE', 'usuarios', 110, '110', '2024-10-22 22:18:32'),
(1538, 'UPDATE', 'usuarios', 111, '111', '2024-10-22 22:18:32'),
(1539, 'UPDATE', 'usuarios', 112, '112', '2024-10-22 22:18:32'),
(1540, 'UPDATE', 'usuarios', 113, '113', '2024-10-22 22:18:32'),
(1541, 'UPDATE', 'usuarios', 114, '114', '2024-10-22 22:18:32'),
(1542, 'UPDATE', 'usuarios', 115, '115', '2024-10-22 22:18:32'),
(1543, 'UPDATE', 'usuarios', 116, '116', '2024-10-22 22:18:32'),
(1544, 'UPDATE', 'usuarios', 117, '117', '2024-10-22 22:18:32'),
(1545, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:18:32'),
(1546, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:18:32'),
(1547, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1548, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1549, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1550, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1551, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1552, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1553, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1554, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1555, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:18:32'),
(1556, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1557, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1558, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1559, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1560, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1561, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1562, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1563, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1564, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1565, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1566, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1567, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1568, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1569, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1570, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1571, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1572, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1573, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1574, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1575, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1576, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1577, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1578, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1579, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1580, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1581, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1582, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:32'),
(1583, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1584, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1585, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1586, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1587, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1588, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1589, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1590, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1591, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1592, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1593, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1594, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1595, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1596, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1597, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1598, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1599, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1600, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1601, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1602, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1603, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1604, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1605, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:18:33'),
(1606, 'UPDATE', 'usuarios', 121, '121', '2024-10-22 22:18:33'),
(1607, 'UPDATE', 'familias', 1, 'sistema', '2024-10-22 22:22:07'),
(1608, 'UPDATE', 'familias', 16, 'sistema', '2024-10-22 22:22:07'),
(1609, 'UPDATE', 'familias', 22, 'sistema', '2024-10-22 22:22:07'),
(1610, 'UPDATE', 'familias', 23, 'sistema', '2024-10-22 22:22:07'),
(1611, 'UPDATE', 'familias', 24, 'sistema', '2024-10-22 22:22:07'),
(1612, 'UPDATE', 'familias', 30, 'sistema', '2024-10-22 22:22:07'),
(1613, 'UPDATE', 'familias', 31, 'sistema', '2024-10-22 22:22:07'),
(1614, 'UPDATE', 'familias', 32, 'sistema', '2024-10-22 22:22:07'),
(1615, 'UPDATE', 'familias', 33, 'sistema', '2024-10-22 22:22:07'),
(1616, 'UPDATE', 'familias', 34, 'sistema', '2024-10-22 22:22:07'),
(1617, 'UPDATE', 'familias', 35, 'sistema', '2024-10-22 22:22:07'),
(1618, 'UPDATE', 'familias', 36, 'sistema', '2024-10-22 22:22:07'),
(1619, 'UPDATE', 'familias', 37, 'sistema', '2024-10-22 22:22:07'),
(1620, 'UPDATE', 'familias', 38, 'sistema', '2024-10-22 22:22:07'),
(1621, 'UPDATE', 'familias', 39, 'sistema', '2024-10-22 22:22:07');
INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(1622, 'UPDATE', 'familias', 40, 'sistema', '2024-10-22 22:22:07'),
(1623, 'UPDATE', 'familias', 41, 'sistema', '2024-10-22 22:22:07'),
(1624, 'UPDATE', 'familias', 43, 'sistema', '2024-10-22 22:22:07'),
(1625, 'UPDATE', 'familias', 44, 'sistema', '2024-10-22 22:22:07'),
(1626, 'UPDATE', 'familias', 45, 'sistema', '2024-10-22 22:22:07'),
(1627, 'UPDATE', 'familias', 46, 'sistema', '2024-10-22 22:22:07'),
(1628, 'UPDATE', 'familias', 47, 'sistema', '2024-10-22 22:22:07'),
(1629, 'UPDATE', 'familias', 48, 'sistema', '2024-10-22 22:22:07'),
(1630, 'UPDATE', 'familias', 50, 'sistema', '2024-10-22 22:22:07'),
(1631, 'UPDATE', 'familias', 51, 'sistema', '2024-10-22 22:22:07'),
(1632, 'UPDATE', 'familias', 53, 'sistema', '2024-10-22 22:22:07'),
(1633, 'UPDATE', 'familias', 55, 'sistema', '2024-10-22 22:22:07'),
(1634, 'UPDATE', 'familias', 56, 'sistema', '2024-10-22 22:22:07'),
(1635, 'UPDATE', 'familias', 57, 'sistema', '2024-10-22 22:22:07'),
(1636, 'UPDATE', 'familias', 58, 'sistema', '2024-10-22 22:22:07'),
(1637, 'UPDATE', 'familias', 59, 'sistema', '2024-10-22 22:22:07'),
(1638, 'UPDATE', 'familias', 60, 'sistema', '2024-10-22 22:22:07'),
(1639, 'UPDATE', 'familias', 61, 'sistema', '2024-10-22 22:22:07'),
(1640, 'UPDATE', 'familias', 62, 'sistema', '2024-10-22 22:22:07'),
(1641, 'UPDATE', 'familias', 63, 'sistema', '2024-10-22 22:22:07'),
(1642, 'UPDATE', 'familias', 64, 'sistema', '2024-10-22 22:22:07'),
(1643, 'UPDATE', 'familias', 65, 'sistema', '2024-10-22 22:22:07'),
(1644, 'UPDATE', 'familias', 66, 'sistema', '2024-10-22 22:22:07'),
(1645, 'UPDATE', 'familias', 67, 'sistema', '2024-10-22 22:22:07'),
(1646, 'UPDATE', 'familias', 68, 'sistema', '2024-10-22 22:22:07'),
(1647, 'UPDATE', 'familias', 69, 'sistema', '2024-10-22 22:22:07'),
(1648, 'UPDATE', 'familias', 70, 'sistema', '2024-10-22 22:22:07'),
(1649, 'UPDATE', 'familias', 71, 'sistema', '2024-10-22 22:22:07'),
(1650, 'UPDATE', 'familias', 72, 'sistema', '2024-10-22 22:22:07'),
(1651, 'UPDATE', 'familias', 73, 'sistema', '2024-10-22 22:22:07'),
(1652, 'UPDATE', 'familias', 74, 'sistema', '2024-10-22 22:22:07'),
(1653, 'UPDATE', 'familias', 75, 'sistema', '2024-10-22 22:22:07'),
(1654, 'UPDATE', 'familias', 76, 'sistema', '2024-10-22 22:22:07'),
(1655, 'UPDATE', 'familias', 77, 'sistema', '2024-10-22 22:22:07'),
(1656, 'UPDATE', 'familias', 78, 'sistema', '2024-10-22 22:22:07'),
(1657, 'UPDATE', 'familias', 79, 'sistema', '2024-10-22 22:22:07'),
(1658, 'UPDATE', 'familias', 80, 'sistema', '2024-10-22 22:22:07'),
(1659, 'UPDATE', 'familias', 81, 'sistema', '2024-10-22 22:22:07'),
(1660, 'UPDATE', 'familias', 82, 'sistema', '2024-10-22 22:22:07'),
(1661, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-22 22:22:07'),
(1662, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-22 22:22:07'),
(1663, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-22 22:22:07'),
(1664, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-22 22:22:07'),
(1665, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-22 22:22:07'),
(1666, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-22 22:22:07'),
(1667, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-22 22:22:07'),
(1668, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-22 22:22:07'),
(1669, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-22 22:22:07'),
(1670, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-22 22:22:07'),
(1671, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-22 22:22:07'),
(1672, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-22 22:22:07'),
(1673, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-22 22:22:07'),
(1674, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-22 22:22:07'),
(1675, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-22 22:22:07'),
(1676, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-22 22:22:07'),
(1677, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-22 22:22:07'),
(1678, 'UPDATE', 'grupos', 28, 'Sistema', '2024-10-22 22:22:07'),
(1679, 'UPDATE', 'grupos', 29, 'Sistema', '2024-10-22 22:22:07'),
(1680, 'UPDATE', 'grupos', 30, 'Sistema', '2024-10-22 22:22:07'),
(1681, 'UPDATE', 'grupos', 31, 'Sistema', '2024-10-22 22:22:07'),
(1682, 'UPDATE', 'grupos', 32, 'Sistema', '2024-10-22 22:22:07'),
(1683, 'UPDATE', 'grupos', 33, 'Sistema', '2024-10-22 22:22:07'),
(1684, 'UPDATE', 'grupos', 34, 'Sistema', '2024-10-22 22:22:07'),
(1685, 'UPDATE', 'grupos', 35, 'Sistema', '2024-10-22 22:22:07'),
(1686, 'UPDATE', 'grupos', 36, 'Sistema', '2024-10-22 22:22:07'),
(1687, 'UPDATE', 'grupos', 37, 'Sistema', '2024-10-22 22:22:07'),
(1688, 'UPDATE', 'grupos', 38, 'Sistema', '2024-10-22 22:22:07'),
(1689, 'UPDATE', 'grupos', 39, 'Sistema', '2024-10-22 22:22:07'),
(1690, 'UPDATE', 'grupos', 40, 'Sistema', '2024-10-22 22:22:07'),
(1691, 'UPDATE', 'grupos', 41, 'Sistema', '2024-10-22 22:22:07'),
(1692, 'UPDATE', 'grupos', 42, 'Sistema', '2024-10-22 22:22:07'),
(1693, 'UPDATE', 'grupos', 43, 'Sistema', '2024-10-22 22:22:07'),
(1694, 'UPDATE', 'grupos', 44, 'Sistema', '2024-10-22 22:22:07'),
(1695, 'UPDATE', 'grupos', 45, 'Sistema', '2024-10-22 22:22:07'),
(1696, 'UPDATE', 'grupos', 46, 'Sistema', '2024-10-22 22:22:07'),
(1697, 'UPDATE', 'grupos', 47, 'Sistema', '2024-10-22 22:22:07'),
(1698, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:22:07'),
(1699, 'UPDATE', 'familias', 1, 'sistema', '2024-10-22 22:22:58'),
(1700, 'UPDATE', 'familias', 16, 'sistema', '2024-10-22 22:22:58'),
(1701, 'UPDATE', 'familias', 22, 'sistema', '2024-10-22 22:22:58'),
(1702, 'UPDATE', 'familias', 23, 'sistema', '2024-10-22 22:22:58'),
(1703, 'UPDATE', 'familias', 24, 'sistema', '2024-10-22 22:22:58'),
(1704, 'UPDATE', 'familias', 30, 'sistema', '2024-10-22 22:22:58'),
(1705, 'UPDATE', 'familias', 31, 'sistema', '2024-10-22 22:22:58'),
(1706, 'UPDATE', 'familias', 32, 'sistema', '2024-10-22 22:22:58'),
(1707, 'UPDATE', 'familias', 33, 'sistema', '2024-10-22 22:22:58'),
(1708, 'UPDATE', 'familias', 34, 'sistema', '2024-10-22 22:22:58'),
(1709, 'UPDATE', 'familias', 35, 'sistema', '2024-10-22 22:22:58'),
(1710, 'UPDATE', 'familias', 36, 'sistema', '2024-10-22 22:22:58'),
(1711, 'UPDATE', 'familias', 37, 'sistema', '2024-10-22 22:22:58'),
(1712, 'UPDATE', 'familias', 38, 'sistema', '2024-10-22 22:22:58'),
(1713, 'UPDATE', 'familias', 39, 'sistema', '2024-10-22 22:22:58'),
(1714, 'UPDATE', 'familias', 40, 'sistema', '2024-10-22 22:22:58'),
(1715, 'UPDATE', 'familias', 41, 'sistema', '2024-10-22 22:22:58'),
(1716, 'UPDATE', 'familias', 43, 'sistema', '2024-10-22 22:22:58'),
(1717, 'UPDATE', 'familias', 44, 'sistema', '2024-10-22 22:22:58'),
(1718, 'UPDATE', 'familias', 45, 'sistema', '2024-10-22 22:22:58'),
(1719, 'UPDATE', 'familias', 46, 'sistema', '2024-10-22 22:22:58'),
(1720, 'UPDATE', 'familias', 47, 'sistema', '2024-10-22 22:22:58'),
(1721, 'UPDATE', 'familias', 48, 'sistema', '2024-10-22 22:22:58'),
(1722, 'UPDATE', 'familias', 50, 'sistema', '2024-10-22 22:22:58'),
(1723, 'UPDATE', 'familias', 51, 'sistema', '2024-10-22 22:22:58'),
(1724, 'UPDATE', 'familias', 53, 'sistema', '2024-10-22 22:22:58'),
(1725, 'UPDATE', 'familias', 55, 'sistema', '2024-10-22 22:22:58'),
(1726, 'UPDATE', 'familias', 56, 'sistema', '2024-10-22 22:22:58'),
(1727, 'UPDATE', 'familias', 57, 'sistema', '2024-10-22 22:22:58'),
(1728, 'UPDATE', 'familias', 58, 'sistema', '2024-10-22 22:22:58'),
(1729, 'UPDATE', 'familias', 59, 'sistema', '2024-10-22 22:22:58'),
(1730, 'UPDATE', 'familias', 60, 'sistema', '2024-10-22 22:22:58'),
(1731, 'UPDATE', 'familias', 61, 'sistema', '2024-10-22 22:22:58'),
(1732, 'UPDATE', 'familias', 62, 'sistema', '2024-10-22 22:22:58'),
(1733, 'UPDATE', 'familias', 63, 'sistema', '2024-10-22 22:22:58'),
(1734, 'UPDATE', 'familias', 64, 'sistema', '2024-10-22 22:22:58'),
(1735, 'UPDATE', 'familias', 65, 'sistema', '2024-10-22 22:22:58'),
(1736, 'UPDATE', 'familias', 66, 'sistema', '2024-10-22 22:22:58'),
(1737, 'UPDATE', 'familias', 67, 'sistema', '2024-10-22 22:22:58'),
(1738, 'UPDATE', 'familias', 68, 'sistema', '2024-10-22 22:22:58'),
(1739, 'UPDATE', 'familias', 69, 'sistema', '2024-10-22 22:22:58'),
(1740, 'UPDATE', 'familias', 70, 'sistema', '2024-10-22 22:22:58'),
(1741, 'UPDATE', 'familias', 71, 'sistema', '2024-10-22 22:22:58'),
(1742, 'UPDATE', 'familias', 72, 'sistema', '2024-10-22 22:22:58'),
(1743, 'UPDATE', 'familias', 73, 'sistema', '2024-10-22 22:22:58'),
(1744, 'UPDATE', 'familias', 74, 'sistema', '2024-10-22 22:22:58'),
(1745, 'UPDATE', 'familias', 75, 'sistema', '2024-10-22 22:22:58'),
(1746, 'UPDATE', 'familias', 76, 'sistema', '2024-10-22 22:22:58'),
(1747, 'UPDATE', 'familias', 77, 'sistema', '2024-10-22 22:22:58'),
(1748, 'UPDATE', 'familias', 78, 'sistema', '2024-10-22 22:22:58'),
(1749, 'UPDATE', 'familias', 79, 'sistema', '2024-10-22 22:22:58'),
(1750, 'UPDATE', 'familias', 80, 'sistema', '2024-10-22 22:22:58'),
(1751, 'UPDATE', 'familias', 81, 'sistema', '2024-10-22 22:22:58'),
(1752, 'UPDATE', 'familias', 82, 'sistema', '2024-10-22 22:22:58'),
(1753, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-22 22:22:58'),
(1754, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-22 22:22:58'),
(1755, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-22 22:22:58'),
(1756, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-22 22:22:58'),
(1757, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-22 22:22:58'),
(1758, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-22 22:22:58'),
(1759, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-22 22:22:58'),
(1760, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-22 22:22:58'),
(1761, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-22 22:22:58'),
(1762, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-22 22:22:58'),
(1763, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-22 22:22:58'),
(1764, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-22 22:22:58'),
(1765, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-22 22:22:58'),
(1766, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-22 22:22:58'),
(1767, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-22 22:22:58'),
(1768, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-22 22:22:58'),
(1769, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-22 22:22:58'),
(1770, 'UPDATE', 'grupos', 28, 'Sistema', '2024-10-22 22:22:58'),
(1771, 'UPDATE', 'grupos', 29, 'Sistema', '2024-10-22 22:22:58'),
(1772, 'UPDATE', 'grupos', 30, 'Sistema', '2024-10-22 22:22:58'),
(1773, 'UPDATE', 'grupos', 31, 'Sistema', '2024-10-22 22:22:58'),
(1774, 'UPDATE', 'grupos', 32, 'Sistema', '2024-10-22 22:22:58'),
(1775, 'UPDATE', 'grupos', 33, 'Sistema', '2024-10-22 22:22:58'),
(1776, 'UPDATE', 'grupos', 34, 'Sistema', '2024-10-22 22:22:58'),
(1777, 'UPDATE', 'grupos', 35, 'Sistema', '2024-10-22 22:22:58'),
(1778, 'UPDATE', 'grupos', 36, 'Sistema', '2024-10-22 22:22:58'),
(1779, 'UPDATE', 'grupos', 37, 'Sistema', '2024-10-22 22:22:58'),
(1780, 'UPDATE', 'grupos', 38, 'Sistema', '2024-10-22 22:22:58'),
(1781, 'UPDATE', 'grupos', 39, 'Sistema', '2024-10-22 22:22:58'),
(1782, 'UPDATE', 'grupos', 40, 'Sistema', '2024-10-22 22:22:58'),
(1783, 'UPDATE', 'grupos', 41, 'Sistema', '2024-10-22 22:22:58'),
(1784, 'UPDATE', 'grupos', 42, 'Sistema', '2024-10-22 22:22:58'),
(1785, 'UPDATE', 'grupos', 43, 'Sistema', '2024-10-22 22:22:58'),
(1786, 'UPDATE', 'grupos', 44, 'Sistema', '2024-10-22 22:22:58'),
(1787, 'UPDATE', 'grupos', 45, 'Sistema', '2024-10-22 22:22:58'),
(1788, 'UPDATE', 'grupos', 46, 'Sistema', '2024-10-22 22:22:58'),
(1789, 'UPDATE', 'grupos', 47, 'Sistema', '2024-10-22 22:22:58'),
(1790, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:22:58'),
(1791, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 22:22:58'),
(1792, 'UPDATE', 'usuarios', 5, '5', '2024-10-22 22:22:58'),
(1793, 'UPDATE', 'usuarios', 6, '6', '2024-10-22 22:22:58'),
(1794, 'UPDATE', 'usuarios', 7, '7', '2024-10-22 22:22:58'),
(1795, 'UPDATE', 'usuarios', 8, '8', '2024-10-22 22:22:58'),
(1796, 'UPDATE', 'usuarios', 9, '9', '2024-10-22 22:22:58'),
(1797, 'UPDATE', 'usuarios', 10, '10', '2024-10-22 22:22:58'),
(1798, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:22:58'),
(1799, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:22:58'),
(1800, 'UPDATE', 'usuarios', 16, '16', '2024-10-22 22:22:58'),
(1801, 'UPDATE', 'usuarios', 18, '18', '2024-10-22 22:22:58'),
(1802, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:22:58'),
(1803, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:22:58'),
(1804, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:22:58'),
(1805, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:22:58'),
(1806, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:22:58'),
(1807, 'UPDATE', 'usuarios', 21, '21', '2024-10-22 22:22:58'),
(1808, 'UPDATE', 'usuarios', 26, '26', '2024-10-22 22:22:58'),
(1809, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:22:58'),
(1810, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:22:58'),
(1811, 'UPDATE', 'usuarios', 28, '28', '2024-10-22 22:22:58'),
(1812, 'UPDATE', 'usuarios', 29, '29', '2024-10-22 22:22:58'),
(1813, 'UPDATE', 'usuarios', 30, '30', '2024-10-22 22:22:58'),
(1814, 'UPDATE', 'usuarios', 31, '31', '2024-10-22 22:22:58'),
(1815, 'UPDATE', 'usuarios', 33, '33', '2024-10-22 22:22:58'),
(1816, 'UPDATE', 'usuarios', 34, '34', '2024-10-22 22:22:58'),
(1817, 'UPDATE', 'usuarios', 35, '35', '2024-10-22 22:22:58'),
(1818, 'UPDATE', 'usuarios', 36, '36', '2024-10-22 22:22:58'),
(1819, 'UPDATE', 'usuarios', 37, '37', '2024-10-22 22:22:58'),
(1820, 'UPDATE', 'usuarios', 38, '38', '2024-10-22 22:22:58'),
(1821, 'UPDATE', 'usuarios', 39, '39', '2024-10-22 22:22:58'),
(1822, 'UPDATE', 'usuarios', 40, '40', '2024-10-22 22:22:58'),
(1823, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:22:58'),
(1824, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:22:58'),
(1825, 'UPDATE', 'usuarios', 42, '42', '2024-10-22 22:22:58'),
(1826, 'UPDATE', 'usuarios', 43, '43', '2024-10-22 22:22:58'),
(1827, 'UPDATE', 'usuarios', 44, '44', '2024-10-22 22:22:58'),
(1828, 'UPDATE', 'usuarios', 45, '45', '2024-10-22 22:22:58'),
(1829, 'UPDATE', 'usuarios', 46, '46', '2024-10-22 22:22:58'),
(1830, 'UPDATE', 'usuarios', 47, '47', '2024-10-22 22:22:58'),
(1831, 'UPDATE', 'usuarios', 48, '48', '2024-10-22 22:22:58'),
(1832, 'UPDATE', 'usuarios', 49, '49', '2024-10-22 22:22:58'),
(1833, 'UPDATE', 'usuarios', 50, '50', '2024-10-22 22:22:58'),
(1834, 'UPDATE', 'usuarios', 51, '51', '2024-10-22 22:22:58'),
(1835, 'UPDATE', 'usuarios', 52, '52', '2024-10-22 22:22:58'),
(1836, 'UPDATE', 'usuarios', 53, '53', '2024-10-22 22:22:58'),
(1837, 'UPDATE', 'usuarios', 54, '54', '2024-10-22 22:22:58'),
(1838, 'UPDATE', 'usuarios', 55, '55', '2024-10-22 22:22:58'),
(1839, 'UPDATE', 'usuarios', 56, '56', '2024-10-22 22:22:58'),
(1840, 'UPDATE', 'usuarios', 57, '57', '2024-10-22 22:22:58'),
(1841, 'UPDATE', 'usuarios', 58, '58', '2024-10-22 22:22:58'),
(1842, 'UPDATE', 'usuarios', 59, '59', '2024-10-22 22:22:58'),
(1843, 'UPDATE', 'usuarios', 60, '60', '2024-10-22 22:22:58'),
(1844, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:22:58'),
(1845, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:22:58'),
(1846, 'UPDATE', 'usuarios', 62, '62', '2024-10-22 22:22:58'),
(1847, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:22:58'),
(1848, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:22:58'),
(1849, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:22:58'),
(1850, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:22:58'),
(1851, 'UPDATE', 'usuarios', 64, '64', '2024-10-22 22:22:58'),
(1852, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1853, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1854, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1855, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1856, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1857, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1858, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1859, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1860, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1861, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1862, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1863, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1864, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1865, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1866, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1867, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:22:58'),
(1868, 'UPDATE', 'usuarios', 66, '66', '2024-10-22 22:22:58'),
(1869, 'UPDATE', 'usuarios', 67, '67', '2024-10-22 22:22:58'),
(1870, 'UPDATE', 'usuarios', 70, '70', '2024-10-22 22:22:58'),
(1871, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:22:58'),
(1872, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:22:58'),
(1873, 'UPDATE', 'usuarios', 73, '73', '2024-10-22 22:22:58'),
(1874, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:22:58'),
(1875, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:22:58'),
(1876, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:22:58'),
(1877, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:22:58'),
(1878, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:22:58'),
(1879, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:22:58'),
(1880, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:22:58'),
(1881, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:22:58'),
(1882, 'UPDATE', 'usuarios', 76, '76', '2024-10-22 22:22:58'),
(1883, 'UPDATE', 'usuarios', 77, '77', '2024-10-22 22:22:58'),
(1884, 'UPDATE', 'usuarios', 78, '78', '2024-10-22 22:22:58'),
(1885, 'UPDATE', 'usuarios', 79, '79', '2024-10-22 22:22:58'),
(1886, 'UPDATE', 'usuarios', 80, '80', '2024-10-22 22:22:58'),
(1887, 'UPDATE', 'usuarios', 81, '81', '2024-10-22 22:22:58'),
(1888, 'UPDATE', 'usuarios', 82, '82', '2024-10-22 22:22:58'),
(1889, 'UPDATE', 'usuarios', 85, '85', '2024-10-22 22:22:58'),
(1890, 'UPDATE', 'usuarios', 86, '86', '2024-10-22 22:22:58'),
(1891, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:22:58'),
(1892, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:22:58'),
(1893, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:22:58'),
(1894, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:22:58'),
(1895, 'UPDATE', 'usuarios', 88, '88', '2024-10-22 22:22:58'),
(1896, 'UPDATE', 'usuarios', 89, '89', '2024-10-22 22:22:58'),
(1897, 'UPDATE', 'usuarios', 90, '90', '2024-10-22 22:22:58'),
(1898, 'UPDATE', 'usuarios', 91, '91', '2024-10-22 22:22:58'),
(1899, 'UPDATE', 'usuarios', 92, '92', '2024-10-22 22:22:58'),
(1900, 'UPDATE', 'usuarios', 93, '93', '2024-10-22 22:22:58'),
(1901, 'UPDATE', 'usuarios', 94, '94', '2024-10-22 22:22:58'),
(1902, 'UPDATE', 'usuarios', 95, '95', '2024-10-22 22:22:58'),
(1903, 'UPDATE', 'usuarios', 96, '96', '2024-10-22 22:22:58'),
(1904, 'UPDATE', 'usuarios', 97, '97', '2024-10-22 22:22:58'),
(1905, 'UPDATE', 'usuarios', 98, '98', '2024-10-22 22:22:58'),
(1906, 'UPDATE', 'usuarios', 99, '99', '2024-10-22 22:22:58'),
(1907, 'UPDATE', 'usuarios', 100, '100', '2024-10-22 22:22:58'),
(1908, 'UPDATE', 'usuarios', 101, '101', '2024-10-22 22:22:58'),
(1909, 'UPDATE', 'usuarios', 102, '102', '2024-10-22 22:22:58'),
(1910, 'UPDATE', 'usuarios', 103, '103', '2024-10-22 22:22:58'),
(1911, 'UPDATE', 'usuarios', 104, '104', '2024-10-22 22:22:58'),
(1912, 'UPDATE', 'usuarios', 105, '105', '2024-10-22 22:22:58'),
(1913, 'UPDATE', 'usuarios', 106, '106', '2024-10-22 22:22:58'),
(1914, 'UPDATE', 'usuarios', 107, '107', '2024-10-22 22:22:58'),
(1915, 'UPDATE', 'usuarios', 108, '108', '2024-10-22 22:22:58'),
(1916, 'UPDATE', 'usuarios', 109, '109', '2024-10-22 22:22:58'),
(1917, 'UPDATE', 'usuarios', 110, '110', '2024-10-22 22:22:58'),
(1918, 'UPDATE', 'usuarios', 111, '111', '2024-10-22 22:22:58'),
(1919, 'UPDATE', 'usuarios', 112, '112', '2024-10-22 22:22:58'),
(1920, 'UPDATE', 'usuarios', 113, '113', '2024-10-22 22:22:58'),
(1921, 'UPDATE', 'usuarios', 114, '114', '2024-10-22 22:22:58'),
(1922, 'UPDATE', 'usuarios', 115, '115', '2024-10-22 22:22:58'),
(1923, 'UPDATE', 'usuarios', 116, '116', '2024-10-22 22:22:58'),
(1924, 'UPDATE', 'usuarios', 117, '117', '2024-10-22 22:22:58'),
(1925, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:22:58'),
(1926, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:22:58'),
(1927, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1928, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1929, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1930, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1931, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1932, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1933, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1934, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1935, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:22:58'),
(1936, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1937, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1938, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1939, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1940, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1941, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1942, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1943, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1944, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1945, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1946, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1947, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1948, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1949, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1950, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1951, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1952, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1953, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1954, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1955, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1956, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1957, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1958, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1959, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1960, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1961, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1962, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1963, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1964, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1965, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1966, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1967, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1968, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1969, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1970, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1971, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1972, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1973, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1974, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1975, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1976, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1977, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1978, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1979, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1980, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1981, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1982, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1983, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1984, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1985, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:22:58'),
(1986, 'UPDATE', 'usuarios', 121, '121', '2024-10-22 22:22:58'),
(1987, 'UPDATE', 'familias', 1, 'sistema', '2024-10-22 22:28:54'),
(1988, 'UPDATE', 'familias', 16, 'sistema', '2024-10-22 22:28:54'),
(1989, 'UPDATE', 'familias', 22, 'sistema', '2024-10-22 22:28:54'),
(1990, 'UPDATE', 'familias', 23, 'sistema', '2024-10-22 22:28:54'),
(1991, 'UPDATE', 'familias', 24, 'sistema', '2024-10-22 22:28:54'),
(1992, 'UPDATE', 'familias', 30, 'sistema', '2024-10-22 22:28:54'),
(1993, 'UPDATE', 'familias', 31, 'sistema', '2024-10-22 22:28:54'),
(1994, 'UPDATE', 'familias', 32, 'sistema', '2024-10-22 22:28:54'),
(1995, 'UPDATE', 'familias', 33, 'sistema', '2024-10-22 22:28:54'),
(1996, 'UPDATE', 'familias', 34, 'sistema', '2024-10-22 22:28:54'),
(1997, 'UPDATE', 'familias', 35, 'sistema', '2024-10-22 22:28:54'),
(1998, 'UPDATE', 'familias', 36, 'sistema', '2024-10-22 22:28:54'),
(1999, 'UPDATE', 'familias', 37, 'sistema', '2024-10-22 22:28:54'),
(2000, 'UPDATE', 'familias', 38, 'sistema', '2024-10-22 22:28:54'),
(2001, 'UPDATE', 'familias', 39, 'sistema', '2024-10-22 22:28:54'),
(2002, 'UPDATE', 'familias', 40, 'sistema', '2024-10-22 22:28:54'),
(2003, 'UPDATE', 'familias', 41, 'sistema', '2024-10-22 22:28:54'),
(2004, 'UPDATE', 'familias', 43, 'sistema', '2024-10-22 22:28:54'),
(2005, 'UPDATE', 'familias', 44, 'sistema', '2024-10-22 22:28:54'),
(2006, 'UPDATE', 'familias', 45, 'sistema', '2024-10-22 22:28:54'),
(2007, 'UPDATE', 'familias', 46, 'sistema', '2024-10-22 22:28:54'),
(2008, 'UPDATE', 'familias', 47, 'sistema', '2024-10-22 22:28:54'),
(2009, 'UPDATE', 'familias', 48, 'sistema', '2024-10-22 22:28:54'),
(2010, 'UPDATE', 'familias', 50, 'sistema', '2024-10-22 22:28:54'),
(2011, 'UPDATE', 'familias', 51, 'sistema', '2024-10-22 22:28:54'),
(2012, 'UPDATE', 'familias', 53, 'sistema', '2024-10-22 22:28:54'),
(2013, 'UPDATE', 'familias', 55, 'sistema', '2024-10-22 22:28:54'),
(2014, 'UPDATE', 'familias', 56, 'sistema', '2024-10-22 22:28:54'),
(2015, 'UPDATE', 'familias', 57, 'sistema', '2024-10-22 22:28:54'),
(2016, 'UPDATE', 'familias', 58, 'sistema', '2024-10-22 22:28:54'),
(2017, 'UPDATE', 'familias', 59, 'sistema', '2024-10-22 22:28:54'),
(2018, 'UPDATE', 'familias', 60, 'sistema', '2024-10-22 22:28:54'),
(2019, 'UPDATE', 'familias', 61, 'sistema', '2024-10-22 22:28:54'),
(2020, 'UPDATE', 'familias', 62, 'sistema', '2024-10-22 22:28:54'),
(2021, 'UPDATE', 'familias', 63, 'sistema', '2024-10-22 22:28:54'),
(2022, 'UPDATE', 'familias', 64, 'sistema', '2024-10-22 22:28:54'),
(2023, 'UPDATE', 'familias', 65, 'sistema', '2024-10-22 22:28:54'),
(2024, 'UPDATE', 'familias', 66, 'sistema', '2024-10-22 22:28:54'),
(2025, 'UPDATE', 'familias', 67, 'sistema', '2024-10-22 22:28:54'),
(2026, 'UPDATE', 'familias', 68, 'sistema', '2024-10-22 22:28:54'),
(2027, 'UPDATE', 'familias', 69, 'sistema', '2024-10-22 22:28:54'),
(2028, 'UPDATE', 'familias', 70, 'sistema', '2024-10-22 22:28:54'),
(2029, 'UPDATE', 'familias', 71, 'sistema', '2024-10-22 22:28:54'),
(2030, 'UPDATE', 'familias', 72, 'sistema', '2024-10-22 22:28:54'),
(2031, 'UPDATE', 'familias', 73, 'sistema', '2024-10-22 22:28:54'),
(2032, 'UPDATE', 'familias', 74, 'sistema', '2024-10-22 22:28:54'),
(2033, 'UPDATE', 'familias', 75, 'sistema', '2024-10-22 22:28:54'),
(2034, 'UPDATE', 'familias', 76, 'sistema', '2024-10-22 22:28:54'),
(2035, 'UPDATE', 'familias', 77, 'sistema', '2024-10-22 22:28:54'),
(2036, 'UPDATE', 'familias', 78, 'sistema', '2024-10-22 22:28:54'),
(2037, 'UPDATE', 'familias', 79, 'sistema', '2024-10-22 22:28:54'),
(2038, 'UPDATE', 'familias', 80, 'sistema', '2024-10-22 22:28:54'),
(2039, 'UPDATE', 'familias', 81, 'sistema', '2024-10-22 22:28:54'),
(2040, 'UPDATE', 'familias', 82, 'sistema', '2024-10-22 22:28:54'),
(2041, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-22 22:28:54'),
(2042, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-22 22:28:54'),
(2043, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-22 22:28:54'),
(2044, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-22 22:28:54'),
(2045, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-22 22:28:54'),
(2046, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-22 22:28:54'),
(2047, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-22 22:28:54'),
(2048, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-22 22:28:54'),
(2049, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-22 22:28:54'),
(2050, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-22 22:28:54'),
(2051, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-22 22:28:54'),
(2052, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-22 22:28:54'),
(2053, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-22 22:28:54'),
(2054, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-22 22:28:54'),
(2055, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-22 22:28:54'),
(2056, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-22 22:28:54'),
(2057, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-22 22:28:54'),
(2058, 'UPDATE', 'grupos', 28, 'Sistema', '2024-10-22 22:28:54'),
(2059, 'UPDATE', 'grupos', 29, 'Sistema', '2024-10-22 22:28:54'),
(2060, 'UPDATE', 'grupos', 30, 'Sistema', '2024-10-22 22:28:54'),
(2061, 'UPDATE', 'grupos', 31, 'Sistema', '2024-10-22 22:28:54'),
(2062, 'UPDATE', 'grupos', 32, 'Sistema', '2024-10-22 22:28:54'),
(2063, 'UPDATE', 'grupos', 33, 'Sistema', '2024-10-22 22:28:54'),
(2064, 'UPDATE', 'grupos', 34, 'Sistema', '2024-10-22 22:28:54'),
(2065, 'UPDATE', 'grupos', 35, 'Sistema', '2024-10-22 22:28:54'),
(2066, 'UPDATE', 'grupos', 36, 'Sistema', '2024-10-22 22:28:54'),
(2067, 'UPDATE', 'grupos', 37, 'Sistema', '2024-10-22 22:28:54'),
(2068, 'UPDATE', 'grupos', 38, 'Sistema', '2024-10-22 22:28:54'),
(2069, 'UPDATE', 'grupos', 39, 'Sistema', '2024-10-22 22:28:54'),
(2070, 'UPDATE', 'grupos', 40, 'Sistema', '2024-10-22 22:28:54'),
(2071, 'UPDATE', 'grupos', 41, 'Sistema', '2024-10-22 22:28:54'),
(2072, 'UPDATE', 'grupos', 42, 'Sistema', '2024-10-22 22:28:54'),
(2073, 'UPDATE', 'grupos', 43, 'Sistema', '2024-10-22 22:28:54'),
(2074, 'UPDATE', 'grupos', 44, 'Sistema', '2024-10-22 22:28:54'),
(2075, 'UPDATE', 'grupos', 45, 'Sistema', '2024-10-22 22:28:54'),
(2076, 'UPDATE', 'grupos', 46, 'Sistema', '2024-10-22 22:28:54'),
(2077, 'UPDATE', 'grupos', 47, 'Sistema', '2024-10-22 22:28:54'),
(2078, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:28:54'),
(2079, 'UPDATE', 'usuarios', 1, '1', '2024-10-22 22:28:54'),
(2080, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 22:28:54'),
(2081, 'UPDATE', 'usuarios', 2, '2', '2024-10-22 22:28:54'),
(2082, 'UPDATE', 'usuarios', 5, '5', '2024-10-22 22:28:54'),
(2083, 'UPDATE', 'usuarios', 6, '6', '2024-10-22 22:28:54'),
(2084, 'UPDATE', 'usuarios', 7, '7', '2024-10-22 22:28:54'),
(2085, 'UPDATE', 'usuarios', 8, '8', '2024-10-22 22:28:54'),
(2086, 'UPDATE', 'usuarios', 9, '9', '2024-10-22 22:28:54'),
(2087, 'UPDATE', 'usuarios', 10, '10', '2024-10-22 22:28:54'),
(2088, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:28:54'),
(2089, 'UPDATE', 'usuarios', 11, '11', '2024-10-22 22:28:54'),
(2090, 'UPDATE', 'usuarios', 16, '16', '2024-10-22 22:28:54'),
(2091, 'UPDATE', 'usuarios', 18, '18', '2024-10-22 22:28:54'),
(2092, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:28:54'),
(2093, 'UPDATE', 'usuarios', 19, '19', '2024-10-22 22:28:54'),
(2094, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:28:54'),
(2095, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:28:54'),
(2096, 'UPDATE', 'usuarios', 20, '20', '2024-10-22 22:28:54'),
(2097, 'UPDATE', 'usuarios', 21, '21', '2024-10-22 22:28:54'),
(2098, 'UPDATE', 'usuarios', 26, '26', '2024-10-22 22:28:54'),
(2099, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:28:54'),
(2100, 'UPDATE', 'usuarios', 27, '27', '2024-10-22 22:28:54'),
(2101, 'UPDATE', 'usuarios', 28, '28', '2024-10-22 22:28:54'),
(2102, 'UPDATE', 'usuarios', 29, '29', '2024-10-22 22:28:54'),
(2103, 'UPDATE', 'usuarios', 30, '30', '2024-10-22 22:28:54'),
(2104, 'UPDATE', 'usuarios', 31, '31', '2024-10-22 22:28:54'),
(2105, 'UPDATE', 'usuarios', 33, '33', '2024-10-22 22:28:54'),
(2106, 'UPDATE', 'usuarios', 34, '34', '2024-10-22 22:28:54'),
(2107, 'UPDATE', 'usuarios', 35, '35', '2024-10-22 22:28:54'),
(2108, 'UPDATE', 'usuarios', 36, '36', '2024-10-22 22:28:54'),
(2109, 'UPDATE', 'usuarios', 37, '37', '2024-10-22 22:28:54'),
(2110, 'UPDATE', 'usuarios', 38, '38', '2024-10-22 22:28:54'),
(2111, 'UPDATE', 'usuarios', 39, '39', '2024-10-22 22:28:54'),
(2112, 'UPDATE', 'usuarios', 40, '40', '2024-10-22 22:28:54'),
(2113, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:28:54'),
(2114, 'UPDATE', 'usuarios', 41, '41', '2024-10-22 22:28:54'),
(2115, 'UPDATE', 'usuarios', 42, '42', '2024-10-22 22:28:54'),
(2116, 'UPDATE', 'usuarios', 43, '43', '2024-10-22 22:28:54'),
(2117, 'UPDATE', 'usuarios', 44, '44', '2024-10-22 22:28:54'),
(2118, 'UPDATE', 'usuarios', 45, '45', '2024-10-22 22:28:54'),
(2119, 'UPDATE', 'usuarios', 46, '46', '2024-10-22 22:28:54'),
(2120, 'UPDATE', 'usuarios', 47, '47', '2024-10-22 22:28:54'),
(2121, 'UPDATE', 'usuarios', 48, '48', '2024-10-22 22:28:54'),
(2122, 'UPDATE', 'usuarios', 49, '49', '2024-10-22 22:28:54'),
(2123, 'UPDATE', 'usuarios', 50, '50', '2024-10-22 22:28:54'),
(2124, 'UPDATE', 'usuarios', 51, '51', '2024-10-22 22:28:54'),
(2125, 'UPDATE', 'usuarios', 52, '52', '2024-10-22 22:28:54'),
(2126, 'UPDATE', 'usuarios', 53, '53', '2024-10-22 22:28:54'),
(2127, 'UPDATE', 'usuarios', 54, '54', '2024-10-22 22:28:54'),
(2128, 'UPDATE', 'usuarios', 55, '55', '2024-10-22 22:28:54'),
(2129, 'UPDATE', 'usuarios', 56, '56', '2024-10-22 22:28:54'),
(2130, 'UPDATE', 'usuarios', 57, '57', '2024-10-22 22:28:54'),
(2131, 'UPDATE', 'usuarios', 58, '58', '2024-10-22 22:28:54'),
(2132, 'UPDATE', 'usuarios', 59, '59', '2024-10-22 22:28:54'),
(2133, 'UPDATE', 'usuarios', 60, '60', '2024-10-22 22:28:54'),
(2134, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:28:54'),
(2135, 'UPDATE', 'usuarios', 61, '61', '2024-10-22 22:28:54'),
(2136, 'UPDATE', 'usuarios', 62, '62', '2024-10-22 22:28:54'),
(2137, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:28:54'),
(2138, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:28:54'),
(2139, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:28:54'),
(2140, 'UPDATE', 'usuarios', 63, '63', '2024-10-22 22:28:54'),
(2141, 'UPDATE', 'usuarios', 64, '64', '2024-10-22 22:28:54'),
(2142, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2143, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2144, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2145, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2146, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2147, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2148, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2149, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2150, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2151, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2152, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2153, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2154, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2155, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2156, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2157, 'UPDATE', 'usuarios', 65, '65', '2024-10-22 22:28:54'),
(2158, 'UPDATE', 'usuarios', 66, '66', '2024-10-22 22:28:54'),
(2159, 'UPDATE', 'usuarios', 67, '67', '2024-10-22 22:28:54'),
(2160, 'UPDATE', 'usuarios', 70, '70', '2024-10-22 22:28:54'),
(2161, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:28:54'),
(2162, 'UPDATE', 'usuarios', 72, '72', '2024-10-22 22:28:54'),
(2163, 'UPDATE', 'usuarios', 73, '73', '2024-10-22 22:28:54'),
(2164, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:28:54'),
(2165, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:28:54'),
(2166, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:28:54'),
(2167, 'UPDATE', 'usuarios', 74, '74', '2024-10-22 22:28:54'),
(2168, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:28:54'),
(2169, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:28:54'),
(2170, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:28:54'),
(2171, 'UPDATE', 'usuarios', 75, '75', '2024-10-22 22:28:54'),
(2172, 'UPDATE', 'usuarios', 76, '76', '2024-10-22 22:28:54'),
(2173, 'UPDATE', 'usuarios', 77, '77', '2024-10-22 22:28:54'),
(2174, 'UPDATE', 'usuarios', 78, '78', '2024-10-22 22:28:54'),
(2175, 'UPDATE', 'usuarios', 79, '79', '2024-10-22 22:28:54'),
(2176, 'UPDATE', 'usuarios', 80, '80', '2024-10-22 22:28:54'),
(2177, 'UPDATE', 'usuarios', 81, '81', '2024-10-22 22:28:54'),
(2178, 'UPDATE', 'usuarios', 82, '82', '2024-10-22 22:28:54'),
(2179, 'UPDATE', 'usuarios', 85, '85', '2024-10-22 22:28:54'),
(2180, 'UPDATE', 'usuarios', 86, '86', '2024-10-22 22:28:54'),
(2181, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:28:54'),
(2182, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:28:54'),
(2183, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:28:54'),
(2184, 'UPDATE', 'usuarios', 87, '87', '2024-10-22 22:28:54'),
(2185, 'UPDATE', 'usuarios', 88, '88', '2024-10-22 22:28:54'),
(2186, 'UPDATE', 'usuarios', 89, '89', '2024-10-22 22:28:54'),
(2187, 'UPDATE', 'usuarios', 90, '90', '2024-10-22 22:28:54'),
(2188, 'UPDATE', 'usuarios', 91, '91', '2024-10-22 22:28:54'),
(2189, 'UPDATE', 'usuarios', 92, '92', '2024-10-22 22:28:54'),
(2190, 'UPDATE', 'usuarios', 93, '93', '2024-10-22 22:28:54'),
(2191, 'UPDATE', 'usuarios', 94, '94', '2024-10-22 22:28:54'),
(2192, 'UPDATE', 'usuarios', 95, '95', '2024-10-22 22:28:54'),
(2193, 'UPDATE', 'usuarios', 96, '96', '2024-10-22 22:28:54'),
(2194, 'UPDATE', 'usuarios', 97, '97', '2024-10-22 22:28:54'),
(2195, 'UPDATE', 'usuarios', 98, '98', '2024-10-22 22:28:54'),
(2196, 'UPDATE', 'usuarios', 99, '99', '2024-10-22 22:28:54'),
(2197, 'UPDATE', 'usuarios', 100, '100', '2024-10-22 22:28:54'),
(2198, 'UPDATE', 'usuarios', 101, '101', '2024-10-22 22:28:54'),
(2199, 'UPDATE', 'usuarios', 102, '102', '2024-10-22 22:28:54'),
(2200, 'UPDATE', 'usuarios', 103, '103', '2024-10-22 22:28:54'),
(2201, 'UPDATE', 'usuarios', 104, '104', '2024-10-22 22:28:54'),
(2202, 'UPDATE', 'usuarios', 105, '105', '2024-10-22 22:28:54'),
(2203, 'UPDATE', 'usuarios', 106, '106', '2024-10-22 22:28:54'),
(2204, 'UPDATE', 'usuarios', 107, '107', '2024-10-22 22:28:54'),
(2205, 'UPDATE', 'usuarios', 108, '108', '2024-10-22 22:28:54'),
(2206, 'UPDATE', 'usuarios', 109, '109', '2024-10-22 22:28:54'),
(2207, 'UPDATE', 'usuarios', 110, '110', '2024-10-22 22:28:54'),
(2208, 'UPDATE', 'usuarios', 111, '111', '2024-10-22 22:28:54'),
(2209, 'UPDATE', 'usuarios', 112, '112', '2024-10-22 22:28:54'),
(2210, 'UPDATE', 'usuarios', 113, '113', '2024-10-22 22:28:54'),
(2211, 'UPDATE', 'usuarios', 114, '114', '2024-10-22 22:28:54'),
(2212, 'UPDATE', 'usuarios', 115, '115', '2024-10-22 22:28:54'),
(2213, 'UPDATE', 'usuarios', 116, '116', '2024-10-22 22:28:54'),
(2214, 'UPDATE', 'usuarios', 117, '117', '2024-10-22 22:28:54'),
(2215, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:28:54'),
(2216, 'UPDATE', 'usuarios', 118, '118', '2024-10-22 22:28:54'),
(2217, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2218, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2219, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2220, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2221, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2222, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2223, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2224, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2225, 'UPDATE', 'usuarios', 119, '119', '2024-10-22 22:28:54'),
(2226, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2227, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2228, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2229, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2230, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2231, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2232, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2233, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2234, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2235, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2236, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:54'),
(2237, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2238, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2239, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2240, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2241, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2242, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2243, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2244, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2245, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2246, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2247, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2248, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2249, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2250, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2251, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2252, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2253, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2254, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2255, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2256, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2257, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2258, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2259, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2260, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2261, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2262, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2263, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2264, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2265, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2266, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2267, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2268, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2269, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2270, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2271, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2272, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2273, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2274, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2275, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2276, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2277, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2278, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2279, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2280, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2281, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2282, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2283, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2284, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2285, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2286, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2287, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2288, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2289, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2290, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2291, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2292, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2293, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2294, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2295, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2296, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2297, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2298, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2299, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2300, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2301, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2302, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2303, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2304, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2305, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2306, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2307, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2308, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2309, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2310, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2311, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2312, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2313, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2314, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2315, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2316, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2317, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2318, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2319, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2320, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2321, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2322, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2323, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2324, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2325, 'UPDATE', 'usuarios', 120, '120', '2024-10-22 22:28:55'),
(2326, 'UPDATE', 'usuarios', 121, '121', '2024-10-22 22:28:55'),
(2327, 'INSERT', 'usuarios', 122, '122', '2024-10-22 22:34:26'),
(2328, 'INSERT', 'usuarios', 122, '122', '2024-10-22 22:34:26'),
(2329, 'INSERT', 'usuarios', 122, 'sistema', '2024-10-22 22:34:26'),
(2330, 'INSERT', 'usuarios', 123, '123', '2024-10-22 23:15:56'),
(2331, 'INSERT', 'usuarios', 123, '123', '2024-10-22 23:15:56'),
(2332, 'INSERT', 'usuarios', 123, 'sistema', '2024-10-22 23:15:56'),
(2333, 'UPDATE', 'usuarios', 1, '1', '2024-10-23 09:26:08'),
(2334, 'UPDATE', 'usuarios', 2, '2', '2024-10-23 09:26:15'),
(2335, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:26:34'),
(2336, 'UPDATE', 'usuarios', 1, '1', '2024-10-23 09:58:58'),
(2337, 'UPDATE', 'familias', 1, 'sistema', '2024-10-23 09:59:14'),
(2338, 'UPDATE', 'familias', 16, 'sistema', '2024-10-23 09:59:14'),
(2339, 'UPDATE', 'familias', 22, 'sistema', '2024-10-23 09:59:14'),
(2340, 'UPDATE', 'familias', 23, 'sistema', '2024-10-23 09:59:14'),
(2341, 'UPDATE', 'familias', 24, 'sistema', '2024-10-23 09:59:14'),
(2342, 'UPDATE', 'familias', 30, 'sistema', '2024-10-23 09:59:14'),
(2343, 'UPDATE', 'familias', 31, 'sistema', '2024-10-23 09:59:14'),
(2344, 'UPDATE', 'familias', 32, 'sistema', '2024-10-23 09:59:14'),
(2345, 'UPDATE', 'familias', 33, 'sistema', '2024-10-23 09:59:14'),
(2346, 'UPDATE', 'familias', 34, 'sistema', '2024-10-23 09:59:14'),
(2347, 'UPDATE', 'familias', 35, 'sistema', '2024-10-23 09:59:14'),
(2348, 'UPDATE', 'familias', 36, 'sistema', '2024-10-23 09:59:14'),
(2349, 'UPDATE', 'familias', 37, 'sistema', '2024-10-23 09:59:14'),
(2350, 'UPDATE', 'familias', 38, 'sistema', '2024-10-23 09:59:14'),
(2351, 'UPDATE', 'familias', 39, 'sistema', '2024-10-23 09:59:14'),
(2352, 'UPDATE', 'familias', 40, 'sistema', '2024-10-23 09:59:14'),
(2353, 'UPDATE', 'familias', 41, 'sistema', '2024-10-23 09:59:14'),
(2354, 'UPDATE', 'familias', 43, 'sistema', '2024-10-23 09:59:14'),
(2355, 'UPDATE', 'familias', 44, 'sistema', '2024-10-23 09:59:14'),
(2356, 'UPDATE', 'familias', 45, 'sistema', '2024-10-23 09:59:14'),
(2357, 'UPDATE', 'familias', 46, 'sistema', '2024-10-23 09:59:14'),
(2358, 'UPDATE', 'familias', 47, 'sistema', '2024-10-23 09:59:14'),
(2359, 'UPDATE', 'familias', 48, 'sistema', '2024-10-23 09:59:14'),
(2360, 'UPDATE', 'familias', 50, 'sistema', '2024-10-23 09:59:14'),
(2361, 'UPDATE', 'familias', 51, 'sistema', '2024-10-23 09:59:14'),
(2362, 'UPDATE', 'familias', 53, 'sistema', '2024-10-23 09:59:14'),
(2363, 'UPDATE', 'familias', 55, 'sistema', '2024-10-23 09:59:14'),
(2364, 'UPDATE', 'familias', 56, 'sistema', '2024-10-23 09:59:14'),
(2365, 'UPDATE', 'familias', 57, 'sistema', '2024-10-23 09:59:14'),
(2366, 'UPDATE', 'familias', 58, 'sistema', '2024-10-23 09:59:14'),
(2367, 'UPDATE', 'familias', 59, 'sistema', '2024-10-23 09:59:14'),
(2368, 'UPDATE', 'familias', 60, 'sistema', '2024-10-23 09:59:14'),
(2369, 'UPDATE', 'familias', 61, 'sistema', '2024-10-23 09:59:14'),
(2370, 'UPDATE', 'familias', 62, 'sistema', '2024-10-23 09:59:14'),
(2371, 'UPDATE', 'familias', 63, 'sistema', '2024-10-23 09:59:14'),
(2372, 'UPDATE', 'familias', 64, 'sistema', '2024-10-23 09:59:14'),
(2373, 'UPDATE', 'familias', 65, 'sistema', '2024-10-23 09:59:14'),
(2374, 'UPDATE', 'familias', 66, 'sistema', '2024-10-23 09:59:14'),
(2375, 'UPDATE', 'familias', 67, 'sistema', '2024-10-23 09:59:14'),
(2376, 'UPDATE', 'familias', 68, 'sistema', '2024-10-23 09:59:14'),
(2377, 'UPDATE', 'familias', 69, 'sistema', '2024-10-23 09:59:14'),
(2378, 'UPDATE', 'familias', 70, 'sistema', '2024-10-23 09:59:14'),
(2379, 'UPDATE', 'familias', 71, 'sistema', '2024-10-23 09:59:14'),
(2380, 'UPDATE', 'familias', 72, 'sistema', '2024-10-23 09:59:14'),
(2381, 'UPDATE', 'familias', 73, 'sistema', '2024-10-23 09:59:14'),
(2382, 'UPDATE', 'familias', 74, 'sistema', '2024-10-23 09:59:14'),
(2383, 'UPDATE', 'familias', 75, 'sistema', '2024-10-23 09:59:14'),
(2384, 'UPDATE', 'familias', 76, 'sistema', '2024-10-23 09:59:14'),
(2385, 'UPDATE', 'familias', 77, 'sistema', '2024-10-23 09:59:14'),
(2386, 'UPDATE', 'familias', 78, 'sistema', '2024-10-23 09:59:14'),
(2387, 'UPDATE', 'familias', 79, 'sistema', '2024-10-23 09:59:14'),
(2388, 'UPDATE', 'familias', 80, 'sistema', '2024-10-23 09:59:14'),
(2389, 'UPDATE', 'familias', 81, 'sistema', '2024-10-23 09:59:14'),
(2390, 'UPDATE', 'familias', 82, 'sistema', '2024-10-23 09:59:14'),
(2391, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-23 09:59:14'),
(2392, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-23 09:59:14'),
(2393, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-23 09:59:14'),
(2394, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-23 09:59:14'),
(2395, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-23 09:59:14'),
(2396, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-23 09:59:14'),
(2397, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-23 09:59:14'),
(2398, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-23 09:59:14'),
(2399, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-23 09:59:14'),
(2400, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-23 09:59:14'),
(2401, 'UPDATE', 'grupos', 21, 'Sistema', '2024-10-23 09:59:14'),
(2402, 'UPDATE', 'grupos', 22, 'Sistema', '2024-10-23 09:59:14'),
(2403, 'UPDATE', 'grupos', 23, 'Sistema', '2024-10-23 09:59:14'),
(2404, 'UPDATE', 'grupos', 24, 'Sistema', '2024-10-23 09:59:14'),
(2405, 'UPDATE', 'grupos', 25, 'Sistema', '2024-10-23 09:59:14'),
(2406, 'UPDATE', 'grupos', 26, 'Sistema', '2024-10-23 09:59:14'),
(2407, 'UPDATE', 'grupos', 27, 'Sistema', '2024-10-23 09:59:14'),
(2408, 'UPDATE', 'grupos', 28, 'Sistema', '2024-10-23 09:59:14'),
(2409, 'UPDATE', 'grupos', 29, 'Sistema', '2024-10-23 09:59:14');
INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(2410, 'UPDATE', 'grupos', 30, 'Sistema', '2024-10-23 09:59:14'),
(2411, 'UPDATE', 'grupos', 31, 'Sistema', '2024-10-23 09:59:14'),
(2412, 'UPDATE', 'grupos', 32, 'Sistema', '2024-10-23 09:59:14'),
(2413, 'UPDATE', 'grupos', 33, 'Sistema', '2024-10-23 09:59:14'),
(2414, 'UPDATE', 'grupos', 34, 'Sistema', '2024-10-23 09:59:14'),
(2415, 'UPDATE', 'grupos', 35, 'Sistema', '2024-10-23 09:59:14'),
(2416, 'UPDATE', 'grupos', 36, 'Sistema', '2024-10-23 09:59:14'),
(2417, 'UPDATE', 'grupos', 37, 'Sistema', '2024-10-23 09:59:14'),
(2418, 'UPDATE', 'grupos', 38, 'Sistema', '2024-10-23 09:59:14'),
(2419, 'UPDATE', 'grupos', 39, 'Sistema', '2024-10-23 09:59:14'),
(2420, 'UPDATE', 'grupos', 40, 'Sistema', '2024-10-23 09:59:14'),
(2421, 'UPDATE', 'grupos', 41, 'Sistema', '2024-10-23 09:59:14'),
(2422, 'UPDATE', 'grupos', 42, 'Sistema', '2024-10-23 09:59:14'),
(2423, 'UPDATE', 'grupos', 43, 'Sistema', '2024-10-23 09:59:14'),
(2424, 'UPDATE', 'grupos', 44, 'Sistema', '2024-10-23 09:59:14'),
(2425, 'UPDATE', 'grupos', 45, 'Sistema', '2024-10-23 09:59:14'),
(2426, 'UPDATE', 'grupos', 46, 'Sistema', '2024-10-23 09:59:14'),
(2427, 'UPDATE', 'grupos', 47, 'Sistema', '2024-10-23 09:59:14'),
(2428, 'UPDATE', 'usuarios', 1, '1', '2024-10-23 09:59:14'),
(2429, 'UPDATE', 'usuarios', 1, '1', '2024-10-23 09:59:14'),
(2430, 'UPDATE', 'usuarios', 2, '2', '2024-10-23 09:59:14'),
(2431, 'UPDATE', 'usuarios', 5, '5', '2024-10-23 09:59:14'),
(2432, 'UPDATE', 'usuarios', 6, '6', '2024-10-23 09:59:14'),
(2433, 'UPDATE', 'usuarios', 7, '7', '2024-10-23 09:59:14'),
(2434, 'UPDATE', 'usuarios', 8, '8', '2024-10-23 09:59:14'),
(2435, 'UPDATE', 'usuarios', 9, '9', '2024-10-23 09:59:14'),
(2436, 'UPDATE', 'usuarios', 10, '10', '2024-10-23 09:59:14'),
(2437, 'UPDATE', 'usuarios', 11, '11', '2024-10-23 09:59:14'),
(2438, 'UPDATE', 'usuarios', 11, '11', '2024-10-23 09:59:14'),
(2439, 'UPDATE', 'usuarios', 16, '16', '2024-10-23 09:59:14'),
(2440, 'UPDATE', 'usuarios', 18, '18', '2024-10-23 09:59:14'),
(2441, 'UPDATE', 'usuarios', 19, '19', '2024-10-23 09:59:14'),
(2442, 'UPDATE', 'usuarios', 19, '19', '2024-10-23 09:59:14'),
(2443, 'UPDATE', 'usuarios', 20, '20', '2024-10-23 09:59:14'),
(2444, 'UPDATE', 'usuarios', 20, '20', '2024-10-23 09:59:14'),
(2445, 'UPDATE', 'usuarios', 20, '20', '2024-10-23 09:59:14'),
(2446, 'UPDATE', 'usuarios', 21, '21', '2024-10-23 09:59:14'),
(2447, 'UPDATE', 'usuarios', 26, '26', '2024-10-23 09:59:14'),
(2448, 'UPDATE', 'usuarios', 27, '27', '2024-10-23 09:59:14'),
(2449, 'UPDATE', 'usuarios', 27, '27', '2024-10-23 09:59:14'),
(2450, 'UPDATE', 'usuarios', 28, '28', '2024-10-23 09:59:14'),
(2451, 'UPDATE', 'usuarios', 29, '29', '2024-10-23 09:59:14'),
(2452, 'UPDATE', 'usuarios', 30, '30', '2024-10-23 09:59:14'),
(2453, 'UPDATE', 'usuarios', 31, '31', '2024-10-23 09:59:14'),
(2454, 'UPDATE', 'usuarios', 33, '33', '2024-10-23 09:59:14'),
(2455, 'UPDATE', 'usuarios', 34, '34', '2024-10-23 09:59:14'),
(2456, 'UPDATE', 'usuarios', 35, '35', '2024-10-23 09:59:14'),
(2457, 'UPDATE', 'usuarios', 36, '36', '2024-10-23 09:59:14'),
(2458, 'UPDATE', 'usuarios', 37, '37', '2024-10-23 09:59:14'),
(2459, 'UPDATE', 'usuarios', 38, '38', '2024-10-23 09:59:14'),
(2460, 'UPDATE', 'usuarios', 39, '39', '2024-10-23 09:59:14'),
(2461, 'UPDATE', 'usuarios', 40, '40', '2024-10-23 09:59:14'),
(2462, 'UPDATE', 'usuarios', 41, '41', '2024-10-23 09:59:14'),
(2463, 'UPDATE', 'usuarios', 41, '41', '2024-10-23 09:59:14'),
(2464, 'UPDATE', 'usuarios', 42, '42', '2024-10-23 09:59:15'),
(2465, 'UPDATE', 'usuarios', 43, '43', '2024-10-23 09:59:15'),
(2466, 'UPDATE', 'usuarios', 44, '44', '2024-10-23 09:59:15'),
(2467, 'UPDATE', 'usuarios', 45, '45', '2024-10-23 09:59:15'),
(2468, 'UPDATE', 'usuarios', 46, '46', '2024-10-23 09:59:15'),
(2469, 'UPDATE', 'usuarios', 47, '47', '2024-10-23 09:59:15'),
(2470, 'UPDATE', 'usuarios', 48, '48', '2024-10-23 09:59:15'),
(2471, 'UPDATE', 'usuarios', 49, '49', '2024-10-23 09:59:15'),
(2472, 'UPDATE', 'usuarios', 50, '50', '2024-10-23 09:59:15'),
(2473, 'UPDATE', 'usuarios', 51, '51', '2024-10-23 09:59:15'),
(2474, 'UPDATE', 'usuarios', 52, '52', '2024-10-23 09:59:15'),
(2475, 'UPDATE', 'usuarios', 53, '53', '2024-10-23 09:59:15'),
(2476, 'UPDATE', 'usuarios', 54, '54', '2024-10-23 09:59:15'),
(2477, 'UPDATE', 'usuarios', 55, '55', '2024-10-23 09:59:15'),
(2478, 'UPDATE', 'usuarios', 56, '56', '2024-10-23 09:59:15'),
(2479, 'UPDATE', 'usuarios', 57, '57', '2024-10-23 09:59:15'),
(2480, 'UPDATE', 'usuarios', 58, '58', '2024-10-23 09:59:15'),
(2481, 'UPDATE', 'usuarios', 59, '59', '2024-10-23 09:59:15'),
(2482, 'UPDATE', 'usuarios', 60, '60', '2024-10-23 09:59:15'),
(2483, 'UPDATE', 'usuarios', 61, '61', '2024-10-23 09:59:15'),
(2484, 'UPDATE', 'usuarios', 61, '61', '2024-10-23 09:59:15'),
(2485, 'UPDATE', 'usuarios', 62, '62', '2024-10-23 09:59:15'),
(2486, 'UPDATE', 'usuarios', 63, '63', '2024-10-23 09:59:15'),
(2487, 'UPDATE', 'usuarios', 63, '63', '2024-10-23 09:59:15'),
(2488, 'UPDATE', 'usuarios', 63, '63', '2024-10-23 09:59:15'),
(2489, 'UPDATE', 'usuarios', 63, '63', '2024-10-23 09:59:15'),
(2490, 'UPDATE', 'usuarios', 64, '64', '2024-10-23 09:59:15'),
(2491, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2492, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2493, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2494, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2495, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2496, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2497, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2498, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2499, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2500, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2501, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2502, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2503, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2504, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2505, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2506, 'UPDATE', 'usuarios', 65, '65', '2024-10-23 09:59:15'),
(2507, 'UPDATE', 'usuarios', 66, '66', '2024-10-23 09:59:15'),
(2508, 'UPDATE', 'usuarios', 67, '67', '2024-10-23 09:59:15'),
(2509, 'UPDATE', 'usuarios', 70, '70', '2024-10-23 09:59:15'),
(2510, 'UPDATE', 'usuarios', 72, '72', '2024-10-23 09:59:15'),
(2511, 'UPDATE', 'usuarios', 72, '72', '2024-10-23 09:59:15'),
(2512, 'UPDATE', 'usuarios', 73, '73', '2024-10-23 09:59:15'),
(2513, 'UPDATE', 'usuarios', 74, '74', '2024-10-23 09:59:15'),
(2514, 'UPDATE', 'usuarios', 74, '74', '2024-10-23 09:59:15'),
(2515, 'UPDATE', 'usuarios', 74, '74', '2024-10-23 09:59:15'),
(2516, 'UPDATE', 'usuarios', 74, '74', '2024-10-23 09:59:15'),
(2517, 'UPDATE', 'usuarios', 75, '75', '2024-10-23 09:59:15'),
(2518, 'UPDATE', 'usuarios', 75, '75', '2024-10-23 09:59:15'),
(2519, 'UPDATE', 'usuarios', 75, '75', '2024-10-23 09:59:15'),
(2520, 'UPDATE', 'usuarios', 75, '75', '2024-10-23 09:59:15'),
(2521, 'UPDATE', 'usuarios', 76, '76', '2024-10-23 09:59:15'),
(2522, 'UPDATE', 'usuarios', 77, '77', '2024-10-23 09:59:15'),
(2523, 'UPDATE', 'usuarios', 78, '78', '2024-10-23 09:59:15'),
(2524, 'UPDATE', 'usuarios', 79, '79', '2024-10-23 09:59:15'),
(2525, 'UPDATE', 'usuarios', 80, '80', '2024-10-23 09:59:15'),
(2526, 'UPDATE', 'usuarios', 81, '81', '2024-10-23 09:59:15'),
(2527, 'UPDATE', 'usuarios', 82, '82', '2024-10-23 09:59:15'),
(2528, 'UPDATE', 'usuarios', 85, '85', '2024-10-23 09:59:15'),
(2529, 'UPDATE', 'usuarios', 86, '86', '2024-10-23 09:59:15'),
(2530, 'UPDATE', 'usuarios', 87, '87', '2024-10-23 09:59:15'),
(2531, 'UPDATE', 'usuarios', 87, '87', '2024-10-23 09:59:15'),
(2532, 'UPDATE', 'usuarios', 87, '87', '2024-10-23 09:59:15'),
(2533, 'UPDATE', 'usuarios', 87, '87', '2024-10-23 09:59:15'),
(2534, 'UPDATE', 'usuarios', 88, '88', '2024-10-23 09:59:15'),
(2535, 'UPDATE', 'usuarios', 89, '89', '2024-10-23 09:59:15'),
(2536, 'UPDATE', 'usuarios', 90, '90', '2024-10-23 09:59:15'),
(2537, 'UPDATE', 'usuarios', 91, '91', '2024-10-23 09:59:15'),
(2538, 'UPDATE', 'usuarios', 92, '92', '2024-10-23 09:59:15'),
(2539, 'UPDATE', 'usuarios', 93, '93', '2024-10-23 09:59:15'),
(2540, 'UPDATE', 'usuarios', 94, '94', '2024-10-23 09:59:15'),
(2541, 'UPDATE', 'usuarios', 95, '95', '2024-10-23 09:59:15'),
(2542, 'UPDATE', 'usuarios', 96, '96', '2024-10-23 09:59:15'),
(2543, 'UPDATE', 'usuarios', 97, '97', '2024-10-23 09:59:15'),
(2544, 'UPDATE', 'usuarios', 98, '98', '2024-10-23 09:59:15'),
(2545, 'UPDATE', 'usuarios', 99, '99', '2024-10-23 09:59:15'),
(2546, 'UPDATE', 'usuarios', 100, '100', '2024-10-23 09:59:15'),
(2547, 'UPDATE', 'usuarios', 101, '101', '2024-10-23 09:59:15'),
(2548, 'UPDATE', 'usuarios', 102, '102', '2024-10-23 09:59:15'),
(2549, 'UPDATE', 'usuarios', 103, '103', '2024-10-23 09:59:15'),
(2550, 'UPDATE', 'usuarios', 104, '104', '2024-10-23 09:59:15'),
(2551, 'UPDATE', 'usuarios', 105, '105', '2024-10-23 09:59:15'),
(2552, 'UPDATE', 'usuarios', 106, '106', '2024-10-23 09:59:15'),
(2553, 'UPDATE', 'usuarios', 107, '107', '2024-10-23 09:59:15'),
(2554, 'UPDATE', 'usuarios', 108, '108', '2024-10-23 09:59:15'),
(2555, 'UPDATE', 'usuarios', 109, '109', '2024-10-23 09:59:15'),
(2556, 'UPDATE', 'usuarios', 110, '110', '2024-10-23 09:59:15'),
(2557, 'UPDATE', 'usuarios', 111, '111', '2024-10-23 09:59:15'),
(2558, 'UPDATE', 'usuarios', 112, '112', '2024-10-23 09:59:15'),
(2559, 'UPDATE', 'usuarios', 113, '113', '2024-10-23 09:59:15'),
(2560, 'UPDATE', 'usuarios', 114, '114', '2024-10-23 09:59:15'),
(2561, 'UPDATE', 'usuarios', 115, '115', '2024-10-23 09:59:15'),
(2562, 'UPDATE', 'usuarios', 116, '116', '2024-10-23 09:59:15'),
(2563, 'UPDATE', 'usuarios', 117, '117', '2024-10-23 09:59:15'),
(2564, 'UPDATE', 'usuarios', 118, '118', '2024-10-23 09:59:15'),
(2565, 'UPDATE', 'usuarios', 118, '118', '2024-10-23 09:59:15'),
(2566, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2567, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2568, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2569, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2570, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2571, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2572, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2573, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2574, 'UPDATE', 'usuarios', 119, '119', '2024-10-23 09:59:15'),
(2575, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2576, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2577, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2578, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2579, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2580, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2581, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2582, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2583, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2584, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2585, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2586, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2587, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2588, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2589, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2590, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2591, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2592, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2593, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2594, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2595, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2596, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2597, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2598, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2599, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2600, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2601, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2602, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2603, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2604, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2605, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2606, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2607, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2608, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2609, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2610, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2611, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2612, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2613, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2614, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2615, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2616, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2617, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2618, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2619, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2620, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2621, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2622, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2623, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2624, 'UPDATE', 'usuarios', 120, '120', '2024-10-23 09:59:15'),
(2625, 'UPDATE', 'usuarios', 121, '121', '2024-10-23 09:59:15'),
(2626, 'UPDATE', 'usuarios', 122, '122', '2024-10-23 09:59:15'),
(2627, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 09:59:15'),
(2628, 'INSERT', 'usuarios', 124, '124', '2024-10-23 10:03:05'),
(2629, 'INSERT', 'usuarios', 124, '124', '2024-10-23 10:03:05'),
(2630, 'INSERT', 'usuarios', 124, 'sistema', '2024-10-23 10:03:05'),
(2631, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 14:28:52'),
(2632, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 14:40:31'),
(2633, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 14:42:01'),
(2634, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 14:50:27'),
(2635, 'INSERT', 'usuarios', 125, '125', '2024-10-23 15:09:33'),
(2636, 'INSERT', 'usuarios', 125, '125', '2024-10-23 15:09:33'),
(2637, 'INSERT', 'usuarios', 125, 'sistema', '2024-10-23 15:09:33'),
(2638, 'INSERT', 'usuarios', 126, '126', '2024-10-23 16:41:47'),
(2639, 'INSERT', 'usuarios', 126, '126', '2024-10-23 16:41:47'),
(2640, 'INSERT', 'usuarios', 126, 'sistema', '2024-10-23 16:41:47'),
(2641, 'UPDATE', 'usuarios', 123, '123', '2024-10-23 16:44:54'),
(2642, 'INSERT', 'usuarios', 127, '127', '2024-10-23 16:49:43'),
(2643, 'INSERT', 'usuarios', 127, '127', '2024-10-23 16:49:43'),
(2644, 'INSERT', 'usuarios', 127, 'sistema', '2024-10-23 16:49:43'),
(2645, 'INSERT', 'familias', 83, 'sistema', '2024-10-23 16:49:43'),
(2646, 'INSERT', 'grupos', 48, 'Sistema', '2024-10-23 16:49:44'),
(2647, 'UPDATE', 'usuarios', 127, '127', '2024-10-23 16:49:44'),
(2648, 'UPDATE', 'usuarios', 127, '127', '2024-10-23 19:08:50'),
(2649, 'UPDATE', 'usuarios', 127, '127', '2024-10-23 19:12:19'),
(2650, 'INSERT', 'familias', 84, 'sistema', '2024-10-23 19:37:10'),
(2651, 'INSERT', 'grupos', 49, 'Sistema', '2024-10-23 19:37:10'),
(2652, 'UPDATE', 'grupos', 49, 'Sistema', '2024-10-23 19:39:17'),
(2653, 'UPDATE', 'familias', 84, 'sistema', '2024-10-23 19:39:41'),
(2654, 'INSERT', 'familias', 85, 'sistema', '2024-10-23 19:41:42'),
(2655, 'INSERT', 'grupos', 50, 'Sistema', '2024-10-23 19:41:42'),
(2656, 'INSERT', 'usuarios', 128, '128', '2024-10-23 19:50:56'),
(2657, 'INSERT', 'usuarios', 128, '128', '2024-10-23 19:50:56'),
(2658, 'INSERT', 'usuarios', 128, 'sistema', '2024-10-23 19:50:56'),
(2659, 'INSERT', 'familias', 86, 'sistema', '2024-10-23 19:50:56'),
(2660, 'INSERT', 'grupos', 51, 'Sistema', '2024-10-23 19:50:56'),
(2661, 'UPDATE', 'usuarios', 128, '128', '2024-10-23 19:50:56'),
(2662, 'UPDATE', 'usuarios', 128, '128', '2024-10-23 19:52:18'),
(2663, 'UPDATE', 'usuarios', 128, '128', '2024-10-23 19:53:32'),
(2664, 'INSERT', 'familias', 87, 'sistema', '2024-10-23 19:54:39'),
(2665, 'INSERT', 'grupos', 52, 'Sistema', '2024-10-23 19:54:39'),
(2666, 'INSERT', 'usuarios', 129, '129', '2024-10-24 14:10:58'),
(2667, 'INSERT', 'usuarios', 129, '129', '2024-10-24 14:10:58'),
(2668, 'INSERT', 'usuarios', 129, 'sistema', '2024-10-24 14:10:58'),
(2669, 'UPDATE', 'usuarios', 129, '129', '2024-10-24 14:10:58'),
(2670, 'UPDATE', 'usuarios', 129, '129', '2024-10-24 14:12:23'),
(2671, 'UPDATE', 'usuarios', 129, '129', '2024-10-24 14:12:56'),
(2672, 'INSERT', 'usuarios', 130, '130', '2024-10-24 14:27:49'),
(2673, 'INSERT', 'usuarios', 130, '130', '2024-10-24 14:27:49'),
(2674, 'INSERT', 'usuarios', 130, 'sistema', '2024-10-24 14:27:49'),
(2675, 'INSERT', 'familias', 88, 'sistema', '2024-10-24 14:27:49'),
(2676, 'INSERT', 'grupos', 53, 'Sistema', '2024-10-24 14:27:49'),
(2677, 'UPDATE', 'usuarios', 130, '130', '2024-10-24 14:27:49'),
(2678, 'INSERT', 'usuarios', 131, '131', '2024-10-24 14:51:14'),
(2679, 'INSERT', 'usuarios', 131, '131', '2024-10-24 14:51:14'),
(2680, 'INSERT', 'usuarios', 131, 'sistema', '2024-10-24 14:51:14'),
(2681, 'INSERT', 'familias', 89, 'sistema', '2024-10-24 14:51:14'),
(2682, 'INSERT', 'grupos', 54, 'Sistema', '2024-10-24 14:51:14'),
(2683, 'UPDATE', 'usuarios', 131, '131', '2024-10-24 14:51:14'),
(2684, 'INSERT', 'usuarios', 132, '132', '2024-10-24 15:03:04'),
(2685, 'INSERT', 'usuarios', 132, '132', '2024-10-24 15:03:04'),
(2686, 'INSERT', 'usuarios', 132, 'sistema', '2024-10-24 15:03:04'),
(2687, 'UPDATE', 'usuarios', 132, '132', '2024-10-24 15:03:04'),
(2688, 'INSERT', 'familias', 90, 'sistema', '2024-10-24 15:03:04'),
(2689, 'INSERT', 'grupos', 55, 'Sistema', '2024-10-24 15:03:04'),
(2690, 'UPDATE', 'usuarios', 132, '132', '2024-10-24 15:03:04'),
(2691, 'INSERT', 'familias', 91, 'sistema', '2024-10-24 15:06:04'),
(2692, 'INSERT', 'grupos', 56, 'Sistema', '2024-10-24 15:06:04'),
(2693, 'INSERT', 'usuarios', 133, '133', '2024-10-24 17:15:56'),
(2694, 'INSERT', 'usuarios', 133, '133', '2024-10-24 17:15:56'),
(2695, 'INSERT', 'usuarios', 133, 'sistema', '2024-10-24 17:15:56'),
(2696, 'UPDATE', 'usuarios', 133, '133', '2024-10-24 17:15:56'),
(2697, 'INSERT', 'familias', 92, 'sistema', '2024-10-24 17:15:56'),
(2698, 'INSERT', 'grupos', 57, 'Sistema', '2024-10-24 17:15:56'),
(2699, 'UPDATE', 'usuarios', 133, '133', '2024-10-24 17:15:56'),
(2700, 'INSERT', 'familias', 93, 'sistema', '2024-10-24 17:22:07'),
(2701, 'INSERT', 'grupos', 58, 'Sistema', '2024-10-24 17:22:07'),
(2702, 'INSERT', 'usuarios', 134, '134', '2024-10-24 17:41:58'),
(2703, 'INSERT', 'usuarios', 134, '134', '2024-10-24 17:41:58'),
(2704, 'INSERT', 'usuarios', 134, 'sistema', '2024-10-24 17:41:58'),
(2705, 'UPDATE', 'usuarios', 134, '134', '2024-10-24 17:41:58'),
(2706, 'INSERT', 'familias', 94, 'sistema', '2024-10-24 17:41:58'),
(2707, 'UPDATE', 'grupos', 58, 'Sistema', '2024-10-24 17:42:45'),
(2708, 'UPDATE', 'familias', 93, 'sistema', '2024-10-24 17:43:32'),
(2709, 'UPDATE', 'familias', 94, 'sistema', '2024-10-24 17:43:42'),
(2710, 'INSERT', 'usuarios', 135, '135', '2024-10-24 17:48:53'),
(2711, 'INSERT', 'usuarios', 135, '135', '2024-10-24 17:48:53'),
(2712, 'INSERT', 'usuarios', 135, 'sistema', '2024-10-24 17:48:53'),
(2713, 'UPDATE', 'usuarios', 135, '135', '2024-10-24 17:48:53'),
(2714, 'INSERT', 'familias', 95, 'sistema', '2024-10-24 17:48:53'),
(2715, 'INSERT', 'grupos', 59, 'Sistema', '2024-10-24 17:48:53'),
(2716, 'UPDATE', 'usuarios', 135, '135', '2024-10-24 17:48:53'),
(2717, 'INSERT', 'familias', 96, 'sistema', '2024-10-24 18:12:49'),
(2718, 'INSERT', 'grupos', 60, 'Sistema', '2024-10-24 18:12:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos`
--

DROP TABLE IF EXISTS `auditoria_accesos`;
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
(2, 2, 'login', '2024-10-07 13:34:32'),
(3, 2, 'login', '2024-10-08 17:01:30'),
(4, 2, 'logout', '2024-10-08 17:21:03'),
(5, 2, 'login', '2024-10-08 17:21:08'),
(6, 2, 'login', '2024-10-08 17:48:00'),
(7, 2, 'logout', '2024-10-08 17:48:10'),
(10, 6, 'login', '2024-10-08 17:48:25'),
(11, 6, 'logout', '2024-10-08 17:48:28'),
(12, 1, 'login', '2024-10-08 17:48:35'),
(13, 1, 'logout', '2024-10-08 17:48:53'),
(14, 2, 'login', '2024-10-08 17:48:58'),
(15, 2, 'logout', '2024-10-08 17:58:48'),
(16, 2, 'login', '2024-10-08 17:58:52'),
(17, 2, 'logout', '2024-10-08 18:06:37'),
(18, 2, 'login', '2024-10-08 18:06:41'),
(19, 2, 'logout', '2024-10-08 18:09:06'),
(20, 2, 'login', '2024-10-08 18:09:15'),
(21, 2, 'logout', '2024-10-08 18:09:39'),
(22, 2, 'login', '2024-10-08 18:10:55'),
(23, 2, 'logout', '2024-10-08 19:32:55'),
(24, 1, 'login', '2024-10-08 19:33:01'),
(25, 2, 'login', '2024-10-08 20:21:45'),
(26, 2, 'logout', '2024-10-08 20:22:21'),
(27, 2, 'login', '2024-10-08 20:22:30'),
(28, 2, 'logout', '2024-10-08 20:52:25'),
(29, 1, 'login', '2024-10-08 20:52:32'),
(30, 1, 'logout', '2024-10-08 20:55:02'),
(31, 2, 'login', '2024-10-08 20:55:08'),
(32, 2, 'logout', '2024-10-08 21:40:22'),
(33, 2, 'login', '2024-10-08 21:40:27'),
(34, 2, 'logout', '2024-10-08 21:54:40'),
(35, 1, 'login', '2024-10-08 22:22:02'),
(36, 1, 'logout', '2024-10-08 23:41:19'),
(37, 1, 'login', '2024-10-08 23:41:26'),
(38, 1, 'logout', '2024-10-08 23:44:37'),
(39, 1, 'login', '2024-10-08 23:44:43'),
(40, 1, 'logout', '2024-10-08 23:48:51'),
(41, 1, 'login', '2024-10-08 23:48:56'),
(42, 1, 'logout', '2024-10-08 23:54:04'),
(43, 2, 'login', '2024-10-08 23:54:10'),
(44, 2, 'logout', '2024-10-08 23:55:13'),
(45, 1, 'login', '2024-10-10 14:05:42'),
(46, 1, 'logout', '2024-10-10 14:49:07'),
(47, 1, 'login', '2024-10-10 14:49:13'),
(48, 1, 'logout', '2024-10-10 15:04:48'),
(49, 1, 'login', '2024-10-10 15:04:53'),
(50, 1, 'logout', '2024-10-10 17:12:07'),
(51, 1, 'login', '2024-10-10 17:12:13'),
(52, 1, 'logout', '2024-10-10 17:27:59'),
(53, 1, 'login', '2024-10-10 17:28:05'),
(54, 1, 'logout', '2024-10-10 17:37:25'),
(55, 1, 'login', '2024-10-10 17:37:32'),
(56, 1, 'logout', '2024-10-10 18:12:34'),
(57, 1, 'login', '2024-10-10 18:12:40'),
(58, 1, 'login', '2024-10-11 06:55:13'),
(59, 1, 'logout', '2024-10-11 07:24:50'),
(60, 1, 'login', '2024-10-11 07:24:57'),
(61, 1, 'logout', '2024-10-11 07:59:49'),
(62, 2, 'login', '2024-10-11 07:59:55'),
(63, 2, 'logout', '2024-10-11 08:00:08'),
(64, 1, 'login', '2024-10-11 08:00:13'),
(65, 1, 'logout', '2024-10-11 08:38:34'),
(66, 1, 'login', '2024-10-11 08:38:40'),
(67, 1, 'logout', '2024-10-11 11:01:46'),
(68, 1, 'login', '2024-10-11 11:01:52'),
(69, 1, 'logout', '2024-10-11 11:23:43'),
(70, 1, 'login', '2024-10-11 11:23:50'),
(71, 1, 'logout', '2024-10-11 11:28:36'),
(72, 1, 'login', '2024-10-11 11:28:42'),
(73, 1, 'logout', '2024-10-11 11:33:47'),
(74, 1, 'login', '2024-10-11 11:33:56'),
(75, 1, 'logout', '2024-10-11 12:15:48'),
(76, 1, 'login', '2024-10-11 12:15:56'),
(77, 1, 'logout', '2024-10-11 12:29:19'),
(78, 1, 'login', '2024-10-11 12:29:25'),
(79, 1, 'logout', '2024-10-11 12:30:17'),
(80, 1, 'login', '2024-10-11 12:31:47'),
(81, 1, 'logout', '2024-10-11 13:03:23'),
(82, 1, 'login', '2024-10-11 13:03:31'),
(83, 1, 'logout', '2024-10-11 13:23:22'),
(84, 1, 'login', '2024-10-11 13:23:33'),
(85, 1, 'logout', '2024-10-11 13:26:38'),
(86, 1, 'login', '2024-10-11 13:26:47'),
(87, 1, 'logout', '2024-10-11 13:42:52'),
(88, 1, 'login', '2024-10-11 13:42:59'),
(89, 1, 'login', '2024-10-11 15:05:22'),
(90, 1, 'logout', '2024-10-11 15:06:04'),
(91, 1, 'login', '2024-10-11 15:06:22'),
(92, 1, 'logout', '2024-10-11 17:54:32'),
(93, 1, 'login', '2024-10-11 17:56:39'),
(94, 1, 'logout', '2024-10-11 17:59:43'),
(95, 1, 'login', '2024-10-11 17:59:54'),
(96, 1, 'logout', '2024-10-11 18:04:53'),
(97, 1, 'login', '2024-10-11 18:05:01'),
(98, 1, 'logout', '2024-10-11 18:08:16'),
(99, 1, 'login', '2024-10-11 18:08:24'),
(100, 1, 'logout', '2024-10-11 18:16:07'),
(101, 1, 'login', '2024-10-11 18:16:13'),
(102, 1, 'login', '2024-10-11 18:46:16'),
(103, 1, 'login', '2024-10-12 11:10:12'),
(104, 1, 'logout', '2024-10-12 11:23:57'),
(105, 1, 'login', '2024-10-12 11:24:04'),
(106, 1, 'logout', '2024-10-12 11:52:57'),
(107, 1, 'login', '2024-10-12 11:53:04'),
(108, 1, 'logout', '2024-10-12 11:57:39'),
(109, 1, 'login', '2024-10-12 11:57:59'),
(110, 1, 'logout', '2024-10-12 12:17:26'),
(111, 1, 'login', '2024-10-12 12:17:36'),
(112, 1, 'logout', '2024-10-12 12:51:06'),
(113, 1, 'login', '2024-10-12 12:51:13'),
(114, 1, 'logout', '2024-10-12 14:21:15'),
(115, 1, 'login', '2024-10-12 14:21:23'),
(116, 1, 'logout', '2024-10-12 14:36:05'),
(117, 1, 'login', '2024-10-12 14:36:19'),
(118, 1, 'logout', '2024-10-12 14:45:40'),
(119, 1, 'login', '2024-10-12 14:45:59'),
(120, 1, 'logout', '2024-10-12 14:47:46'),
(121, 1, 'login', '2024-10-12 14:48:02'),
(122, 1, 'logout', '2024-10-12 14:50:23'),
(123, 1, 'login', '2024-10-12 14:50:31'),
(124, 1, 'logout', '2024-10-12 14:57:32'),
(125, 1, 'login', '2024-10-12 14:57:44'),
(126, 1, 'logout', '2024-10-12 16:09:52'),
(127, 1, 'login', '2024-10-12 16:10:07'),
(128, 1, 'logout', '2024-10-12 16:15:26'),
(129, 1, 'login', '2024-10-12 16:15:38'),
(130, 1, 'logout', '2024-10-12 16:18:52'),
(131, 1, 'login', '2024-10-12 16:18:59'),
(132, 1, 'logout', '2024-10-12 16:40:59'),
(133, 1, 'login', '2024-10-12 16:41:04'),
(134, 1, 'logout', '2024-10-12 16:53:12'),
(135, 2, 'login', '2024-10-12 17:10:21'),
(136, 2, 'logout', '2024-10-12 17:13:02'),
(137, 6, 'login', '2024-10-12 17:14:15'),
(138, 6, 'logout', '2024-10-12 17:14:37'),
(139, 2, 'login', '2024-10-12 17:14:56'),
(140, 2, 'logout', '2024-10-12 17:14:58'),
(141, 1, 'login', '2024-10-12 17:34:50'),
(142, 1, 'logout', '2024-10-12 17:34:53'),
(143, 18, 'login', '2024-10-12 17:43:19'),
(144, 18, 'login', '2024-10-12 18:07:41'),
(145, 18, 'logout', '2024-10-12 18:08:20'),
(146, 19, 'login', '2024-10-12 18:29:19'),
(147, 2, 'login', '2024-10-12 19:09:21'),
(148, 2, 'logout', '2024-10-12 19:09:27'),
(149, 9, 'login', '2024-10-12 19:10:10'),
(150, 9, 'logout', '2024-10-12 19:11:34'),
(151, 1, 'login', '2024-10-12 19:33:59'),
(152, 1, 'logout', '2024-10-12 19:34:02'),
(153, 1, 'login', '2024-10-13 07:59:33'),
(154, 1, 'logout', '2024-10-13 08:08:13'),
(155, 1, 'login', '2024-10-13 08:09:59'),
(156, 1, 'login', '2024-10-13 08:28:50'),
(157, 1, 'logout', '2024-10-13 08:29:31'),
(158, 1, 'login', '2024-10-13 08:30:26'),
(159, 1, 'logout', '2024-10-13 08:30:31'),
(160, 1, 'login', '2024-10-13 08:31:21'),
(161, 1, 'logout', '2024-10-13 08:32:19'),
(162, 20, 'login', '2024-10-13 08:32:28'),
(163, 20, 'logout', '2024-10-13 08:33:37'),
(164, 21, 'login', '2024-10-13 08:35:13'),
(165, 21, 'logout', '2024-10-13 08:35:24'),
(166, 1, 'login', '2024-10-13 08:38:28'),
(167, 1, 'logout', '2024-10-13 08:59:46'),
(168, 1, 'login', '2024-10-13 13:59:38'),
(169, 1, 'logout', '2024-10-13 13:59:46'),
(170, 1, 'login', '2024-10-13 14:01:47'),
(171, 1, 'logout', '2024-10-13 14:03:19'),
(172, 1, 'login', '2024-10-13 14:06:10'),
(173, 1, 'logout', '2024-10-13 14:06:16'),
(174, 2, 'login', '2024-10-13 14:06:21'),
(175, 2, 'logout', '2024-10-13 14:06:30'),
(176, 1, 'login', '2024-10-13 14:06:35'),
(177, 1, 'logout', '2024-10-13 14:13:33'),
(178, 2, 'login', '2024-10-13 14:36:09'),
(179, 2, 'logout', '2024-10-13 14:36:12'),
(184, 1, 'login', '2024-10-13 14:39:53'),
(185, 1, 'logout', '2024-10-13 14:41:01'),
(191, 1, 'login', '2024-10-13 16:00:01'),
(192, 1, 'login', '2024-10-13 16:02:21'),
(193, 1, 'logout', '2024-10-13 17:03:43'),
(197, 26, 'login', '2024-10-13 17:41:27'),
(198, 26, 'login', '2024-10-13 18:16:16'),
(199, 26, 'logout', '2024-10-13 18:17:18'),
(200, 1, 'login', '2024-10-13 18:17:29'),
(201, 27, 'login', '2024-10-13 18:29:18'),
(202, 27, 'logout', '2024-10-13 18:32:05'),
(203, 27, 'login', '2024-10-13 18:32:20'),
(204, 27, 'logout', '2024-10-13 18:48:08'),
(205, 27, 'login', '2024-10-13 18:48:21'),
(206, 27, 'logout', '2024-10-13 18:56:53'),
(207, 27, 'login', '2024-10-13 18:57:15'),
(208, 27, 'logout', '2024-10-13 19:03:00'),
(209, 27, 'login', '2024-10-13 19:06:11'),
(210, 27, 'logout', '2024-10-13 19:12:53'),
(211, 27, 'login', '2024-10-13 19:12:59'),
(212, 27, 'logout', '2024-10-13 19:25:56'),
(213, 27, 'login', '2024-10-13 19:26:12'),
(214, 27, 'logout', '2024-10-13 19:39:40'),
(215, 27, 'login', '2024-10-13 19:39:48'),
(216, 27, 'logout', '2024-10-13 19:49:35'),
(217, 27, 'login', '2024-10-13 19:49:41'),
(218, 27, 'logout', '2024-10-13 20:07:11'),
(219, 27, 'login', '2024-10-13 20:09:24'),
(220, 27, 'logout', '2024-10-13 20:18:27'),
(221, 27, 'login', '2024-10-13 20:18:34'),
(222, 27, 'login', '2024-10-13 20:33:07'),
(223, 27, 'logout', '2024-10-13 20:38:50'),
(224, 27, 'login', '2024-10-13 20:40:13'),
(225, 27, 'logout', '2024-10-13 20:48:58'),
(226, 27, 'login', '2024-10-13 20:49:03'),
(227, 2, 'login', '2024-10-13 22:29:47'),
(228, 2, 'logout', '2024-10-13 22:30:20'),
(229, 27, 'login', '2024-10-14 16:25:52'),
(230, 27, 'logout', '2024-10-14 16:26:40'),
(231, 27, 'login', '2024-10-14 16:33:06'),
(232, 27, 'logout', '2024-10-14 16:33:14'),
(233, 27, 'login', '2024-10-14 16:33:36'),
(234, 27, 'logout', '2024-10-14 16:33:59'),
(235, 27, 'login', '2024-10-14 16:34:32'),
(236, 27, 'logout', '2024-10-14 16:34:36'),
(237, 27, 'login', '2024-10-14 17:11:01'),
(238, 27, 'logout', '2024-10-14 17:11:06'),
(239, 27, 'login', '2024-10-14 17:15:01'),
(240, 27, 'logout', '2024-10-14 17:18:08'),
(242, 27, 'login', '2024-10-14 17:23:21'),
(243, 27, 'logout', '2024-10-14 17:23:25'),
(244, 31, 'login', '2024-10-14 18:15:37'),
(245, 31, 'logout', '2024-10-14 18:21:21'),
(246, 27, 'login', '2024-10-14 18:21:29'),
(247, 31, 'login', '2024-10-15 14:09:25'),
(248, 31, 'logout', '2024-10-15 14:11:53'),
(249, 27, 'login', '2024-10-15 14:12:01'),
(250, 27, 'logout', '2024-10-15 14:15:30'),
(251, 1, 'login', '2024-10-15 14:15:36'),
(252, 1, 'logout', '2024-10-15 15:05:55'),
(253, 2, 'login', '2024-10-15 15:06:03'),
(254, 2, 'login', '2024-10-16 07:41:07'),
(255, 2, 'logout', '2024-10-16 07:41:28'),
(256, 1, 'login', '2024-10-16 07:52:32'),
(257, 1, 'logout', '2024-10-16 07:58:09'),
(258, 1, 'login', '2024-10-16 07:58:19'),
(259, 1, 'logout', '2024-10-16 07:58:24'),
(260, 1, 'login', '2024-10-16 08:21:45'),
(261, 1, 'logout', '2024-10-16 08:25:03'),
(262, 2, 'login', '2024-10-16 08:25:11'),
(263, 2, 'logout', '2024-10-16 08:25:43'),
(264, 31, 'login', '2024-10-16 08:25:52'),
(265, 31, 'logout', '2024-10-16 11:44:50'),
(266, 1, 'login', '2024-10-16 11:44:58'),
(267, 1, 'logout', '2024-10-16 11:46:07'),
(268, 27, 'login', '2024-10-16 11:46:14'),
(269, 1, 'login', '2024-10-16 13:24:01'),
(270, 1, 'logout', '2024-10-16 13:28:03'),
(271, 1, 'login', '2024-10-16 13:28:22'),
(272, 1, 'logout', '2024-10-16 13:28:41'),
(273, 1, 'login', '2024-10-16 13:28:59'),
(274, 1, 'login', '2024-10-16 14:36:17'),
(275, 1, 'logout', '2024-10-16 14:39:05'),
(276, 1, 'login', '2024-10-16 15:17:44'),
(277, 1, 'logout', '2024-10-16 16:12:38'),
(278, 1, 'login', '2024-10-16 16:12:48'),
(279, 1, 'logout', '2024-10-16 16:15:47'),
(280, 27, 'login', '2024-10-16 16:16:10'),
(281, 27, 'login', '2024-10-16 16:55:05'),
(282, 31, 'login', '2024-10-16 17:05:28'),
(283, 31, 'logout', '2024-10-16 17:05:56'),
(284, 27, 'login', '2024-10-16 17:06:01'),
(285, 2, 'login', '2024-10-16 17:06:15'),
(286, 2, 'logout', '2024-10-16 19:40:31'),
(287, 27, 'login', '2024-10-16 19:40:38'),
(288, 27, 'logout', '2024-10-16 19:41:39'),
(289, 31, 'login', '2024-10-16 19:45:47'),
(290, 31, 'logout', '2024-10-16 19:46:03'),
(291, 27, 'login', '2024-10-16 19:46:11'),
(292, 27, 'logout', '2024-10-16 19:46:17'),
(293, 1, 'login', '2024-10-16 19:46:23'),
(294, 1, 'logout', '2024-10-16 19:47:23'),
(295, 27, 'login', '2024-10-17 15:54:12'),
(296, 27, 'logout', '2024-10-17 18:21:06'),
(297, 27, 'login', '2024-10-17 18:21:11'),
(298, 27, 'logout', '2024-10-17 18:21:32'),
(299, 1, 'login', '2024-10-17 18:21:37'),
(300, 1, 'logout', '2024-10-17 18:22:02'),
(301, 31, 'login', '2024-10-17 18:22:07'),
(302, 31, 'logout', '2024-10-17 18:42:35'),
(303, 27, 'login', '2024-10-17 18:42:40'),
(304, 27, 'logout', '2024-10-17 18:51:00'),
(305, 1, 'login', '2024-10-17 18:51:04'),
(306, 1, 'logout', '2024-10-17 18:51:30'),
(307, 27, 'login', '2024-10-17 18:51:38'),
(308, 27, 'logout', '2024-10-17 18:51:53'),
(309, 31, 'login', '2024-10-17 18:52:03'),
(310, 31, 'logout', '2024-10-17 18:52:11'),
(312, 6, 'login', '2024-10-17 18:52:24'),
(313, 1, 'login', '2024-10-18 11:01:52'),
(314, 1, 'logout', '2024-10-18 11:03:28'),
(315, 1, 'login', '2024-10-18 11:03:36'),
(316, 1, 'logout', '2024-10-18 11:22:07'),
(317, 1, 'login', '2024-10-18 11:22:13'),
(318, 1, 'logout', '2024-10-18 11:24:06'),
(319, 1, 'login', '2024-10-18 11:24:16'),
(320, 1, 'logout', '2024-10-18 11:24:46'),
(321, 1, 'login', '2024-10-18 11:24:53'),
(322, 1, 'logout', '2024-10-18 11:55:00'),
(323, 1, 'login', '2024-10-18 11:56:03'),
(324, 1, 'logout', '2024-10-18 12:08:49'),
(325, 1, 'login', '2024-10-18 12:09:21'),
(326, 1, 'logout', '2024-10-18 12:29:24'),
(327, 1, 'login', '2024-10-18 12:29:31'),
(328, 1, 'logout', '2024-10-18 12:30:04'),
(329, 1, 'login', '2024-10-18 12:30:30'),
(330, 1, 'logout', '2024-10-18 14:07:26'),
(331, 1, 'login', '2024-10-18 14:07:44'),
(332, 1, 'logout', '2024-10-18 14:15:17'),
(333, 1, 'login', '2024-10-18 14:15:25'),
(334, 1, 'logout', '2024-10-18 14:22:46'),
(335, 1, 'login', '2024-10-18 14:22:57'),
(336, 1, 'logout', '2024-10-18 14:28:14'),
(337, 1, 'login', '2024-10-18 14:28:34'),
(338, 1, 'logout', '2024-10-18 14:30:10'),
(339, 1, 'login', '2024-10-18 14:30:24'),
(340, 1, 'logout', '2024-10-18 14:42:20'),
(341, 1, 'login', '2024-10-18 14:42:30'),
(342, 1, 'logout', '2024-10-18 14:43:31'),
(343, 1, 'login', '2024-10-18 14:43:42'),
(344, 1, 'logout', '2024-10-18 14:47:29'),
(345, 1, 'login', '2024-10-18 14:47:36'),
(346, 1, 'logout', '2024-10-18 14:54:40'),
(347, 1, 'login', '2024-10-18 14:55:08'),
(348, 1, 'logout', '2024-10-18 15:07:48'),
(349, 1, 'login', '2024-10-18 15:08:04'),
(350, 1, 'logout', '2024-10-18 15:27:50'),
(351, 27, 'login', '2024-10-18 15:27:59'),
(352, 27, 'logout', '2024-10-18 15:28:29'),
(353, 1, 'login', '2024-10-18 15:28:36'),
(354, 1, 'logout', '2024-10-18 16:34:28'),
(355, 1, 'login', '2024-10-18 16:34:34'),
(356, 1, 'login', '2024-10-19 09:47:48'),
(357, 1, 'logout', '2024-10-19 11:02:21'),
(358, 1, 'login', '2024-10-19 11:02:56'),
(359, 1, 'logout', '2024-10-19 12:34:58'),
(360, 1, 'login', '2024-10-19 12:35:19'),
(361, 1, 'logout', '2024-10-19 14:12:51'),
(362, 27, 'login', '2024-10-19 14:13:02'),
(363, 27, 'logout', '2024-10-19 14:13:57'),
(364, 1, 'login', '2024-10-19 14:48:30'),
(365, 1, 'logout', '2024-10-19 15:32:10'),
(366, 2, 'login', '2024-10-19 15:32:16'),
(367, 2, 'logout', '2024-10-19 15:33:19'),
(368, 27, 'login', '2024-10-19 15:33:27'),
(369, 27, 'logout', '2024-10-19 15:33:41'),
(370, 1, 'login', '2024-10-19 15:36:57'),
(371, 1, 'logout', '2024-10-19 15:58:29'),
(372, 1, 'login', '2024-10-19 15:58:36'),
(373, 1, 'logout', '2024-10-19 16:07:17'),
(374, 1, 'login', '2024-10-19 16:07:26'),
(375, 1, 'login', '2024-10-20 08:13:00'),
(376, 1, 'logout', '2024-10-20 09:18:33'),
(377, 1, 'login', '2024-10-20 09:19:02'),
(378, 1, 'logout', '2024-10-20 09:19:27'),
(379, 1, 'login', '2024-10-20 09:19:45'),
(380, 1, 'logout', '2024-10-20 09:21:56'),
(381, 1, 'login', '2024-10-20 09:22:04'),
(382, 1, 'logout', '2024-10-20 10:12:12'),
(383, 1, 'login', '2024-10-20 10:14:24'),
(384, 1, 'logout', '2024-10-20 10:15:28'),
(385, 1, 'login', '2024-10-20 10:20:27'),
(386, 1, 'logout', '2024-10-20 10:44:40'),
(387, 1, 'login', '2024-10-20 10:47:28'),
(388, 1, 'logout', '2024-10-20 10:53:26'),
(389, 1, 'login', '2024-10-20 10:56:48'),
(390, 1, 'logout', '2024-10-20 10:57:51'),
(391, 1, 'login', '2024-10-20 11:11:15'),
(392, 1, 'logout', '2024-10-20 11:15:35'),
(393, 1, 'login', '2024-10-20 11:18:06'),
(394, 1, 'logout', '2024-10-20 11:20:15'),
(395, 1, 'login', '2024-10-20 11:24:39'),
(396, 1, 'logout', '2024-10-20 11:26:19'),
(397, 1, 'login', '2024-10-20 11:35:34'),
(398, 1, 'logout', '2024-10-20 11:37:33'),
(399, 1, 'login', '2024-10-20 11:44:20'),
(400, 1, 'logout', '2024-10-20 11:48:44'),
(401, 1, 'login', '2024-10-20 11:50:14'),
(402, 1, 'login', '2024-10-20 11:52:00'),
(403, 1, 'logout', '2024-10-20 11:53:37'),
(404, 1, 'login', '2024-10-20 11:54:56'),
(405, 1, 'logout', '2024-10-20 12:01:06'),
(406, 1, 'login', '2024-10-20 12:02:48'),
(407, 1, 'logout', '2024-10-20 13:28:19'),
(408, 1, 'login', '2024-10-20 13:30:06'),
(409, 1, 'logout', '2024-10-20 13:36:40'),
(410, 1, 'login', '2024-10-20 13:38:46'),
(411, 1, 'logout', '2024-10-20 13:44:09'),
(412, 1, 'login', '2024-10-20 13:46:02'),
(413, 1, 'logout', '2024-10-20 13:51:29'),
(414, 1, 'login', '2024-10-20 13:55:23'),
(415, 1, 'logout', '2024-10-20 14:02:09'),
(416, 1, 'login', '2024-10-20 14:02:13'),
(417, 1, 'logout', '2024-10-20 14:07:08'),
(418, 1, 'login', '2024-10-20 14:52:15'),
(419, 1, 'login', '2024-10-20 16:56:54'),
(420, 1, 'logout', '2024-10-20 17:28:18'),
(421, 1, 'login', '2024-10-20 17:28:24'),
(422, 1, 'logout', '2024-10-20 17:48:06'),
(423, 1, 'login', '2024-10-20 17:48:28'),
(424, 1, 'logout', '2024-10-20 17:59:57'),
(425, 1, 'login', '2024-10-20 18:00:26'),
(426, 1, 'logout', '2024-10-20 18:42:46'),
(427, 1, 'login', '2024-10-20 18:43:59'),
(428, 1, 'logout', '2024-10-20 19:21:10'),
(429, 1, 'login', '2024-10-20 19:21:39'),
(430, 1, 'logout', '2024-10-20 19:27:45'),
(431, 1, 'login', '2024-10-20 19:28:53'),
(432, 1, 'logout', '2024-10-20 20:33:19'),
(433, 1, 'login', '2024-10-20 20:33:49'),
(434, 1, 'login', '2024-10-20 21:20:57'),
(435, 1, 'login', '2024-10-21 07:48:27'),
(436, 1, 'logout', '2024-10-21 09:01:12'),
(437, 1, 'login', '2024-10-21 09:01:42'),
(438, 1, 'logout', '2024-10-21 09:22:07'),
(442, 1, 'login', '2024-10-21 10:47:17'),
(443, 1, 'logout', '2024-10-21 10:48:52'),
(444, 86, 'login', '2024-10-21 15:44:39'),
(445, 86, 'logout', '2024-10-21 15:45:01'),
(446, 1, 'login', '2024-10-21 15:45:13'),
(447, 1, 'logout', '2024-10-21 17:32:54'),
(448, 1, 'login', '2024-10-21 18:08:37'),
(449, 1, 'logout', '2024-10-21 18:13:42'),
(450, 120, 'login', '2024-10-22 21:18:25'),
(451, 120, 'logout', '2024-10-22 21:19:05'),
(452, 1, 'login', '2024-10-22 21:19:11'),
(453, 1, 'logout', '2024-10-22 21:20:32'),
(454, 1, 'login', '2024-10-22 22:37:01'),
(455, 1, 'logout', '2024-10-22 22:37:03'),
(456, 1, 'login', '2024-10-22 22:43:11'),
(457, 1, 'logout', '2024-10-22 22:43:14'),
(458, 1, 'login', '2024-10-22 22:52:11'),
(459, 1, 'logout', '2024-10-22 22:52:13'),
(460, 1, 'login', '2024-10-22 23:12:24'),
(461, 1, 'logout', '2024-10-22 23:12:33'),
(462, 1, 'login', '2024-10-23 14:17:58'),
(463, 1, 'logout', '2024-10-23 14:18:36'),
(464, 1, 'login', '2024-10-23 14:20:18'),
(465, 1, 'logout', '2024-10-23 14:20:36'),
(466, 1, 'login', '2024-10-23 14:21:47'),
(467, 1, 'logout', '2024-10-23 14:24:09'),
(468, 1, 'login', '2024-10-23 14:24:24'),
(469, 1, 'logout', '2024-10-23 15:01:49'),
(470, 1, 'login', '2024-10-23 15:03:40'),
(471, 1, 'logout', '2024-10-23 15:03:47'),
(472, 127, 'login', '2024-10-23 16:54:12'),
(473, 127, 'logout', '2024-10-23 17:16:58'),
(474, 127, 'login', '2024-10-23 17:20:36'),
(475, 127, 'logout', '2024-10-23 17:36:34'),
(476, 127, 'login', '2024-10-23 17:37:50'),
(477, 127, 'logout', '2024-10-23 17:43:56'),
(478, 1, 'login', '2024-10-23 17:49:59'),
(479, 1, 'logout', '2024-10-23 18:06:47'),
(480, 127, 'login', '2024-10-23 18:06:56'),
(481, 127, 'login', '2024-10-23 18:08:33'),
(482, 127, 'logout', '2024-10-23 18:09:03'),
(483, 127, 'login', '2024-10-23 18:09:31'),
(484, 127, 'logout', '2024-10-23 19:13:11'),
(485, 127, 'login', '2024-10-23 19:13:18'),
(486, 127, 'logout', '2024-10-23 19:49:13'),
(487, 128, 'login', '2024-10-23 19:51:33'),
(488, 1, 'login', '2024-10-24 14:12:51'),
(489, 1, 'logout', '2024-10-24 14:13:13'),
(490, 129, 'login', '2024-10-24 14:13:45'),
(491, 129, 'logout', '2024-10-24 14:14:02'),
(494, 1, 'login', '2024-10-24 14:40:30'),
(495, 1, 'logout', '2024-10-24 14:41:56'),
(498, 1, 'login', '2024-10-24 14:42:29'),
(499, 1, 'logout', '2024-10-24 14:42:35'),
(500, 2, 'login', '2024-10-24 14:42:41'),
(501, 2, 'logout', '2024-10-24 14:42:43'),
(506, 1, 'login', '2024-10-24 14:47:42'),
(507, 1, 'logout', '2024-10-24 14:47:45'),
(508, 2, 'login', '2024-10-24 14:47:49'),
(509, 2, 'logout', '2024-10-24 14:47:52'),
(510, 1, 'login', '2024-10-24 14:49:41'),
(511, 1, 'logout', '2024-10-24 14:50:01'),
(513, 131, 'login', '2024-10-24 14:52:31'),
(514, 131, 'logout', '2024-10-24 15:01:47'),
(515, 132, 'login', '2024-10-24 15:04:42'),
(516, 132, 'logout', '2024-10-24 17:12:13'),
(519, 133, 'login', '2024-10-24 17:19:41'),
(520, 133, 'logout', '2024-10-24 17:39:15'),
(522, 135, 'login', '2024-10-24 18:09:54'),
(523, 135, 'logout', '2024-10-24 18:15:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos_archivo`
--

DROP TABLE IF EXISTS `auditoria_accesos_archivo`;
CREATE TABLE `auditoria_accesos_archivo` (
  `idAcceso` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_accesos_archivo`
--

INSERT INTO `auditoria_accesos_archivo` (`idAcceso`, `idUser`, `accion`, `fecha`) VALUES
(1, 32, 'DELETE', '2024-10-20 09:24:17'),
(2, 71, 'DELETE', '2024-10-20 21:34:55'),
(3, 68, 'DELETE', '2024-10-20 21:35:09'),
(4, 83, 'DELETE', '2024-10-21 15:45:25'),
(5, 84, 'DELETE', '2024-10-21 15:45:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
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
(23, 'Compra', 2, 'activo', 'gasto'),
(24, 'Ocio', 2, 'activo', 'gasto'),
(25, 'Luz', 2, 'activo', 'gasto'),
(26, 'Agua', 2, 'activo', 'gasto'),
(29, 'Salario', 2, 'activo', 'ingreso'),
(30, 'Paga', 2, 'activo', 'ingreso'),
(31, 'Otros', 2, 'activo', 'ingreso'),
(32, 'Ventas', 2, 'activo', 'ingreso'),
(35, 'Supermercado', 1, 'activo', 'gasto'),
(36, 'Ingresos inesperados', 1, 'activo', 'ingreso'),
(37, 'Ingresos esperados', 1, 'activo', 'ingreso'),
(38, 'Gastos presupuestados', 1, 'activo', 'gasto'),
(39, 'Gastos imprevistos', 1, 'activo', 'gasto'),
(41, 'Nuevitas', 26, 'activo', 'ingreso');

--
-- Disparadores `categorias`
--
DROP TRIGGER IF EXISTS `auditar_categoria_insert`;
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

DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones` (
  `idConfig` int(11) NOT NULL,
  `clave_config` varchar(100) DEFAULT NULL,
  `valor_config` text DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrasenyas_premium`
--

DROP TABLE IF EXISTS `contrasenyas_premium`;
CREATE TABLE `contrasenyas_premium` (
  `id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envio_refranes`
--

DROP TABLE IF EXISTS `envio_refranes`;
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

DROP TABLE IF EXISTS `familias`;
CREATE TABLE `familias` (
  `idFamilia` int(11) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familias`
--

INSERT INTO `familias` (`idFamilia`, `nombre_familia`, `password`, `estado`) VALUES
(1, 'Familia1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(16, 'Familia juan', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(22, 'mmmmmm', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(23, 'vvvvv', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(24, 'buena', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(30, '27Fam', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(31, 'SebasFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(32, 'PaulaFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(33, 'SilviaFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(34, 'JuanFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(35, 'LucioFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(36, 'FamiliaNueva1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(37, 'FamiliaNueva2', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(38, 'RobertoF1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(39, 'RobertoF2', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(40, 'RobertoF3', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(41, 'RobertoF4', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(43, 'LosGuayonesF1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(44, 'LosGuayonesF2', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(45, 'MolonaFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(46, 'LaMejorDelMundo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(47, 'Pruebita11', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(48, 'LosBola', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(50, 'BolincheFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(51, 'AverSi', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(53, 'GonzalezFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(55, 'PucholFamila', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(56, 'FamiliaPrueba', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(57, 'FamiliaPruebitaDefinitiva', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(58, 'Rafalito2Familia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(59, 'MuretiFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(60, 'BeitaJFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(61, '61F', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(62, 'Familia62', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(63, 'BobiotoFamilia', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(64, 'Familia64', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(65, 'Fam65', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(66, 'Familia66', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(67, 'Familia67', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(68, 'Familia68', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(69, 'Familia69', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(70, 'Familia70', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(71, 'Familia71', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(72, 'Familia72', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(73, 'Familia73', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(74, 'Familia74', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(75, 'Familia75', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(76, 'Familia76', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(77, 'Familia77', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(78, 'Familia78', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(79, 'Familia79', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(80, 'Familia80', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(81, 'Familia81', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(82, 'Familia82', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 'activo'),
(83, 'Familia83', '$2y$10$Wy9BJmP2qnV3cwMSPRKgUuIVhN4JbOmxINWQaNqkLm586fZTAZnBG', 'activo'),
(84, 'Familia84', '$2y$10$z5pjmG7evZ6jhmJsZjvG0.7PqFktDXYAjIz7r8zUKEt46SOsVv4WW', 'activo'),
(85, 'Familia85', '$2y$10$pySPh8ESxOeYGM7AYOkY.eIxB6Go9ydaK/2.4/DzHE82n/stjHOCC', 'activo'),
(86, 'Familia86', '$2y$10$7bH.CQVkIm4OlXHsaJSTh.eXt1iECG6J6qZwcn5Gh3mmSyoMWz5Ve', 'activo'),
(87, 'Familia87', '$2y$10$AVrcUmuAn.0959ZuwGpy9eCVfh3u8YQWDsrYXchWMp/uJnyhHvEi.', 'activo'),
(88, 'Familia88', '$2y$10$Ub32byrFRA1j65y3C558du6ZbI3vcZDMfCjGMkCHBPGEscZp01VaO', 'activo'),
(89, 'Familia89', '$2y$10$TasoJzM2qEyRiSd0Ak8jn.hkImdDYLx5O7eGbqA5Tp5a1uza/MwT6', 'activo'),
(90, 'Familia90', '$2y$10$/EDAOcN5YXGBdtSdmjJIAO37wlWUy6qccLqW7rTUVakaGq2ArOw0q', 'activo'),
(91, 'Familia91', '$2y$10$5zsCJLQ5Zr/3FLkMq.r.q.NScKV0ebyMyYNC3oMj1Xb34QuNFoF5a', 'activo'),
(92, 'Familia92', '$2y$10$HCU8iMH1Ot77dvT8gzQzPeP6B9EAp0cWfU06jp0qDdjoUcJVZINy.', 'activo'),
(93, 'Familia93', '$2y$10$DmBnGqJTCS6vgQ3cZ/8U5.udjhP1mBETMGVpw6D9jEGX5xKlH9hUu', 'activo'),
(94, 'Familia94', '$2y$10$ra9z2ePV6ka7OQG4jPvJ/OTntzyDAX3l4sNT5s4ZjiiDQpFPWbVYq', 'activo'),
(95, 'Familia95', '$2y$10$qF76R7ndbioILMkFAxnaXO5wHEa7rDAPuECowL0ZxYHwI4cm96F8q', 'activo'),
(96, 'Familia60', '$2y$10$g/HBUqcuBqKSyOmUqd1AJurSiLBF261hC4rsRSoDQEmNEynWTfx.S', 'activo');

--
-- Disparadores `familias`
--
DROP TRIGGER IF EXISTS `auditar_familias`;
DELIMITER $$
CREATE TRIGGER `auditar_familias` AFTER INSERT ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'familias', NEW.idFamilia, 'sistema', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_familias_delete`;
DELIMITER $$
CREATE TRIGGER `auditar_familias_delete` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha) 
  VALUES ('delete', 'familias', OLD.idFamilia, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_familias_delete_archivo`;
DELIMITER $$
CREATE TRIGGER `auditar_familias_delete_archivo` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (NULL, 'DELETE_FAMILIA_' || OLD.idFamilia, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_familias_update`;
DELIMITER $$
CREATE TRIGGER `auditar_familias_update` AFTER UPDATE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'familias', NEW.idFamilia, 'sistema', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `eliminar_gastos_huerfanos`;
DELIMITER $$
CREATE TRIGGER `eliminar_gastos_huerfanos` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  DELETE FROM gastos WHERE idFamilia = OLD.idFamilia;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `eliminar_ingresos_huerfanos`;
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

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `idGasto` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) DEFAULT NULL,
  `idFamilia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idGasto`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(18, 2, 120.50, 23, 'banco', 'Mercadona', '2024-10-07 22:00:00', 1, 1),
(19, 2, 48.25, 24, 'efectivo', 'Cena', '2024-10-07 22:00:00', 1, 1),
(20, 2, 80.23, 25, 'banco', 'Factura Luz', '2024-10-07 22:00:00', 1, 1),
(21, 2, 80.00, 26, 'banco', 'Factura Agua', '2024-10-07 22:00:00', 1, 1),
(22, 27, 150.00, 24, 'banco', 'otrossss', '2024-10-12 22:00:00', NULL, 30),
(23, 27, 8.00, 24, 'efectivo', 'Cervecitas', '2024-10-12 22:00:00', NULL, 30),
(24, 27, 80.00, 26, 'banco', 'Factura Agua', '2024-10-12 22:00:00', NULL, 30),
(25, 27, 300.00, 38, 'banco', 'Microondas', '2024-10-12 22:00:00', NULL, 30),
(26, 27, 26.00, 23, 'banco', 'farmacia', '2024-10-12 22:00:00', NULL, 30),
(27, 27, 8.00, 35, 'banco', 'Leche', '2024-10-12 22:00:00', NULL, 30),
(28, 27, 123.00, 23, 'banco', 'Mercadona', '2024-10-12 22:00:00', NULL, 30),
(29, 31, 25.23, 24, 'banco', 'Comida amigas', '2024-10-13 22:00:00', NULL, 30),
(30, 31, 4.00, 24, 'efectivo', 'bono Bus', '2024-10-13 22:00:00', NULL, 30);

--
-- Disparadores `gastos`
--
DROP TRIGGER IF EXISTS `auditar_creacion_gasto`;
DELIMITER $$
CREATE TRIGGER `auditar_creacion_gasto` AFTER INSERT ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'gastos', NEW.idGasto, NEW.idUser);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_gastos_delete`;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_delete` AFTER DELETE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'gastos', OLD.idGasto, OLD.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_gastos_update`;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_update` AFTER UPDATE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'gastos', NEW.idGasto, NEW.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `check_gasto_asignacion`;
DELIMITER $$
CREATE TRIGGER `check_gasto_asignacion` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NULL AND NEW.idGrupo IS NULL THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Un gasto debe pertenecer a una familia, a un grupo o ser personal.';
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `validar_consistencia_gasto`;
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
DROP TRIGGER IF EXISTS `verificar_consistencia_gasto`;
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
DROP TRIGGER IF EXISTS `verificar_gasto_familia_grupo`;
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

DROP TABLE IF EXISTS `grupos`;
CREATE TABLE `grupos` (
  `idGrupo` int(11) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idGrupo`, `nombre_grupo`, `password`) VALUES
(1, 'Grupo1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(9, 'Grupitoo lllll', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(11, 'B3', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(14, 'AndrésGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(15, 'PaulaGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(16, 'SilviaGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(17, 'JuanGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(18, 'LucioGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(19, 'GrupoNuevo1', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(20, 'GrupoNuevo2', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(21, 'RafitaGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(22, 'MuretiGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(23, 'BeitaJGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(24, '24Grupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(25, 'Grupo25', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(26, 'BobitoGrupo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(27, 'Gru27', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(28, 'Grupo28', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(29, 'Grupo29', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(30, 'Grupo30', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(31, 'Grupo31', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(32, 'Grupo32', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(33, 'Grupo33', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(34, 'Grupo34', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(35, 'Grupo35', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(36, 'Grupo36', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(37, 'Grupo37', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(38, 'Grupo38', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(39, 'Grupo39', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(40, 'Grupo40', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(41, 'Grupo41', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(42, 'Grupo42', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(43, 'Grupo43', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(44, 'Grupo44', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(45, 'Grupo45', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(46, 'Grupo46', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(47, 'Grupo47', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK'),
(48, 'Grupo48', '$2y$10$TTqgb8Rd/hTgx4GNaBC/Mu401sgpSjo3x1g5rXie3K4VTUW8Ux3rO'),
(49, 'Grupo49', '$2y$10$6SHDNK3KMC78tcYSMU0WLey7xnvvs/.Jd0H7rSPmvYvkxcV27imoq'),
(50, 'Grupo50', '$2y$10$91MHZ0uHMObgIWeYsWLHr.P7ZVhzaemeSnvt4lJE85SG6BYDtAplK'),
(51, 'Grupo51', '$2y$10$pSe06z31RH9k2foF1xxZIuNwkGeYocelbotw8rlQFDmncw6xsu6FK'),
(52, 'Grupo52', '$2y$10$oXaazn9XMFrfSSKFt4/8cOKZwEb0LiMvekHS9FIC.XFV2Fw.qHXzm'),
(53, 'Grupo53', '$2y$10$dtr.ECQToYguHOI6vatZdOo8MNoNxhHYjCYXr.nQ2QkAxOsbIe3/a'),
(54, 'Grupo54', '$2y$10$UiSZKlAW0B8AHOf6YHPwse14u0TTXHEwVmq0zNDImZ0KPBKu0zThG'),
(55, 'Grupo55', '$2y$10$4vLdeZtzDcnckEEYcXuiju8r0u/UsYOBcaRTjZFQ2vP.Quoko4KaG'),
(56, 'Grupo56', '$2y$10$6Z0SsYYA4dOus3nCCGzHK.2wR89Rbe69zPt44BakZNO0r1959peqS'),
(57, 'Grupo57', '$2y$10$1DWummAbGNFYb/.a3rNC1uz7xzMhG4ePWHQVqbJsVubLGvKC2Ag/i'),
(58, 'Grupo58', '$2y$10$3yv9Y9i6esv.wCM5pm.yVuOQ57leAaLnR44w/.HSP.6b3EjM9dACS'),
(59, 'Grupo59', '$2y$10$0V7ZeSg1c1wZqylZny5Cp.QucxIDay/U/qceby1OStWDeYoQsRsdK'),
(60, 'Grupo60', '$2y$10$lRmbrTcpo8AOmB6zz7kNt.JzHtCq9ve8Rf3Zv0fYv/xvDrFhL6wbe');

--
-- Disparadores `grupos`
--
DROP TRIGGER IF EXISTS `auditar_grupos_delete`;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha)
    VALUES ('DELETE', 'grupos', OLD.idGrupo, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_grupos_delete_archivo`;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete_archivo` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (NULL, 'DELETE_GRUPO_' || OLD.idGrupo, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_grupos_insert`;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_insert` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('INSERT', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_grupos_update`;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_update` AFTER UPDATE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('UPDATE', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_insert_grupos`;
DELIMITER $$
CREATE TRIGGER `before_insert_grupos` BEFORE INSERT ON `grupos` FOR EACH ROW BEGIN
    DECLARE v_grupo_exist INT;

    -- Verificar si ya existe un grupo con el mismo nombre
    SELECT COUNT(*) INTO v_grupo_exist FROM grupos WHERE nombre_grupo = NEW.nombre_grupo;
    IF v_grupo_exist > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo ya existe.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
CREATE TABLE `ingresos` (
  `idIngreso` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) DEFAULT NULL,
  `idFamilia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`idIngreso`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(17, 2, 1500.00, 29, 'banco', 'Sueldo Rafa', '2024-10-07 22:00:00', 1, 1),
(18, 2, 50.00, 30, 'efectivo', 'Cumple Rafa', '2024-10-07 22:00:00', 1, 1),
(19, 2, 15.00, 31, 'efectivo', 'Lotería', '2024-10-07 22:00:00', 1, 1),
(20, 2, 350.00, 32, 'efectivo', 'Venta móvil', '2024-10-07 22:00:00', 1, 1),
(23, 27, 15.00, 31, 'efectivo', 'Lotería', '2024-10-12 22:00:00', NULL, 30),
(24, 27, 1500.00, 29, 'banco', 'Septiembre', '2024-10-12 22:00:00', NULL, 30),
(25, 31, 1600.00, 29, 'banco', 'Agosto', '2024-10-13 22:00:00', NULL, 30),
(26, 31, 15.00, 32, 'efectivo', 'Venta libros', '2024-10-13 22:00:00', NULL, 30);

--
-- Disparadores `ingresos`
--
DROP TRIGGER IF EXISTS `auditar_creacion_ingreso`;
DELIMITER $$
CREATE TRIGGER `auditar_creacion_ingreso` AFTER INSERT ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'ingresos', NEW.idIngreso, NEW.idUser);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_ingresos_delete`;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_delete` AFTER DELETE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'ingresos', OLD.idIngreso, OLD.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_ingresos_update`;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_update` AFTER UPDATE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'ingresos', NEW.idIngreso, NEW.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `check_ingreso_asignacion`;
DELIMITER $$
CREATE TRIGGER `check_ingreso_asignacion` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NOT NULL OR NEW.idGrupo IS NOT NULL THEN
    SET @dummy = 1;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menuadmin`
--

DROP TABLE IF EXISTS `menuadmin`;
CREATE TABLE `menuadmin` (
  `idMenu` int(11) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `nombreItem` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_letter_envios`
--

DROP TABLE IF EXISTS `news_letter_envios`;
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
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `idNotificacion` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`idNotificacion`, `idUser`, `mensaje`, `fecha`, `leido`) VALUES
(1, 1, 'Se ha agregado un nuevo gasto.', '2024-10-15 18:01:23', 1),
(2, 1, 'Esta es una nueva notificación.', '2024-10-15 18:03:48', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_cache`
--

DROP TABLE IF EXISTS `permisos_cache`;
CREATE TABLE `permisos_cache` (
  `idUser` int(11) NOT NULL,
  `nombrePermiso` varchar(255) NOT NULL,
  `tipoPermiso` varchar(255) NOT NULL,
  `tiene_permiso` tinyint(4) DEFAULT 0,
  `fecha_cache` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preferencias_usuarios`
--

DROP TABLE IF EXISTS `preferencias_usuarios`;
CREATE TABLE `preferencias_usuarios` (
  `idPreferencia` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preferencias_usuarios`
--

INSERT INTO `preferencias_usuarios` (`idPreferencia`, `idUser`, `clave`, `valor`) VALUES
(1, 1, 'notificaciones', 'activado'),
(2, 1, 'dashboard_layout', 'modo_simple');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refranes`
--

DROP TABLE IF EXISTS `refranes`;
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

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombreRol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `nombreRol`) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'usuario'),
(4, 'registro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

DROP TABLE IF EXISTS `roles_permisos`;
CREATE TABLE `roles_permisos` (
  `idPermiso` int(11) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `nombrePermiso` varchar(255) DEFAULT NULL,
  `tipoPermiso` enum('leer','escribir','eliminar') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`idPermiso`, `idRol`, `nombrePermiso`, `tipoPermiso`) VALUES
(7, 1, 'gastos', 'leer'),
(8, 1, 'gastos', 'escribir'),
(9, 1, 'gastos', 'eliminar'),
(10, 2, 'gastos', 'leer'),
(11, 3, 'gastos', 'leer'),
(12, 3, 'ingresos', 'leer'),
(13, 1, 'usuarios', 'leer'),
(14, 1, 'usuarios', 'escribir'),
(15, 1, 'usuarios', 'eliminar'),
(16, 1, 'familias', 'leer'),
(17, 1, 'familias', 'escribir'),
(18, 1, 'familias', 'eliminar'),
(19, 1, 'grupos', 'leer'),
(20, 1, 'grupos', 'escribir'),
(21, 1, 'grupos', 'eliminar'),
(22, 1, 'gastos', 'leer'),
(23, 1, 'gastos', 'escribir'),
(24, 1, 'gastos', 'eliminar'),
(25, 1, 'ingresos', 'leer'),
(26, 1, 'ingresos', 'escribir'),
(27, 1, 'ingresos', 'eliminar'),
(28, 1, 'presupuestos', 'leer'),
(29, 1, 'presupuestos', 'escribir'),
(30, 1, 'presupuestos', 'eliminar'),
(31, 1, 'metas', 'leer'),
(32, 1, 'metas', 'escribir'),
(33, 1, 'metas', 'eliminar'),
(34, 2, 'usuarios', 'leer'),
(35, 2, 'usuarios', 'escribir'),
(36, 2, 'usuarios', 'eliminar'),
(37, 2, 'familias', 'leer'),
(38, 2, 'familias', 'escribir'),
(39, 2, 'familias', 'eliminar'),
(40, 2, 'grupos', 'leer'),
(41, 2, 'grupos', 'escribir'),
(42, 2, 'grupos', 'eliminar'),
(43, 2, 'gastos', 'leer'),
(44, 2, 'gastos', 'escribir'),
(45, 2, 'gastos', 'eliminar'),
(46, 2, 'ingresos', 'leer'),
(47, 2, 'ingresos', 'escribir'),
(48, 2, 'ingresos', 'eliminar'),
(49, 2, 'presupuestos', 'leer'),
(50, 2, 'presupuestos', 'escribir'),
(51, 2, 'metas', 'leer'),
(52, 2, 'metas', 'escribir'),
(53, 3, 'gastos', 'leer'),
(54, 3, 'gastos', 'escribir'),
(55, 3, 'gastos', 'eliminar'),
(56, 3, 'ingresos', 'leer'),
(57, 3, 'ingresos', 'escribir'),
(58, 3, 'ingresos', 'eliminar'),
(59, 3, 'presupuestos', 'leer'),
(60, 3, 'presupuestos', 'escribir'),
(61, 3, 'metas', 'leer'),
(62, 3, 'metas', 'escribir'),
(63, 3, 'gastos', 'leer'),
(64, 3, 'ingresos', 'leer'),
(65, 3, 'gastos', 'leer'),
(66, 3, 'gastos', 'escribir'),
(67, 3, 'gastos', 'eliminar'),
(68, 3, 'ingresos', 'leer'),
(69, 3, 'ingresos', 'escribir'),
(70, 3, 'ingresos', 'eliminar'),
(71, 3, 'presupuestos', 'leer'),
(72, 3, 'presupuestos', 'escribir'),
(73, 3, 'metas', 'leer'),
(74, 3, 'metas', 'escribir'),
(75, 4, 'usuarios', 'leer'),
(76, 4, 'usuarios', 'escribir'),
(77, 4, 'familias', 'leer'),
(78, 4, 'familias', 'escribir'),
(79, 4, 'grupos', 'leer'),
(80, 4, 'grupos', 'escribir');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacion`
--

DROP TABLE IF EXISTS `situacion`;
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

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenya` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nivel_usuario` enum('superadmin','admin','usuario','registro') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_usuario` enum('activo','inactivo') DEFAULT 'activo',
  `password_premium` varchar(255) DEFAULT NULL,
  `es_premium` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `alias`, `email`, `contrasenya`, `fecha_nacimiento`, `telefono`, `nivel_usuario`, `fecha_registro`, `estado_usuario`, `password_premium`, `es_premium`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'superadmin@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1970-01-01', '625418965', 'superadmin', '2024-10-04 10:03:48', 'activo', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', 0),
(2, 'Admin1', 'Family1', 'admin1', 'admin1@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1980-01-01', '625418965', 'admin', '2024-10-04 10:04:06', 'activo', NULL, 0),
(5, 'Pareja1', 'Family1', 'pareja1', 'pareja1@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1982-02-02', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo', NULL, 0),
(6, 'Hijo1', 'Family1', 'hijo1', 'hijo1@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2005-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo', NULL, 0),
(7, 'Hijo2', 'Family1', 'hijo2', 'hijo2@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2007-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo', NULL, 0),
(8, 'Hijo3', 'Family1', 'hijo3', 'hijo3@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2010-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo', NULL, 0),
(9, 'Amigo1', 'Grupo1', 'amigo1', 'amigo1@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1990-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo', NULL, 0),
(10, 'Amigo2', 'Grupo1', 'amigo2', 'amigo2@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1991-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo', NULL, 0),
(11, 'Amigo3', 'Grupo1', 'amigo3', 'amigo3@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1992-03-03', '625418965', 'admin', '2024-10-04 10:05:49', 'activo', NULL, 0),
(16, 'kk', 'kkkk', 'kk', 'kk@kk.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2004-04-04', '455545545', 'admin', '2024-10-10 16:31:32', 'activo', NULL, 0),
(18, 'Lucía', 'Gómez', 'Lucy', 'lucy@lucy.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1968-02-02', '123456789', 'usuario', '2024-10-12 17:42:45', 'activo', NULL, 0),
(19, 'Rafa', 'Gómez', 'RafaGomez', 'RafaGomez@RafaGomez.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1982-02-02', '336265984', 'usuario', '2024-10-12 18:28:50', 'activo', NULL, 0),
(20, 'xxxxxx', 'xxxxxx', 'xxxxxx', 'xxx@xxx.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2003-03-03', '777777777', 'admin', '2024-10-13 08:28:06', 'activo', NULL, 0),
(21, 'mmmmmm', 'mmmmmm', 'mmmmmm', 'mmmmmm@mmmmmm.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-12-18', '222555555', 'usuario', '2024-10-13 08:34:52', 'activo', NULL, 0),
(26, '26Usu', '26Usu', '26Usu', '26Usu@26Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1980-06-03', '262662266', 'usuario', '2024-10-13 17:39:57', 'activo', NULL, 0),
(27, '27Usu', '27Usu', '27Usu', '27Usu@27Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2001-01-01', '222333333', 'admin', '2024-10-13 18:28:47', 'activo', NULL, 0),
(28, '28Usu', '28Usu', '28Usu', '28Usu@28Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2004-04-04', '626222222', 'usuario', '2024-10-14 16:28:08', 'activo', NULL, 0),
(29, '29Usu', '29Usu', '29Usu', '29Usu@29Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2001-03-03', '555222222', 'usuario', '2024-10-14 16:30:53', 'activo', NULL, 0),
(30, '50Usu', '50Usu', '50Usu', '50Usu@50Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1966-12-22', '639584745', 'usuario', '2024-10-14 17:24:50', 'activo', NULL, 0),
(31, '31Usu', '31Usu', '31Usu', '31Usu@31Usu.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2008-08-08', '987454545', 'usuario', '2024-10-14 18:11:36', 'activo', NULL, 0),
(33, 'Euclides', 'Euclides', 'Euclides', 'Euclides@Euclides.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1987-03-02', '582331145', 'usuario', '2024-10-20 09:25:11', 'activo', NULL, 0),
(34, 'Sebas', 'Sebas', 'Sebas', 'Sebas@Sebas.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-03-02', '245636985', 'admin', '2024-10-20 09:27:59', 'activo', NULL, 0),
(35, 'Carlitos', 'Carlitos', 'Carlitos', 'Carlitos@Carlitos.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1975-01-01', '458623515', 'usuario', '2024-10-20 09:29:23', 'activo', NULL, 0),
(36, 'Ramón', 'Ramón', 'Ramón', 'Ramon@Ramon.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-02-03', '789512445', 'usuario', '2024-10-20 09:31:36', 'activo', NULL, 0),
(37, 'Andrés', 'Andrés', 'Andrés', 'Andres@Andres.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2003-03-03', '526323214', 'admin', '2024-10-20 09:34:04', 'activo', NULL, 0),
(38, 'Sebastián', 'Sebastián', 'Sebastián', 'Sebas@123.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2004-04-04', '754232659', 'admin', '2024-10-20 09:36:05', 'activo', NULL, 0),
(39, 'Tian', 'Tian', 'Tian', 'Tian@Tian.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-03-03', '256336699', 'admin', '2024-10-20 09:40:26', 'activo', NULL, 0),
(40, 'Goro', 'Goro', 'Goro', 'Goro@Goro.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1976-06-30', '568221144', 'usuario', '2024-10-20 09:42:58', 'activo', NULL, 0),
(41, 'Eduardito', 'Eduardito', 'Eduardito', 'Eduardito@Eduardito.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1987-03-02', '351226699', 'admin', '2024-10-20 09:56:31', 'activo', NULL, 0),
(42, 'Nolo', 'Nolo', 'Nolo', 'Nolo@Nolo.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-02-03', '321565854', 'admin', '2024-10-20 10:10:40', 'activo', NULL, 0),
(43, 'Externo', 'Externo', 'Externo', 'Externo@Externo.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-12-02', '856232124', 'usuario', '2024-10-20 10:14:01', 'activo', NULL, 0),
(44, 'Eduardo', 'Eduardo', 'Eduardo', 'Eduardo@Eduardo.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-01-01', '358652147', 'usuario', '2024-10-20 10:20:19', 'activo', NULL, 0),
(45, 'Esteban', 'Esteban', 'Esteban', 'Esteban@Esteban.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-02-02', '352669988', 'usuario', '2024-10-20 10:47:20', 'activo', NULL, 0),
(46, 'Franjo', 'Franjo', 'Franjo', 'Franjo@Franjo.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-05-01', '352487489', 'usuario', '2024-10-20 10:56:38', 'activo', NULL, 0),
(47, 'Luis', 'Luis', 'Luis', 'Luis@Luis.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-05-05', '695332211', 'usuario', '2024-10-20 11:10:56', 'activo', NULL, 0),
(48, 'JavierI', 'JavierI', 'JavierI', 'JavierI@JavierI.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-06-06', '695847574', 'usuario', '2024-10-20 11:17:56', 'activo', NULL, 0),
(49, 'Mónica', 'Mónica', 'Mónica', 'Monica@Monica.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-02-03', '352669944', 'usuario', '2024-10-20 11:24:30', 'activo', NULL, 0),
(50, 'Mery', 'Mery', 'Mery', 'Mery@Mery.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1979-09-09', '985624763', 'usuario', '2024-10-20 11:35:17', 'activo', NULL, 0),
(51, 'Luisote', 'Luisote', 'Luisote', 'Luisote@Luisote.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-03-03', '698574868', 'usuario', '2024-10-20 11:49:59', 'activo', NULL, 0),
(52, 'Estela', 'Estela', 'Estela', 'Estela@Estela.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1979-09-09', '325456987', 'usuario', '2024-10-20 11:51:53', 'activo', NULL, 0),
(53, 'Susanita', 'Susanita', 'Susanita', 'Susanita@Susanita.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1974-06-02', '357986512', 'usuario', '2024-10-20 12:02:41', 'activo', NULL, 0),
(54, 'Natalia', 'Natalia', 'Natalia', 'Natalia@Natalia.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1998-08-08', '586241458', 'usuario', '2024-10-20 13:29:53', 'activo', NULL, 0),
(55, 'Paula', 'Paula', 'Paula', 'Paula@Paula.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1997-09-26', '789562519', 'usuario', '2024-10-20 13:33:15', 'activo', NULL, 0),
(56, 'Fran', 'Fran', 'Fran', 'Fran@Fran.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1966-04-15', '245968547', 'usuario', '2024-10-20 13:38:38', 'activo', NULL, 0),
(57, 'Mamen', 'Mamen', 'Mamen', 'Mamen@Mamen.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-04-04', '586958475', 'usuario', '2024-10-20 13:45:49', 'activo', NULL, 0),
(58, 'Laura', 'Laura', 'Laura', 'Laura@Laura.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1984-01-06', '753264875', 'usuario', '2024-10-20 13:55:11', 'activo', NULL, 0),
(59, 'Silvia', 'Silvia', 'Silvia', 'Silvia@Silvia.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-03-22', '351265847', 'usuario', '2024-10-20 13:58:07', 'activo', NULL, 0),
(60, 'Manolo', 'Manolo', 'Manolo', 'Manolo@Manolo.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1995-05-05', '265323536', 'admin', '2024-10-20 14:03:29', 'activo', NULL, 0),
(61, 'Juan', 'Juan', 'Juan', 'Juan@Juan.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1992-03-03', '785246859', 'admin', '2024-10-20 14:05:27', 'activo', NULL, 0),
(62, 'Mica', 'Mica', 'Mica', 'Mica@Mica.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1971-09-01', '159698423', 'usuario', '2024-10-20 14:08:36', 'activo', NULL, 0),
(63, 'Lucio', 'Lucio', 'Lucio', 'Lucio@Lucio.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2005-05-04', '265323254', 'admin', '2024-10-20 14:55:14', 'activo', NULL, 0),
(64, 'NuevoAdmin', 'ApellidoAdmin', 'nuevoadmin', 'nuevoadmin@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1990-01-01', '123456789', 'admin', '2024-10-20 15:27:05', 'activo', NULL, 0),
(65, 'NuevoAdmin', 'ApellidoAdmin', 'nuevoadmin', 'nuevoadmin2@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1990-01-01', '123456789', 'admin', '2024-10-20 15:28:56', 'activo', NULL, 0),
(66, 'Marianico', 'Marianico', 'Marianico', 'Marianico@Marianico.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-12-22', '562336699', 'usuario', '2024-10-20 19:26:49', 'activo', NULL, 0),
(67, 'Mariana', 'Mariana', 'Mariana', 'Mariana@Mariana.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-12-13', '625474142', 'usuario', '2024-10-20 19:28:40', 'activo', NULL, 0),
(70, 'UsuarioPrueba', 'ApellidoPrueba', 'AliasPrueba', 'nuevoemail@example.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1990-01-01', '123456789', 'usuario', '2024-10-20 21:08:55', 'activo', NULL, 0),
(72, 'PruebitaFinal', 'PruebitaFinal', 'PruebitaFinal', 'PruebitaFinal@PruebitaFinal.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-02-03', '622335588', 'admin', '2024-10-20 21:25:22', 'activo', NULL, 0),
(73, 'Rafalito', 'Rafalito', 'Rafalito', 'Rafalito@Rafalito.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1991-10-10', '524440033', 'admin', '2024-10-21 07:42:36', 'activo', NULL, 0),
(74, 'Mureti', 'Mureti', 'Mureti', 'Mureti@Mureti.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1997-01-01', '624586235', 'admin', '2024-10-21 08:02:32', 'activo', NULL, 0),
(75, 'BeitaJ', 'BeitaJ', 'BeitaJ', 'BeitaJ@BeitaJ.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2002-01-01', '654321478', 'admin', '2024-10-21 09:03:38', 'activo', NULL, 0),
(76, 'MariCarla', 'MariCarla', 'MariCarla', 'MariCarla@MariCarla.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2002-02-02', '633842651', 'usuario', '2024-10-21 10:45:37', 'activo', NULL, 0),
(77, 'MonoMonito', 'MonoMonito', 'MonoMonito', 'MonoMonito@MonoMonito.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-01-01', '685447788', 'usuario', '2024-10-21 10:51:30', 'activo', NULL, 0),
(78, 'Pichurritos', 'Pichurritos', 'Pichurritos', 'Pichurritos@Pichurritos.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2003-03-03', '698447852', 'admin', '2024-10-21 11:20:39', 'activo', NULL, 0),
(79, 'Usu79', 'Usu79', 'Usu79', 'Usu79@Usu79.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1979-09-09', '635221144', 'admin', '2024-10-21 11:42:34', 'activo', NULL, 0),
(80, 'Jaime80', 'Jaime80', 'Jaime80', 'Jaime80@Jaime80.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2001-01-01', '633254869', 'admin', '2024-10-21 12:07:21', 'activo', NULL, 0),
(81, 'Usu81', 'Usu81', 'Usu81', 'Usu81@Usu81.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1997-07-07', '633847595', 'admin', '2024-10-21 12:18:43', 'activo', NULL, 0),
(82, 'Usu82', 'Usu82', 'Usu82', 'Usu82@Usu82.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1991-10-10', '658998877', 'admin', '2024-10-21 12:36:35', 'activo', NULL, 0),
(85, 'OchentaYcinco', 'OchentaYcinco', 'OchentaYcinco', 'OchentaYcinco@OchentaYcinco.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1996-06-06', '655388475', 'registro', '2024-10-21 15:27:11', 'activo', NULL, 0),
(86, 'OchentaYSeis', 'OchentaYSeis', 'OchentaYSeis', 'OchentaYSeis@OchentaYSeis.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-05-02', '654445566', 'admin', '2024-10-21 15:42:33', 'activo', NULL, 0),
(87, 'OchentaYsiete', 'OchentaYsiete', 'OchentaYsiete', 'OchentaYsiete@OchentaYsiete.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1976-05-06', '652489563', 'admin', '2024-10-21 15:59:34', 'activo', NULL, 0),
(88, 'Calimero', 'Calimero', 'Calimero', 'Calimero@Calimero.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2003-03-03', '625333333', 'admin', '2024-10-21 18:07:12', 'activo', NULL, 0),
(89, 'Bobito', 'Bobito', 'Bobito', 'Bobito@Bobito.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-06-06', '658585858', 'admin', '2024-10-21 18:15:54', 'activo', NULL, 0),
(90, 'Bobito90', 'Bobito90', 'Bobito90', 'Bobito90@Bobito90.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1995-09-09', '958652545', 'admin', '2024-10-21 18:21:43', 'activo', NULL, 0),
(91, 'Bobito91', 'Bobito91', 'Bobito91', 'Bobito91@Bobito91.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-12-13', '584625847', 'usuario', '2024-10-21 18:24:58', 'activo', NULL, 0),
(92, 'abcdefg', 'abcdefg', 'abcdefg', 'abcdefg@abcdefg', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1997-07-07', '655332244', 'admin', '2024-10-22 14:24:26', 'activo', NULL, 0),
(93, 'NoventayUno', 'NoventayUno', 'NoventayUno', 'NoventayUno@NoventayUno.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1997-08-09', '352154578', 'usuario', '2024-10-22 14:32:57', 'activo', NULL, 0),
(94, 'NovetayCuatro', 'NovetayCuatro', 'NovetayCuatro', 'NovetayCuatro@NovetayCuatro.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2002-01-01', '368486857', 'admin', '2024-10-22 14:36:43', 'activo', NULL, 0),
(95, 'NoventayCinco', 'NoventayCinco', 'NoventayCinco', 'NoventayCinco@NoventayCinco.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '0000-00-00', '', 'admin', '2024-10-22 15:07:58', 'activo', NULL, 0),
(96, 'NoventaySeis', 'NoventaySeis', 'NoventaySeis', 'NoventaySeis@NoventaySeis.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1996-06-05', '695558847', 'admin', '2024-10-22 15:14:35', 'activo', NULL, 0),
(97, 'NoventaySiete', 'NoventaySiete', 'NoventaySiete', 'NoventaySiete@NoventaySiete.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '0000-00-00', '', 'admin', '2024-10-22 15:23:38', 'activo', NULL, 0),
(98, 'NoventayOcho', 'NoventayOcho', 'NoventayOcho', 'NoventayOcho@NoventayOcho.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '0000-00-00', '', 'admin', '2024-10-22 15:35:30', 'activo', NULL, 0),
(99, 'NoventayNueve', 'NoventayNueve', 'NoventayNueve', 'NoventayNueve@NoventayNueve.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '0000-00-00', '', 'admin', '2024-10-22 15:38:36', 'activo', NULL, 0),
(100, 'Cien', 'Cien', 'Cien', 'Cien@Cien.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1995-02-03', '625884475', 'admin', '2024-10-22 15:44:47', 'activo', NULL, 0),
(101, 'CientoUno', 'CientoUno', 'CientoUno', 'CientoUno@CientoUno.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1999-05-05', '625448878', 'admin', '2024-10-22 15:50:44', 'activo', NULL, 0),
(102, 'CientoDos', 'CientoDos', 'CientoDos', 'CientoDos@CientoDos', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1977-11-03', '658443398', 'admin', '2024-10-22 16:10:37', 'activo', NULL, 0),
(103, 'CientoTres', 'CientoTres', 'CientoTres', 'CientoTres@CientoTres.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1965-12-15', '854756525', 'admin', '2024-10-22 16:19:26', 'activo', NULL, 0),
(104, 'CientoCuatro', 'CientoCuatro', 'CientoCuatro', 'CientoCuatro@CientoCuatro.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1965-11-26', '523235487', 'usuario', '2024-10-22 16:23:50', 'activo', NULL, 0),
(105, 'CientoCinco', 'CientoCinco', 'CientoCinco', 'CientoCinco@CientoCinco.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1973-03-01', '321586848', 'admin', '2024-10-22 16:27:00', 'activo', NULL, 0),
(106, 'CientoSeis', 'CientoSeis', 'CientoSeis', 'CientoSeis@CientoSeis.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '2000-02-03', '654852512', 'admin', '2024-10-22 16:31:58', 'activo', NULL, 0),
(107, 'CientoSiete', 'CientoSiete', 'CientoSiete', 'CientoSiete@CientoSiete.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1996-06-06', '684576259', 'usuario', '2024-10-22 16:38:05', 'activo', NULL, 0),
(108, 'CientoOcho', 'CientoOcho', 'CientoOcho', 'CientoOcho@CientoOcho.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1972-02-02', '695884477', 'admin', '2024-10-22 16:41:16', 'activo', NULL, 0),
(109, 'CientoNueve', 'CientoNueve', 'CientoNueve', 'CientoNueve@CientoNueve.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1961-12-24', '625847598', 'usuario', '2024-10-22 17:00:16', 'activo', NULL, 0),
(110, 'CientoDiez', 'CientoDiez', 'CientoDiez', 'CientoDiez@CientoDiez.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1988-02-02', '325698475', 'admin', '2024-10-22 17:41:09', 'activo', NULL, 0),
(111, 'CientoOnce', 'CientoOnce', 'CientoOnce', 'CientoOnce@CientoOnce.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', '1976-06-06', '685472569', 'admin', '2024-10-22 18:20:18', 'activo', NULL, 0),
(112, 'CientoDoce', 'CientoDoce', 'CientoDoce', 'CientoDoce@CientoDoce.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 18:23:25', 'activo', NULL, 0),
(113, 'CientoTrece', 'CientoTrece', 'CientoTrece', 'CientoTrece@CientoTrece.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 18:27:06', 'activo', NULL, 0),
(114, 'CientoCatorce', 'CientoCatorce', 'CientoCatorce', 'CientoCatorce@CientoCatorce.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 18:35:04', 'activo', NULL, 0),
(115, 'CientoQuince', 'CientoQuince', 'CientoQuince', 'CientoQuince@CientoQuince.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 19:32:58', 'activo', NULL, 0),
(116, 'CientoDieciSeis', 'CientoDieciSeis', 'CientoDieciSeis', 'CientoDieciSeis@CientoDieciSeis.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 20:06:58', 'activo', NULL, 0),
(117, 'CientoDieciSiete', 'CientoDieciSiete', 'CientoDieciSiete', 'CientoDieciSiete@CientoDieciSiete.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'usuario', '2024-10-22 20:09:38', 'activo', NULL, 0),
(118, 'CientoDieciOcho', 'CientoDieciOcho', 'CientoDieciOcho', 'CientoDieciOcho@CientoDieciOcho.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 20:24:11', 'activo', NULL, 0),
(119, 'CientoDieciNueve', 'CientoDieciNueve', 'CientoDieciNueve', 'CientoDieciNueve@CientoDieciNueve.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 20:41:01', 'activo', NULL, 0),
(120, 'CientoVeinte', 'CientoVeinte', 'CientoVeinte', 'CientoVeinte@CientoVeinte.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'admin', '2024-10-22 21:10:04', 'activo', NULL, 0),
(121, 'CientoVeintiUno', 'CientoVeintiUno', 'CientoVeintiUno', 'CientoVeintiUno@CientoVeintiUno.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'usuario', '2024-10-22 21:21:45', 'activo', NULL, 0),
(122, 'CientoVeintiDos', 'CientoVeintiDos', 'CientoVeintiDos', 'CientoVeintiDos@CientoVeintiDos.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'usuario', '2024-10-22 22:34:26', 'activo', NULL, 0),
(123, 'CientoVeintiTres', 'CientoVeintiTres', 'CientoVeintiTres', 'CientoVeintiTres@CientoVeintiTres.com', '$2y$10$i09b6iEZIunPlXmNKidTQeDJspu0meyKO8a6P0aZpdL5CkmrgkxoK', NULL, NULL, 'usuario', '2024-10-22 23:15:56', 'activo', NULL, 0),
(124, 'Ana', 'Pérez', 'ana_p', 'ana.perez@example.com', '$2y$10$b3UvyMyJL/K/P7YJMFRbTO1xf7qxiUSWkhyFXQi7DVzOdATByxPqK', NULL, NULL, 'usuario', '2024-10-23 10:03:05', 'activo', NULL, 0),
(125, 'CientoVeintiCinco', 'CientoVeintiCinco', 'CientoVeintiCinco', 'CientoVeintiCinco@CientoVeintiCinco.com', '$2y$10$5w5eZGMQSb5Qaarn9dYZQOLcfHcAnxT5QhDrXpzWhTJwW4q2iRszq', NULL, NULL, 'usuario', '2024-10-23 15:09:33', 'activo', NULL, 0),
(126, 'CientoVeintiSeis', 'CientoVeintiSeis', 'CientoVeintiSeis', 'CientoVeintiSeis@CientoVeintiSeis.com', '$2y$10$Z3krSaow2SVpEy8EAQD8g.7SsreHQxD57/SRulOxWEUJLp4JNHyF.', NULL, NULL, 'usuario', '2024-10-23 16:41:47', 'activo', '$2y$10$r8/thWJl9/38/hB3Lt5jzOFtpSU196PPZsU9BlMUjuQlarC6r1NSW', 0),
(127, 'CientoVeintiSiete', 'CientoVeintiSiete', 'CientoVeintiSiete', 'CientoVeintiSiete@CientoVeintiSiete.com', '$2y$10$Ak0rl67F7QyKwfivUnx6QenvudjcejOFveS7E6ayorA9uxXsiHK9W', NULL, NULL, 'admin', '2024-10-23 16:49:43', 'activo', '$2y$10$Qi1j5OV/rITfvd4TJOsZCeNctXeiprMWYGq89uvXawCWG1Mso/1PG', 0),
(128, 'CientoVeintiOcho', 'CientoVeintiOcho', 'CientoVeintiOcho', 'CientoVeintiOcho@CientoVeintiOcho.com', '$2y$10$xQv31cTF98BvdX9LzPS5tuxZGWLE5h1dESaHCTo0PbvtJdLG6pbky', NULL, NULL, 'admin', '2024-10-23 19:50:56', 'activo', '$2y$10$G0tlKPlNyfm9iaJyt3PY3uyv3k3AARoEepA8E3g8vTbk3GVPZg/E.', 0),
(129, 'CientoVeitiNueve', 'CientoVeitiNueve', 'CientoVeitiNueve', 'CientoVeitiNueve@CientoVeitiNueve.com', '$2y$10$6upgX04ptVIe2A0jS6am8Owk61oPRvtkipje4rbpcae97UiaeFIwe', NULL, NULL, 'usuario', '2024-10-24 14:10:58', 'activo', '$2y$10$p///Fygr8hE//mEELCisROcGKIebe5nCw0Zkqel6DJnjEm1drIwQO', 0),
(130, 'CientoTreinta', 'CientoTreinta', 'CientoTreinta', 'CientoTreinta@CientoTreinta.com', '$2y$10$VRk0fcTDhfIbVd3ulDQ4leU4v/RldHtnbz/pH1ygy2IvTJOfoGH/K', NULL, NULL, 'admin', '2024-10-24 14:27:49', 'activo', NULL, 0),
(131, 'CientoTreintayUno', 'CientoTreintayUno', 'CientoTreintayUno', 'CientoTreintayUno@CientoTreintayUno.com', '$2y$10$y4XCbXk4FHQFX7bhQYkaxewHqdocGW6eWDEpG/WdKvqWJ.FWdp0Q.', NULL, NULL, 'admin', '2024-10-24 14:51:14', 'activo', NULL, 0),
(132, 'CientoTreintayDos', 'CientoTreintayDos', 'CientoTreintayDos', 'CientoTreintayDos@CientoTreintayDos.com', '$2y$10$qS83UMpbm7/Bd7AMevAO9OFAGadMl8sX2GTR77zjtY.Ss5IM782gi', NULL, NULL, 'admin', '2024-10-24 15:03:04', 'activo', '$2y$10$Vi/.AF/qJr/js0xTTAPdqOstnL4TTquqJjgFvzSh4PU7jJXAVOifO', 0),
(133, 'CientoTreintayTres', 'CientoTreintayTres', 'CientoTreintayTres', 'CientoTreintayTres@CientoTreintayTres.com', '$2y$10$kI2Ovmyx/HndoJvmmExhY.fAP0dS5Z4MfcY1O2curTwAZR6FU8tfq', NULL, NULL, 'admin', '2024-10-24 17:15:56', 'activo', '$2y$10$lahnWvVQ/KQeO3QPCNtuMuJHNrfvZQFeiqkD92/n1s/PifDvF3AKq', 0),
(134, 'CientoTrintayCinco', 'CientoTrintayCinco', 'CientoTrintayCinco', 'CientoTrintayCinco@CientoTrintayCinco.com', '$2y$10$aBv2TYdReTCiBkDxtho3BOcGZzGgx7cxLeGf7K5qWn5lsxO.jht/W', NULL, NULL, 'usuario', '2024-10-24 17:41:58', 'activo', '$2y$10$N4DnIhmUWIZ/n9ovP9kZMejCMBc5IH2CBBKGcN5tS7duzB0F22c2u', 0),
(135, 'CientoTreintayCinco', 'CientoTreintayCinco', 'CientoTreintayCinco', 'CientoTreintayCinco@CientoTreintayCinco.com', '$2y$10$/dpzduNx9/SoEs8tL4AsIu2e6k/N1M5niV1kZ1eVwk00tyM6OMRY2', NULL, NULL, 'admin', '2024-10-24 17:48:53', 'activo', '$2y$10$qZswWUjK1/.k4LP/ogz0eeVrfWhixrrPm/7XkfsLTdSr16Vbo0DHO', 0);

--
-- Disparadores `usuarios`
--
DROP TRIGGER IF EXISTS `auditar_creacion_usuario`;
DELIMITER $$
CREATE TRIGGER `auditar_creacion_usuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_usuarios`;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, 'sistema', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_usuarios_delete`;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_delete` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'usuarios', OLD.idUser, OLD.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_usuarios_delete_archivo`;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_delete_archivo` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (OLD.idUser, 'DELETE', NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_usuarios_insert`;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_usuarios_update`;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_update` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_familias`
--

DROP TABLE IF EXISTS `usuarios_familias`;
CREATE TABLE `usuarios_familias` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_familias`
--

INSERT INTO `usuarios_familias` (`id`, `idUser`, `idFamilia`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 5, 1),
(4, 6, 1),
(5, 7, 1),
(6, 8, 1),
(8, 27, 30),
(9, 31, 30),
(10, 16, 23),
(12, 20, 1),
(13, 19, 22),
(14, 19, 23),
(15, 33, 30),
(16, 34, 31),
(17, 41, 31),
(18, 42, 31),
(19, 44, 31),
(20, 51, 31),
(21, 55, 32),
(22, 59, 33),
(23, 60, 33),
(24, 61, 34),
(25, 63, 35),
(26, 63, 34),
(27, 65, 34),
(28, 65, 35),
(29, 65, 36),
(30, 65, 37),
(31, 38, 50),
(32, 27, 51),
(33, 41, 53),
(34, 61, 55),
(35, 72, 1),
(36, 72, 57),
(37, 73, 58),
(38, 74, 59),
(39, 74, 58),
(40, 75, 60),
(41, 75, 59),
(42, 86, 61),
(43, 87, 62),
(44, 87, 60),
(45, 89, 63),
(46, 93, 64),
(47, 96, 65),
(48, 103, 66),
(49, 104, 66),
(50, 105, 66),
(51, 106, 67),
(52, 107, 68),
(53, 108, 68),
(54, 109, 69),
(55, 110, 70),
(56, 112, 71),
(57, 113, 61),
(58, 116, 72),
(59, 118, 73),
(60, 118, 74),
(61, 119, 75),
(62, 119, 76),
(63, 119, 77),
(64, 120, 78),
(65, 120, 79),
(66, 120, 80),
(67, 120, 81),
(68, 120, 82),
(69, 127, 83),
(70, 127, 84),
(71, 127, 85),
(72, 128, 86),
(73, 128, 87),
(74, 130, 88),
(75, 131, 89),
(76, 132, 90),
(77, 132, 91),
(78, 133, 92),
(79, 133, 93),
(80, 134, 94),
(81, 135, 95),
(82, 135, 96);

--
-- Disparadores `usuarios_familias`
--
DROP TRIGGER IF EXISTS `before_insert_usuarios_familias`;
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

DROP TABLE IF EXISTS `usuarios_grupos`;
CREATE TABLE `usuarios_grupos` (
  `idUser` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`idUser`, `idGrupo`) VALUES
(1, 1),
(2, 1),
(9, 1),
(10, 1),
(11, 1),
(11, 11),
(18, 11),
(19, 9),
(20, 1),
(20, 9),
(20, 11),
(36, 1),
(37, 14),
(41, 14),
(42, 14),
(43, 14),
(46, 14),
(55, 15),
(59, 16),
(60, 16),
(61, 17),
(63, 17),
(63, 18),
(65, 17),
(65, 18),
(65, 19),
(65, 20),
(72, 15),
(73, 21),
(74, 21),
(74, 22),
(75, 22),
(75, 23),
(86, 24),
(87, 23),
(87, 25),
(89, 26),
(96, 27),
(103, 28),
(104, 28),
(105, 28),
(106, 29),
(107, 30),
(108, 30),
(109, 31),
(110, 32),
(112, 33),
(113, 24),
(116, 34),
(119, 35),
(119, 36),
(119, 37),
(120, 38),
(120, 39),
(120, 40),
(120, 41),
(120, 42),
(120, 43),
(120, 44),
(120, 45),
(120, 46),
(120, 47),
(127, 48),
(127, 49),
(127, 50),
(128, 51),
(128, 52),
(130, 53),
(131, 54),
(132, 55),
(132, 56),
(133, 57),
(133, 58),
(135, 59),
(135, 60);

--
-- Disparadores `usuarios_grupos`
--
DROP TRIGGER IF EXISTS `before_insert_usuarios_grupos`;
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
  ADD UNIQUE KEY `idAdmin` (`idAdmin`,`idFamilia`),
  ADD KEY `fk_familia` (`idFamilia`);

--
-- Indices de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `fk_admin_grupo` (`idAdmin`);

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
-- Indices de la tabla `contrasenyas_premium`
--
ALTER TABLE `contrasenyas_premium`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `menuadmin`
--
ALTER TABLE `menuadmin`
  ADD PRIMARY KEY (`idMenu`);

--
-- Indices de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD PRIMARY KEY (`idEnvio`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idRefran` (`idRefran`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`idNotificacion`);

--
-- Indices de la tabla `permisos_cache`
--
ALTER TABLE `permisos_cache`
  ADD PRIMARY KEY (`idUser`,`nombrePermiso`,`tipoPermiso`);

--
-- Indices de la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  ADD PRIMARY KEY (`idPreferencia`),
  ADD KEY `idUser` (`idUser`);

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
  ADD KEY `idx_usuario_nivel` (`nivel_usuario`),
  ADD KEY `idx_usuarios_nivel_familia_grupo` (`nivel_usuario`),
  ADD KEY `idx_usuarios_nivel_fecha` (`nivel_usuario`,`fecha_registro`),
  ADD KEY `idx_usuarios_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_nombre` (`nombre`),
  ADD KEY `idx_usuarios_apellido` (`apellido`),
  ADD KEY `idx_idUser` (`idUser`);

--
-- Indices de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idFamilia` (`idFamilia`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2719;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=524;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contrasenyas_premium`
--
ALTER TABLE `contrasenyas_premium`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `idIngreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `menuadmin`
--
ALTER TABLE `menuadmin`
  MODIFY `idMenu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  MODIFY `idPreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `refranes`
--
ALTER TABLE `refranes`
  MODIFY `idRefran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `idPermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `idSituacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD CONSTRAINT `fk_admin_grupo` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  ADD CONSTRAINT `preferencias_usuarios_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

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
-- Filtros para la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD CONSTRAINT `usuarios_familias_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_familias_ibfk_2` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_usuario_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario_grupo_usuario` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
DROP EVENT IF EXISTS `limpiar_cache_permisos`$$
CREATE DEFINER=`root`@`localhost` EVENT `limpiar_cache_permisos` ON SCHEDULE EVERY 1 HOUR STARTS '2024-10-16 09:29:52' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM permisos_cache WHERE TIMESTAMPDIFF(HOUR, fecha_cache, NOW()) > 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
