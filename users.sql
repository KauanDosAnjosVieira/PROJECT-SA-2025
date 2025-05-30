-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/05/2025 às 20:32
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
-- Banco de dados: `licithub`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `stripe_id` varchar(255) DEFAULT NULL COMMENT 'ID no Stripe',
  `pm_type` varchar(255) DEFAULT NULL COMMENT 'Tipo de pagamento',
  `pm_last_four` varchar(4) DEFAULT NULL COMMENT 'Últimos 4 dígitos',
  `trial_ends_at` timestamp NULL DEFAULT NULL COMMENT 'Fim do período de trial',
  `user_type` enum('customer','employee','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`, `user_type`, `created_at`, `updated_at`, `phone`, `reset_token`, `reset_token_expires`) VALUES
(1, 'João Silva', 'joao@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(2, 'Maria Souza', 'maria@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(3, 'Carlos Oliveira', 'carlos@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(4, 'Ana Santos', 'ana@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:56:46', '', NULL, NULL),
(5, 'Pedro Costa', 'pedro@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(6, 'Lucia Pereira', 'lucia@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(7, 'Marcos Rocha', 'marcos@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(8, 'Fernanda Lima', 'fernanda@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(9, 'Admin', 'admin@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(10, 'Suporte', 'suporte@exemplo.com', '2025-05-19 16:45:31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'employee', '2025-05-19 16:45:31', '2025-05-19 16:45:31', NULL, NULL, NULL),
(11, 'Etelvino Vinicius Vitor', 'etelvinovitor@gmail.com', NULL, '$2y$10$AyoNZ2nL11joFh8S6RIXueOYp95nnYFIkGbC05dR7v/UMEvyONYW2', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-05-19 16:52:26', '2025-05-19 16:52:26', NULL, '697089ea0070ccc79243fcf5c5f4632cd18854a6b347e91e17705e1d76fac615', '2025-05-19 22:30:00'),
(12, 'a', 'wda@a', NULL, '', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 18:24:05', '2025-05-19 18:24:05', '(12) 131313131313131', NULL, NULL),
(13, 'Kauan', 'kauan_a_vieira@estudante.sesisenai.org.br', NULL, '$2y$10$n53Y4NNLHbr9zsHewDpxLO1RoaII6veloZE1tFY0qq8UW/N/91PTG', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-23 17:51:31', '2025-05-23 17:51:31', NULL, NULL, NULL),
(14, 'Kauan Vieira', 'kauaanvieiraa07@gmail.com', NULL, '$2y$10$KCxJkaDPVa7U4aPwTJQiWuEGYkKDjmttWE5dJhjn4NLcU.WuZgLG6', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 16:55:23', '2025-05-30 16:55:23', NULL, NULL, NULL),
(15, 'Etelvino', 'etelvino070@gmail.com', NULL, '$2y$10$lEb0yhFvLBRI86fJQR4bHu5WXkEXVhrU6pfFjLZwsd0wgncNqxW8e', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 17:01:53', '2025-05-30 17:01:53', NULL, '4ab587b029adace9065a8f8fc43509029a3981a827cd1ee0965e75c045634d98', '2025-05-30 20:01:58'),
(16, 'Admin', 'admin@admin.com', NULL, '$2y$10$G5gTTgIZd3Thz2769yD9xu4wNTUyNBbB.qFIDi1veu41gmWkvUEoG', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-05-30 17:28:24', '2025-05-30 17:28:24', NULL, NULL, NULL),
(17, 'Neon', 'neon_bruehmueller@estudante.sesisenai.org.br', NULL, '$2y$10$i1FlTAlHScHrxZUNLWCxZ.4CGY8gituq0Kys2M8f5Cv2srlsUapKK', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 18:27:22', '2025-05-30 18:27:22', NULL, 'f317d589614efec57f131c85b89b81f6eb281bcdf4057067609ea605edc7b0a6', '2025-05-30 21:27:45'),
(18, 'Endryo', 'endryo_bittencourt@gmail.com', NULL, '$2y$10$.o21nlR3HR22rhFPeFbme.1iOvZvjCXf28mvAVPqJVRpqFt0.LiPC', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 18:28:06', '2025-05-30 18:28:06', NULL, NULL, NULL),
(19, 'Endryoo', 'endryo_bittencourt@estudante.sesisenai.org.br', NULL, '$2y$10$Agc9YhUmxjPGfqhA.UjfaebUaFlk1bkyXVTufYmxr67MXt4ukPrla', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 18:28:26', '2025-05-30 18:28:26', NULL, 'a4d55eab5588d7cf912fdcb38c3aeba5dc638b2bf0add264e65b511e62106a04', '2025-05-30 21:28:34'),
(20, 'Tiago', 'tiago_vasconcelos@estudante.sesisenai.org.br', NULL, '$2y$10$UcV4epWREhdrUHBUgHsx9.SfPnAz2Fwsk1wIrWz2nKJJ.N/yiikky', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-30 18:29:00', '2025-05-30 18:29:00', NULL, '50342eb6aaaeb1fbac9ee3f15d5927d0f5672fd65fc159045ac2f56f4559fbea', '2025-05-30 21:29:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_stripe_id_index` (`stripe_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
