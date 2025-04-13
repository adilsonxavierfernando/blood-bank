-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01/03/2025 às 17:22
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `blood_place`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'Jelson Chituta', '$2y$10$.ZH.5Ak9xU13NZV677oPk.Kl.hzU1zMAAU3Pg612becJnwSXNGniu'),
(2, 'Administrador', '$2y$10$.ZH.5Ak9xU13NZV677oPk.Kl.hzU1zMAAU3Pg612becJnwSXNGniu');

-- --------------------------------------------------------

--
-- Estrutura para tabela `campanhas`
--

CREATE TABLE `campanhas` (
  `id` int(11) NOT NULL,
  `local` varchar(255) NOT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `tipo_sanguineo` varchar(10) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `campanhas`
--

INSERT INTO `campanhas` (`id`, `local`, `cidade`, `data_inicio`, `data_fim`, `tipo_sanguineo`, `descricao`) VALUES
(8, 'Hospital Geral do Moxico', 'Luena', '2025-02-27', '2025-04-12', 'A+', 'A doação de sangue é um ato voluntario, bondade e amor ao proximo.'),
(9, 'HGM', 'Luena', '2025-02-28', '2025-02-28', 'A+', 'Cu'),
(10, 'HGM', 'Luena', '2025-03-12', '2025-03-28', 'A+', 'Audewew');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contatos`
--

CREATE TABLE `contatos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contatos`
--

INSERT INTO `contatos` (`id`, `nome`, `email`, `mensagem`, `data_envio`) VALUES
(1, 'Furtunato Assunção Chindombe', 'shityboyhack@gmail.com', 'olá', '2025-02-27 16:03:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `depoimentos`
--

CREATE TABLE `depoimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `data_depoimento` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `depoimentos`
--

INSERT INTO `depoimentos` (`id`, `nome`, `mensagem`, `data_depoimento`, `data`) VALUES
(1, 'Selson', 'Atendimento Excelente', '2025-02-27 16:04:22', '2025-02-27 16:04:22');

-- --------------------------------------------------------

--
-- Estrutura para tabela `doacoes`
--

CREATE TABLE `doacoes` (
  `id` int(11) NOT NULL,
  `doador_id` int(11) DEFAULT NULL,
  `campanha_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `data_agendada` date DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','estoque','utilizado') NOT NULL DEFAULT 'pendente',
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `doacoes`
--

INSERT INTO `doacoes` (`id`, `doador_id`, `campanha_id`, `quantidade`, `data_agendada`, `data`, `status`, `valor`) VALUES
(67, 13, 8, 150, '2025-02-27', '2025-02-27 10:58:00', 'estoque', 0.00),
(68, 14, 8, 200, '2025-03-08', '2025-02-27 15:56:17', 'estoque', 0.00),
(69, 15, 8, 200, '2025-04-01', '2025-02-27 16:56:25', 'estoque', 0.00),
(70, 16, 9, 450, '2025-02-28', '2025-02-28 11:02:04', 'estoque', 0.00),
(76, 19, 10, 450, '2025-03-13', '2025-03-01 10:49:47', 'estoque', 0.00),
(77, 20, 10, 450, '2025-03-21', '2025-03-01 11:14:24', 'estoque', 0.00),
(78, 22, 10, 450, '2025-03-18', '2025-03-01 11:56:38', 'estoque', 0.00),
(79, 23, 10, 450, '2025-03-29', '2025-03-01 12:17:34', 'estoque', 0.00),
(82, 21, 10, 450, '2025-03-26', '2025-03-01 12:44:31', 'estoque', 0.00),
(84, 24, 10, 450, '2025-03-17', '2025-03-01 13:08:42', 'estoque', 0.00),
(85, 25, 10, 250, '2025-03-29', '2025-03-01 13:10:24', 'estoque', 0.00),
(86, 26, 10, 450, '2025-03-29', '2025-03-01 13:21:51', 'estoque', 0.00),
(87, 27, 10, 450, '2025-03-29', '2025-03-01 14:39:58', 'pendente', 0.00),
(88, 28, 10, 450, '2025-03-01', '2025-03-01 14:55:46', 'pendente', 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `doadores`
--

CREATE TABLE `doadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `idade` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_doacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `doadores`
--

INSERT INTO `doadores` (`id`, `nome`, `idade`, `email`, `foto_perfil`, `telefone`, `data_doacao`) VALUES
(13, 'Jelson Eduardo Manuel Chituta', 19, 'eduardogellson2@gmail.com', NULL, '933889652', NULL),
(14, 'Furtunato Assunção Chindombe', 2025, 'shityboyhack@gmail.com', NULL, '947026838', NULL),
(15, 'Zezito', 24, 'zezito@teste.com', NULL, '123456789', NULL),
(18, 'Lucas Manuel', 18, 'lucas@gmail.com', NULL, '99942365', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `doacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_entrada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `doacao_id`, `quantidade`, `data_entrada`) VALUES
(1, 68, 200, '2025-02-27 16:08:28'),
(2, 68, 200, '2025-03-01 14:17:06'),
(3, 70, 450, '2025-03-01 14:17:08'),
(4, 76, 450, '2025-03-01 14:17:09'),
(5, 77, 450, '2025-03-01 14:17:10'),
(6, 78, 450, '2025-03-01 14:17:11'),
(7, 79, 450, '2025-03-01 14:17:12'),
(8, 82, 450, '2025-03-01 14:17:13'),
(9, 84, 450, '2025-03-01 14:17:14'),
(10, 85, 250, '2025-03-01 14:17:15'),
(11, 86, 450, '2025-03-01 14:17:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `to_user_id` int(11) NOT NULL,
  `type` enum('like','comment','friend_request','message') NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notifications`
--

INSERT INTO `notifications` (`id`, `from_user_id`, `to_user_id`, `type`, `post_id`, `message`, `is_read`, `created_at`) VALUES
(1, NULL, 13, '', NULL, 'O estoque de sangue do tipo B+ está baixo ou esgotado.', 1, '2025-02-27 10:51:46'),
(2, NULL, 14, '', NULL, 'O estoque de sangue do tipo A+ está baixo ou esgotado.', 1, '2025-02-27 16:02:17'),
(3, NULL, 15, '', NULL, 'O estoque de sangue do tipo O- está baixo ou esgotado.', 0, '2025-02-28 11:52:42'),
(4, NULL, 13, '', NULL, 'O estoque de sangue do tipo B+ está baixo ou esgotado.', 0, '2025-03-01 09:47:29'),
(5, NULL, 19, '', NULL, 'O estoque de sangue do tipo AB+ está baixo ou esgotado.', 0, '2025-03-01 11:24:49'),
(6, NULL, 28, '', NULL, 'O estoque de sangue do tipo A- está baixo ou esgotado.', 0, '2025-03-01 15:28:32'),
(7, NULL, 24, '', NULL, 'O estoque de sangue do tipo AB- está baixo ou esgotado.', 0, '2025-03-01 15:28:32'),
(8, NULL, 27, '', NULL, 'O estoque de sangue do tipo O+ está baixo ou esgotado.', 0, '2025-03-01 15:28:32'),
(9, NULL, 15, '', NULL, 'O estoque de sangue do tipo O- está baixo ou esgotado.', 0, '2025-03-01 15:28:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `participantes`
--

CREATE TABLE `participantes` (
  `participante_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `campanha_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `tipo_sangue` varchar(5) NOT NULL,
  `tipo_doacao` varchar(50) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `patologia` varchar(255) DEFAULT NULL,
  `setor` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `nome`, `email`, `telefone`, `data`, `horario`, `tipo_sangue`, `tipo_doacao`, `observacoes`, `created_at`, `patologia`, `setor`, `status`) VALUES
(1, 'Jelson Eduardo Manuel Chituta', 'eduardogellson2@gmail.com', '234232434', '2025-02-28', '08:00:00', 'B+', 'Sangue Total', '3243r44etrfe', '2025-02-27 16:45:54', NULL, '', 'pendente'),
(2, 'Gelson Manuell', 'gelson@gmail.com', '946669396', '2025-04-02', '08:00:00', 'A+', 'Sangue Total', 'aaa', '2025-03-01 15:10:17', NULL, '', 'completado'),
(3, 'Gelson Manuell', 'gelson@gmail.com', '946669396', '2025-04-02', '08:00:00', 'A+', 'Sangue Total', 'aaa', '2025-03-01 15:10:22', NULL, '', 'cancelado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `retiradas`
--

CREATE TABLE `retiradas` (
  `id` int(11) NOT NULL,
  `doacao_id` int(11) NOT NULL,
  `paciente` varchar(255) DEFAULT NULL,
  `quantidade_retirada` int(11) NOT NULL,
  `tecnico` varchar(100) NOT NULL,
  `hospital` varchar(100) NOT NULL,
  `data_retirada` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `retiradas`
--

INSERT INTO `retiradas` (`id`, `doacao_id`, `paciente`, `quantidade_retirada`, `tecnico`, `hospital`, `data_retirada`, `observacoes`, `data_registro`, `setor`) VALUES
(1, 67, 'Ana Maria', 50, 'Jelson Chituta', 'Sandra Diamond', '2025-02-27', 'Urgente paciente em estado de anemia grave!', '2025-02-27 11:04:38', 'Emergência'),
(2, 67, 'Ana Maria', 50, 'Jelson Chituta', 'Sandra Diamond', '2025-02-27', 'Urgente paciente em estado de anemia grave!', '2025-02-27 11:04:38', 'Emergência'),
(3, 67, 'Selson', 100, 'Administrador', 'Maternidade', '2025-02-27', '', '2025-02-27 16:16:22', 'UTI'),
(4, 67, 'Selson', 100, 'Administrador', 'Maternidade', '2025-02-27', '', '2025-02-27 16:16:26', 'UTI');

-- --------------------------------------------------------

--
-- Estrutura para tabela `slider_images`
--

CREATE TABLE `slider_images` (
  `id` int(11) NOT NULL,
  `caminho_imagem` varchar(255) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `slider_images`
--

INSERT INTO `slider_images` (`id`, `caminho_imagem`, `titulo`, `descricao`) VALUES
(1, 'uploads/R (6).jfif', 'Doar para saval vidas!', 'Um ato Solitario e de bondade.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `blood_type` varchar(3) NOT NULL,
  `city` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT 'default_profile.jpg',
  `cover_picture` varchar(255) DEFAULT 'default_cover.jpg',
  `status` enum('active','inactive') DEFAULT 'active',
  `gender` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `birthdate`, `blood_type`, `city`, `created_at`, `profile_picture`, `cover_picture`, `status`, `gender`) VALUES
(13, 'Jelson Eduardo Manuel Chituta', 'eduardogellson2@gmail.com', '$2y$10$S3aV1lcYxSfT1fV4HLlb0.iHnpmxJNDiTzzM4gPQn3oBcTjTkAHme', '2005-09-25', 'B+', 'Luena', '2025-02-27 10:50:00', 'uploads/profile_pictures/67c1910446d36_OIP (49).jpeg', 'default_cover.jpg', 'active', ''),
(14, 'Furtunato Assunção Chindombe', 'furtunatoa.chindombe@gmail.com', '$2y$10$L/Jlr2R7n3KkydqwrMiNUuSRcoltVmpRAk4b/2JJlOoTuJ0sYVPau', '2025-12-11', 'A+', 'Luena', '2025-02-27 15:52:54', 'uploads/profile_pictures/67c08c14911d1_WIN_20240614_18_22_36_Pro.jpg', 'default_cover.jpg', 'active', ''),
(15, 'caqueiaSpy', 'zezito@teste.com', '$2y$10$uOJZXB.VsC6h93f1YvcdEeqcMHRU56fHy/UTK0uxxexoaACr/CttG', '2000-12-12', 'O-', 'Alto Zambeze', '2025-02-27 16:51:53', 'default_profile.jpg', 'default_cover.jpg', 'active', ''),
(16, 'Gelson Manuell', 'gelson@gmail.com', '$2y$10$9/PmMJnVBNtio.RaubCUpOyjZHkO8zYstJb0Ej55Yhjkt4ayJxJU6', '2007-02-23', 'A+', 'Luena', '2025-02-28 10:04:20', 'uploads/profile_pictures/67c3232996fa1_logo.png', 'default_cover.jpg', 'active', 'Masculino'),
(17, 'Gelson Manuel', 'gelsson@gmail.com', '$2y$10$vHakCUIx1AkNAiLgZOE2GePsYe8Ycpi0DbAQ86g.zPCFamWs2mI..', '2005-06-01', 'A+', 'Luena', '2025-02-28 10:04:45', 'uploads/profile_pictures/67c18bf34b8f8_logo.png', 'default_cover.jpg', 'active', ''),
(18, 'Luas Manuel', 'lucas@gmail.com', '$2y$10$8AzEDV.DXNGAh1RbCbbCm.HPSHL8M9bW.DUN9QRYOhij1qVUXJINW', '2007-02-21', '', 'Luena', '2025-03-01 09:47:12', 'uploads/profile_pictures/67c2e0351d3e7_Captura de tela 2025-02-26 114508.png', 'default_cover.jpg', 'active', ''),
(19, 'Ana Kina', 'kina@gmail.com', '$2y$10$RoJUbarBq665asH2inRKCOE16OH1/gdWhuxYdofajc6vhhKp1mdaa', '2004-03-02', 'AB+', 'Luena', '2025-03-01 10:48:48', 'default_profile.jpg', 'default_cover.jpg', 'active', ''),
(20, 'Analtina Maria', 'maria@gmail.com', '$2y$10$7fxTK/6TkqYUh9OVabos2eOo4TJrSPCbbMXGG3hGZw4GnznRoC78y', '1966-07-24', 'A+', 'Luena', '2025-03-01 11:08:20', 'uploads/profile_pictures/67c2ebf01cfc8_Captura de tela 2025-02-26 114508.png', 'default_cover.jpg', 'active', ''),
(21, 'Ana Carolina', 'caro@gmail.com', '$2y$10$LmZoH1x4oR7LK92RfgXq3ure2.MDrifIMhg5pu1UN13hPOAV48h56', '2004-02-03', 'A+', 'Luena', '2025-03-01 11:15:25', 'default_profile.jpg', 'default_cover.jpg', 'active', ''),
(22, 'Braulio Cachongo', 'braulio@gmail.com', '$2y$10$K7xcHadbOU2uTnu4Qh9ZLuXMIHFEPoN6NzqB2HjoDG39LIR6euO22', '2007-02-20', 'A+', 'luanda', '2025-03-01 11:48:33', 'default_profile.jpg', 'default_cover.jpg', 'active', ''),
(23, 'Galileu Drillers', 'galileu@gmail.com', '$2y$10$ixk3e9PGq4ZS4/h8nevyMOXpupft9P0ZKC7qYGjgYExKJOFNITZ8y', '2000-03-03', 'A+', 'Luena', '2025-03-01 11:58:14', 'uploads/profile_pictures/67c2fa6b0595b_IMG-20250225-WA0005.jpg', 'default_cover.jpg', 'active', ''),
(24, 'Noémia Glorisvalda', 'noemia@gmail.com', '$2y$10$V9ltZWTGkz0nw.bZkiquoes1pX/cR9lDbdxM2AoeoLIG4tqDsFnDe', '2007-02-16', 'AB-', 'Luena', '2025-03-01 13:04:45', 'uploads/profile_pictures/67c3061f83eec_Mixtape cover _1739698332696.jpg', 'default_cover.jpg', 'active', ''),
(25, 'Alice yenga Manuel', 'aliceyengamanuel@gmail.com', '$2y$10$2Ac49YeMSrxKVnmT8PImzu2xpftWkJODBCjDNYN53796qMUmGzPai', '2007-02-16', 'A+', 'Luena', '2025-03-01 13:09:07', 'uploads/profile_pictures/67c30713a75a9_Mixtape cover _1739698332696.jpg', 'default_cover.jpg', 'active', ''),
(26, 'Onessimo Chituta', 'onessimograca69@gmail.com', '$2y$10$WzFS86JM5gD5bcJI6xEM8uP2NO1LHHS7kKUSlcvUWBiP1gIKwkZnO', '2007-02-13', 'A+', 'Luena', '2025-03-01 13:16:02', 'uploads/profile_pictures/67c309d5591ce_Snapchat-340866625.jpg', 'default_cover.jpg', 'active', ''),
(27, 'Zebedeu Chingombo', 'zebedeucastro@gmail.com', '$2y$10$mFJ4yKLXKW2C3p5WtineceIbH4rbPq1Itj8X7ykOMQmwARJsMqtZC', '2006-12-15', 'O+', 'Luena', '2025-03-01 14:37:31', 'uploads/profile_pictures/67c31c008d7b4_IMG-20250228-WA0003.jpg', 'default_cover.jpg', 'active', ''),
(28, 'Tania Lúcia', 'tania@gamil.com', '$2y$10$28v1J01xYyudeDbWYNcDjuY7VpNAsMfKrQFOH6u18Vl0QmLTgeupm', '2007-03-01', 'A-', 'Luena', '2025-03-01 14:48:03', 'default_profile.jpg', 'default_cover.jpg', 'active', 'femenino');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `campanhas`
--
ALTER TABLE `campanhas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `doacoes`
--
ALTER TABLE `doacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doador_id` (`doador_id`),
  ADD KEY `campanha_id` (`campanha_id`);

--
-- Índices de tabela `doadores`
--
ALTER TABLE `doadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doacao_id` (`doacao_id`);

--
-- Índices de tabela `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `to_user_id` (`to_user_id`);

--
-- Índices de tabela `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`participante_id`),
  ADD KEY `campanha_id` (`campanha_id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `retiradas`
--
ALTER TABLE `retiradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doacao_id` (`doacao_id`);

--
-- Índices de tabela `slider_images`
--
ALTER TABLE `slider_images`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `campanhas`
--
ALTER TABLE `campanhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `doacoes`
--
ALTER TABLE `doacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de tabela `doadores`
--
ALTER TABLE `doadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `participantes`
--
ALTER TABLE `participantes`
  MODIFY `participante_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `retiradas`
--
ALTER TABLE `retiradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `slider_images`
--
ALTER TABLE `slider_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `doacoes`
--
ALTER TABLE `doacoes`
  ADD CONSTRAINT `doacoes_ibfk_1` FOREIGN KEY (`doador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `doacoes_ibfk_2` FOREIGN KEY (`campanha_id`) REFERENCES `campanhas` (`id`);

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`doacao_id`) REFERENCES `doacoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `participantes_ibfk_1` FOREIGN KEY (`campanha_id`) REFERENCES `campanhas` (`id`);

--
-- Restrições para tabelas `retiradas`
--
ALTER TABLE `retiradas`
  ADD CONSTRAINT `retiradas_ibfk_1` FOREIGN KEY (`doacao_id`) REFERENCES `doacoes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
