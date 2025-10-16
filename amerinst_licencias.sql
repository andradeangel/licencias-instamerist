-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 03:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amerinst_licencias`
--

-- --------------------------------------------------------

--
-- Table structure for table `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `ci` varchar(20) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `nombre`, `apellido`, `fecha_nacimiento`, `ci`, `fecha_creacion`) VALUES
(1, 'Romell', 'Vargas', '2010-05-15', '12345678', '2025-10-10 21:21:00'),
(2, 'Inés', 'Pérez ', '2011-03-20', '87654321', '2025-10-10 21:21:00'),
(3, 'Raúl ', 'Suarez', '2009-11-10', '11223344', '2025-10-10 21:21:00'),
(4, 'Laura', 'Gómez ', '2012-07-05', '44332211', '2025-10-10 21:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `estudiante_padre`
--

CREATE TABLE `estudiante_padre` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `padre_id` int(11) NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estudiante_padre`
--

INSERT INTO `estudiante_padre` (`id`, `estudiante_id`, `padre_id`, `fecha_asignacion`) VALUES
(1, 1, 4, '2025-10-10 21:21:00'),
(2, 2, 4, '2025-10-10 21:21:00'),
(3, 3, 5, '2025-10-10 21:21:00'),
(4, 4, 5, '2025-10-10 21:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `licencias`
--

CREATE TABLE `licencias` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `fecha_licencia` date NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `creada_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `licencias`
--

INSERT INTO `licencias` (`id`, `estudiante_id`, `fecha_licencia`, `motivo`, `estado`, `fecha_creacion`, `creada_por`) VALUES
(1, 1, '2023-10-16', 'Enfermedad Leve', 'rechazado', '2025-10-10 21:21:00', 4),
(2, 2, '2023-10-16', 'Cita médica', 'pendiente', '2025-10-10 21:21:00', 4),
(3, 3, '2023-10-17', 'Problemas familiares', 'pendiente', '2025-10-10 21:21:00', 5),
(4, 4, '2023-10-18', 'Viaje', 'pendiente', '2025-10-10 21:21:00', 5),
(6, 2, '2025-10-15', 'Trabajos de casa', 'aceptado', '2025-10-11 02:07:38', 4),
(7, 2, '2025-10-28', 'POR MOTIVO DE DEPORTES', 'aceptado', '2025-10-11 16:12:51', 4);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','docente','padre') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `fecha_creacion`, `activo`) VALUES
(1, 'Alejandro', 'Villarroel', 'admin@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-10-10 21:21:00', 1),
(2, 'Ginebra', 'Quispe', 'docente1@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', '2025-10-10 21:21:00', 1),
(3, 'José', 'Borguez', 'docente2@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', '2025-10-10 21:21:00', 1),
(4, 'Pablo', 'Lopez', 'padre1@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'padre', '2025-10-10 21:21:00', 1),
(5, 'Pedro', 'Dante', 'padre2@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'padre', '2025-10-10 21:21:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ci` (`ci`);

--
-- Indexes for table `estudiante_padre`
--
ALTER TABLE `estudiante_padre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estudiante_id` (`estudiante_id`,`padre_id`),
  ADD KEY `padre_id` (`padre_id`);

--
-- Indexes for table `licencias`
--
ALTER TABLE `licencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `creada_por` (`creada_por`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `estudiante_padre`
--
ALTER TABLE `estudiante_padre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `licencias`
--
ALTER TABLE `licencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `estudiante_padre`
--
ALTER TABLE `estudiante_padre`
  ADD CONSTRAINT `estudiante_padre_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estudiante_padre_ibfk_2` FOREIGN KEY (`padre_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `licencias`
--
ALTER TABLE `licencias`
  ADD CONSTRAINT `licencias_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `licencias_ibfk_2` FOREIGN KEY (`creada_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
