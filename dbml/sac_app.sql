-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 29-08-2022 a las 17:13:21
-- Versión del servidor: 5.7.36
-- Versión de PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sac_app`
--
CREATE DATABASE IF NOT EXISTS `sac_app` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sac_app`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'S', '2022-02-22 16:47:52', '2022-08-29 15:15:49'),
(2, 'SAC', 'S', '2022-02-23 16:57:58', '2022-08-29 15:15:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_articulos_favoritos`
--

DROP TABLE IF EXISTS `tbl_articulos_favoritos`;
CREATE TABLE IF NOT EXISTS `tbl_articulos_favoritos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Articulo` varchar(15) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_articulos_favoritos`
--

INSERT INTO `tbl_articulos_favoritos` (`id`, `Articulo`, `created_at`, `updated_at`) VALUES
(41, '13705012', '2022-08-26 16:47:29', '2022-08-26 16:47:29'),
(42, '13401013', '2022-08-26 16:47:48', '2022-08-26 16:47:48'),
(40, '11720011', '2022-08-26 16:47:11', '2022-08-26 16:47:11'),
(35, '17303021', '2022-08-25 22:30:37', '2022-08-25 22:30:37'),
(39, '13705105', '2022-08-26 16:46:37', '2022-08-26 16:46:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_rutas_asginadas`
--

DROP TABLE IF EXISTS `tbl_rutas_asginadas`;
CREATE TABLE IF NOT EXISTS `tbl_rutas_asginadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `RUTA` varchar(10) DEFAULT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_rutas_asginadas`
--

INSERT INTO `tbl_rutas_asginadas` (`id`, `RUTA`, `id_usuario`, `created_at`, `updated_at`) VALUES
(13, 'F06', 9, '2022-08-25 22:16:14', '2022-08-25 22:16:14'),
(14, 'F14', 9, '2022-08-25 22:16:22', '2022-08-25 22:16:22'),
(15, 'F19', 9, '2022-08-25 22:16:29', '2022-08-25 22:16:29'),
(16, 'F04', 10, '2022-08-25 22:17:15', '2022-08-25 22:17:15'),
(17, 'F18', 10, '2022-08-25 22:17:22', '2022-08-25 22:17:22'),
(18, 'F11', 12, '2022-08-25 22:17:55', '2022-08-25 22:17:55'),
(19, 'F20', 12, '2022-08-25 22:18:01', '2022-08-25 22:18:01'),
(20, 'F07', 13, '2022-08-25 22:18:09', '2022-08-25 22:18:09'),
(21, 'F08', 13, '2022-08-25 22:18:14', '2022-08-25 22:18:14'),
(22, 'F23', 13, '2022-08-25 22:18:19', '2022-08-25 22:18:19'),
(23, 'F06', 13, '2022-08-25 22:18:25', '2022-08-25 22:18:25'),
(24, 'F14', 13, '2022-08-25 22:18:31', '2022-08-25 22:18:31'),
(25, 'F19', 13, '2022-08-25 22:18:37', '2022-08-25 22:18:37'),
(26, 'F05', 14, '2022-08-25 22:18:50', '2022-08-25 22:18:50'),
(27, 'F13', 14, '2022-08-25 22:18:56', '2022-08-25 22:18:56'),
(28, 'F03', 14, '2022-08-25 22:19:04', '2022-08-25 22:19:04'),
(29, 'F21', 14, '2022-08-25 22:19:15', '2022-08-25 22:19:15'),
(30, 'F10', 15, '2022-08-25 22:19:29', '2022-08-25 22:19:29'),
(31, 'F09', 15, '2022-08-25 22:19:34', '2022-08-25 22:19:34'),
(32, 'F22', 15, '2022-08-25 22:19:39', '2022-08-25 22:19:39'),
(33, 'F04', 15, '2022-08-25 22:19:45', '2022-08-25 22:19:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `activo` varchar(5) DEFAULT NULL,
  `id_rol` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `username`, `password`, `activo`, `id_rol`, `created_at`, `updated_at`) VALUES
(7, 'maryan adan espinoza', 'marangelo', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 1, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(8, 'SAC01 | Alejandra', 'SAC01', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(9, 'SAC08 | NADIESKA URBINA', 'SAC08', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(10, 'SAC07 | Marisela Sevilla', 'SAC07', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(11, 'SAC6 | Maria Alejandra', 'SAC06', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'N', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(12, 'SAC05 | Yorleni Urbina', 'SAC05', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(13, 'SAC04 | JESSICA LUCILA', 'SAC04', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(14, 'SAC03 | AURA CAROLINA', 'SAC03', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07'),
(15, 'SAC02 | REYNA ALVAREZ', 'SAC02', '$2y$10$EpeGAcAmEGiIaJ.eqDxlQeneAfxDL0QcyT0n0z77MufwSV9VBaQzG', 'S', 2, '2022-08-29 21:41:07', '2022-08-29 21:41:07');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
