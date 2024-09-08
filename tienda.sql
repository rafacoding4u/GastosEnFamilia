-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-06-2024 a las 16:43:11
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Crea la base de datos si no existe y usarla
CREATE DATABASE IF NOT EXISTS `tienda` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tienda`;

-- Crea la tabla 'categorias'
CREATE TABLE `categorias` (
  `idCategoria` int(11) NOT NULL,
  `nombreCategoria` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Inserta datos en 'categorias'
INSERT INTO `categorias` (`idCategoria`, `nombreCategoria`) VALUES
(1, 'Electrónica'),
(2, 'Ropa'),
(3, 'Hogar'),
(4, 'Literatura'),
(5, 'Muebles');

-- Crea la tabla 'productos'
CREATE TABLE `productos` (
  `idProducto` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Inserta datos en 'productos'
INSERT INTO `productos` (`idProducto`, `nombre`, `categoria_id`, `precio`) VALUES
(1, 'Laptop', 1, 999.99),
(2, 'Smartphone', 1, 599.99),
(3, 'Camiseta', 2, 19.99),
(4, 'Jeans', 2, 49.99),
(5, 'Cafetera', 3, 89.99),
(6, 'Libro', 4, 15.99),
(7, 'Silla', 5, 75.99),
(8, 'Mesa', 5, 129.99),
(9, 'Monitor', 1, 199.99);

-- Crea la tabla 'usuarios'
CREATE TABLE `usuarios` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `apellido` varchar(40) NOT NULL,
  `nombreUsuario` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `contrasenya` varchar(256) NOT NULL,
  `nivel_usuario` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Inserta datos en 'usuarios', contraseñas encriptadas mediante token para rescatar el acceso perdido
INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `nombreUsuario`, `contrasenya`, `nivel_usuario`) VALUES
(1, 'Admin', 'Admin', 'admin', '$2y$10$e0MYzXyjpJS2R48kXjY.KeFJv/H5/YPk8DgZX90.wQK3hNksXbrcW', 2), -- Contraseña: "admin"
(2, 'Usuario', 'Normal', 'usuario', '$2y$10$e0MYzXyjpJS2R48kXjY.KeFJv/H5/YPk8DgZX90.wQK3hNksXbrcW', 1); -- Contraseña: "usuario"

-- Define las claves primarias y relaciones
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`idCategoria`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `categoria_id` (`categoria_id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `uk_nombreUsuario` (`nombreUsuario`);

-- Ajusta AUTO_INCREMENT
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `productos`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

