-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2025 a las 23:21:09
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
-- Base de datos: `radiomorrazo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audiolibros`
--

CREATE TABLE `audiolibros` (
  `id` int(11) NOT NULL,
  `titulo_es` varchar(200) NOT NULL,
  `titulo_gl` varchar(200) NOT NULL,
  `autor` varchar(200) NOT NULL,
  `descripcion_es` text NOT NULL,
  `descripcion_gl` text NOT NULL,
  `anio_publicacion` int(11) DEFAULT NULL,
  `duracion` varchar(10) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `archivo` varchar(255) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `reproducciones` int(11) NOT NULL DEFAULT 0,
  `descargas` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `creado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_audiolibro`
--

CREATE TABLE `categorias_audiolibro` (
  `id` int(11) NOT NULL,
  `nombre_es` varchar(100) NOT NULL,
  `nombre_gl` varchar(100) NOT NULL,
  `descripcion_es` text DEFAULT NULL,
  `descripcion_gl` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_podcast`
--

CREATE TABLE `categorias_podcast` (
  `id` int(11) NOT NULL,
  `nombre_es` varchar(100) NOT NULL,
  `nombre_gl` varchar(100) NOT NULL,
  `descripcion_es` text DEFAULT NULL,
  `descripcion_gl` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_podcast`
--

CREATE TABLE `comentarios_podcast` (
  `id` int(11) NOT NULL,
  `podcast_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `comentario` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `aprobado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`) VALUES
(1, 'site_name', 'Radio Morrazo', 'Nombre del sitio web'),
(2, 'site_description', 'Tu emisora local con la mejor programación', 'Descripción del sitio web'),
(3, 'contact_email', 'info@radiomorrazo.com', 'Email de contacto principal'),
(4, 'contact_phone', '+34 986 000 000', 'Teléfono de contacto'),
(5, 'contact_address', 'Dirección, Morrazo, Pontevedra', 'Dirección física'),
(6, 'facebook_url', '#', 'URL de Facebook'),
(7, 'twitter_url', '#', 'URL de Twitter'),
(8, 'instagram_url', '#', 'URL de Instagram'),
(9, 'youtube_url', '#', 'URL de YouTube'),
(10, 'default_lang', 'es', 'Idioma predeterminado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas`
--

CREATE TABLE `estadisticas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `visitas` int(11) NOT NULL DEFAULT 0,
  `reproducciones_podcast` int(11) NOT NULL DEFAULT 0,
  `descargas_podcast` int(11) NOT NULL DEFAULT 0,
  `reproducciones_audiolibro` int(11) NOT NULL DEFAULT 0,
  `descargas_audiolibro` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo_es` varchar(200) NOT NULL,
  `titulo_gl` varchar(200) NOT NULL,
  `descripcion_es` text NOT NULL,
  `descripcion_gl` text NOT NULL,
  `fecha` date NOT NULL,
  `hora` time DEFAULT NULL,
  `lugar` varchar(200) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `enlace` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `creado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_acceso`
--

CREATE TABLE `logs_acceso` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `podcasts`
--

CREATE TABLE `podcasts` (
  `id` int(11) NOT NULL,
  `titulo_es` varchar(200) NOT NULL,
  `titulo_gl` varchar(200) NOT NULL,
  `descripcion_es` text NOT NULL,
  `descripcion_gl` text NOT NULL,
  `fecha` date NOT NULL,
  `duracion` varchar(10) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `archivo` varchar(255) NOT NULL,
  `programa_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `reproducciones` int(11) NOT NULL DEFAULT 0,
  `descargas` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `creado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas`
--

CREATE TABLE `programas` (
  `id` int(11) NOT NULL,
  `nombre_es` varchar(200) NOT NULL,
  `nombre_gl` varchar(200) NOT NULL,
  `descripcion_es` text NOT NULL,
  `descripcion_gl` text NOT NULL,
  `horario` varchar(200) NOT NULL,
  `dias` varchar(200) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `creado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traducciones`
--

CREATE TABLE `traducciones` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `idioma` enum('es','gl') NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `traducciones`
--

INSERT INTO `traducciones` (`id`, `clave`, `idioma`, `value`) VALUES
(1, 'menu_home', 'es', 'Inicio'),
(2, 'menu_home', 'gl', 'Inicio'),
(3, 'menu_programs', 'es', 'Programas'),
(4, 'menu_programs', 'gl', 'Programas'),
(5, 'menu_podcasts', 'es', 'Podcasts'),
(6, 'menu_podcasts', 'gl', 'Podcasts'),
(7, 'menu_audiobooks', 'es', 'Audiolibros'),
(8, 'menu_audiobooks', 'gl', 'Audiolibros'),
(9, 'menu_about', 'es', 'Sobre Nosotros'),
(10, 'menu_about', 'gl', 'Sobre Nós'),
(11, 'footer_description', 'es', 'Radio Morrazo es tu emisora local comprometida con la comunidad y la cultura de la región de Morrazo.'),
(12, 'footer_description', 'gl', 'Radio Morrazo é a túa emisora local comprometida coa comunidade e a cultura da rexión do Morrazo.'),
(13, 'footer_contact', 'es', 'Contacto'),
(14, 'footer_contact', 'gl', 'Contacto'),
(15, 'footer_follow', 'es', 'Síguenos'),
(16, 'footer_follow', 'gl', 'Síguenos'),
(17, 'footer_admin', 'es', 'Administración'),
(18, 'footer_admin', 'gl', 'Administración'),
(19, 'footer_rights', 'es', 'Todos los derechos reservados.'),
(20, 'footer_rights', 'gl', 'Todos os dereitos reservados.'),
(21, 'footer_privacy', 'es', 'Política de Privacidad'),
(22, 'footer_privacy', 'gl', 'Política de Privacidade'),
(23, 'footer_terms', 'es', 'Términos de Uso'),
(24, 'footer_terms', 'gl', 'Termos de Uso'),
(25, 'footer_cookies', 'es', 'Política de Cookies'),
(26, 'footer_cookies', 'gl', 'Política de Cookies');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rol` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre`, `email`, `rol`, `activo`, `fecha_creacion`, `ultima_actualizacion`) VALUES
(1, 'admin', '$2y$10$8MJKjDTKVJ5K1ZZkZ9HnAOkZmJJRJK6A8nxE7X9SYX9vYXJJmLjnK', 'Administrador', 'admin@radiomorrazo.com', 'admin', 1, '2025-08-04 01:27:27', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audiolibros`
--
ALTER TABLE `audiolibros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `categorias_audiolibro`
--
ALTER TABLE `categorias_audiolibro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_podcast`
--
ALTER TABLE `categorias_podcast`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comentarios_podcast`
--
ALTER TABLE `comentarios_podcast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `podcast_id` (`podcast_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `programa_id` (`programa_id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `traducciones`
--
ALTER TABLE `traducciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`,`idioma`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audiolibros`
--
ALTER TABLE `audiolibros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_audiolibro`
--
ALTER TABLE `categorias_audiolibro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_podcast`
--
ALTER TABLE `categorias_podcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios_podcast`
--
ALTER TABLE `comentarios_podcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `programas`
--
ALTER TABLE `programas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traducciones`
--
ALTER TABLE `traducciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `audiolibros`
--
ALTER TABLE `audiolibros`
  ADD CONSTRAINT `audiolibros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_audiolibro` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audiolibros_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `comentarios_podcast`
--
ALTER TABLE `comentarios_podcast`
  ADD CONSTRAINT `comentarios_podcast_ibfk_1` FOREIGN KEY (`podcast_id`) REFERENCES `podcasts` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  ADD CONSTRAINT `logs_acceso_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD CONSTRAINT `podcasts_ibfk_1` FOREIGN KEY (`programa_id`) REFERENCES `programas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `podcasts_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_podcast` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `podcasts_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `programas`
--
ALTER TABLE `programas`
  ADD CONSTRAINT `programas_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
