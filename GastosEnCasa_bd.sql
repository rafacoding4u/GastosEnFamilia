-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-10-2024 a las 18:55:13
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
(5, 2, 10),
(6, 2, 11),
(7, 2, 12),
(9, 2, 14),
(10, 2, 15),
(4, 16, 9),
(8, 16, 13),
(11, 16, 16),
(12, 16, 19),
(13, 17, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_grupos`
--

CREATE TABLE `administradores_grupos` (
  `idAdmin` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_grupos`
--

INSERT INTO `administradores_grupos` (`idAdmin`, `idGrupo`, `id`) VALUES
(2, 1, 1);

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
(259, 'UPDATE', 'usuarios', 17, '17', '2024-10-12 16:53:00');

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
(134, 1, 'logout', '2024-10-12 16:53:12');

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
(39, 'Gastos imprevistos', 1, 'activo', 'gasto');

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
  `password` varchar(255) NOT NULL,
  `idAdmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familias`
--

INSERT INTO `familias` (`idFamilia`, `nombre_familia`, `password`, `idAdmin`) VALUES
(1, 'Familia1', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', 2),
(16, 'Familia juan', '$2y$10$6tMRSAZY4kUDE/bXGryequwQ1w3rmqjuI.9VpNVPYWv1S03wzuWSW', 2),
(17, 'Famila paraProbar', '$2y$10$4TDdNc7ktRlQ.QlzntYNYe40MkM8nJ6c7nRunLIFlvO3gMcjggQ0K', 16),
(18, 'Familia Actualizada', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', 2),
(19, 'Otra Familia más+', '$2y$10$4GxsohZgXza1d8JYsg2aducihKyyw31zEca3vaOJICm0j8mOhjcu.', 16),
(20, 'F63', '$2y$10$W0oMHrG9TKOTZsm51cCa6u5iOVvXn3jWcngZ64kCPBqiQPbB5Af4S', 17);

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
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha) 
  VALUES ('delete', 'familias', OLD.idFamilia, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_update` AFTER UPDATE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'familias', NEW.idFamilia, 'sistema', NOW());
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
(18, 2, 120.50, 23, 'banco', 'Mercadona', '2024-10-07 22:00:00', 1, 1),
(19, 2, 48.25, 24, 'efectivo', 'Cena', '2024-10-07 22:00:00', 1, 1),
(20, 2, 80.23, 25, 'banco', 'Factura Luz', '2024-10-07 22:00:00', 1, 1),
(21, 2, 80.00, 26, 'banco', 'Factura Agua', '2024-10-07 22:00:00', 1, 1);

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
  `password` varchar(255) NOT NULL,
  `idAdmin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idGrupo`, `nombre_grupo`, `password`, `idAdmin`) VALUES
(1, 'Grupo1', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', 2),
(9, 'Grupitoo lllll', '$2y$10$kT8Se.laK5eOSO.Gg.mxUOSJOOSI/ijZ9zHGYjM/VtAtxMaW5lKs2', 2),
(11, 'B3', '$2y$10$W7OSfqRJR1jY9LJx1Ao6XeY16b6Ab7N1Y66KQS76bSkNlS1ztss0O', NULL);

--
-- Disparadores `grupos`
--
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha)
    VALUES ('DELETE', 'grupos', OLD.idGrupo, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_insert` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('INSERT', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_update` AFTER UPDATE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('UPDATE', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
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
(17, 2, 1500.00, 29, 'banco', 'Sueldo Rafa', '2024-10-07 22:00:00', 1, 1),
(18, 2, 50.00, 30, 'efectivo', 'Cumple Rafa', '2024-10-07 22:00:00', 1, 1),
(19, 2, 15.00, 31, 'efectivo', 'Lotería', '2024-10-07 22:00:00', 1, 1),
(20, 2, 350.00, 32, 'efectivo', 'Venta móvil', '2024-10-07 22:00:00', 1, 1);

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
(1, 'Super', 'Admin', 'superadmin', 'superadmin@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1970-01-01', '625418965', 'superadmin', '2024-10-04 10:03:48', 1, NULL, 'activo'),
(2, 'Admin1', 'Family1', 'admin1', 'admin1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1980-01-01', '625418965', 'admin', '2024-10-04 10:04:06', 1, 1, 'activo'),
(5, 'Pareja1', 'Family1', 'pareja1', 'pareja1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1982-02-02', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(6, 'Hijo1', 'Family1', 'hijo1', 'hijo1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2005-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(7, 'Hijo2', 'Family1', 'hijo2', 'hijo2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2007-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(8, 'Hijo3', 'Family1', 'hijo3', 'hijo3@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '2010-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 1, NULL, 'activo'),
(9, 'Amigo1', 'Grupo1', 'amigo1', 'amigo1@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1990-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(10, 'Amigo2', 'Grupo1', 'amigo2', 'amigo2@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1991-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(11, 'Amigo3', 'Grupo1', 'amigo3', 'amigo3@example.com', '$2y$10$HPaFq14JD6huWeE5RFkPROBK9H471071Ze6iP6.61HZkcPhVquZzi', '1992-03-03', '625418965', 'admin', '2024-10-04 10:05:49', NULL, 1, 'activo'),
(16, 'kk', 'kkkk', 'kk', 'kk@kk.com', '$2y$10$5o9QopABL4QqSHujwp8Nk.1GGDb9AJACA7AEwma2NGWFZhxY4QfKe', '2004-04-04', '455545545', 'admin', '2024-10-10 16:31:32', NULL, NULL, 'activo'),
(17, 'Nuevito', 'Nuevito', 'Nuevito', 'Nuevito@Nuevito.com', '$2y$10$s6KAbcStIJXFBeeJ1930ZeKcMpZhdWN6LMMO3TYWZ4TMpWFKo9QIO', '2000-12-17', '552685456', 'admin', '2024-10-12 11:13:29', 1, 1, 'activo');

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
(1, 1),
(2, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(17, 1);

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
(9, 1),
(10, 1),
(11, 1);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `fk_admin_grupo_admin` (`idAdmin`);

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
  ADD KEY `idx_grupos_nombre` (`nombre_grupo`),
  ADD KEY `fk_grupo_admin` (`idAdmin`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `idIngreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD CONSTRAINT `fk_admin_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON UPDATE CASCADE;

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
