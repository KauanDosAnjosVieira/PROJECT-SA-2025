-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/05/2025 às 21:44
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
-- Estrutura para tabela `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pendente','respondido','cancelado') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `gateway` varchar(255) NOT NULL,
  `gateway_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'paid, pending, failed, refunded',
  `paid_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados adicionais' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `subscription_id`, `amount`, `currency`, `gateway`, `gateway_id`, `status`, `paid_at`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 29.90, 'BRL', 'stripe', 'pi_123456789', 'paid', '2025-05-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"4242\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(2, 2, 2, 49.90, 'BRL', 'stripe', 'pi_987654321', 'paid', '2025-05-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"5555\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(3, 3, 3, 99.90, 'BRL', 'stripe', 'pi_456789123', 'paid', '2025-05-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"1111\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(4, 4, 4, 29.90, 'BRL', 'stripe', 'pi_321654987', 'paid', '2025-04-19 16:45:31', '{\"method\": \"pix\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(5, 4, 4, 29.90, 'BRL', 'stripe', 'pi_654123987', 'paid', '2025-05-19 16:45:31', '{\"method\": \"pix\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(6, 5, 5, 49.90, 'BRL', 'stripe', 'pi_987321654', 'paid', '2025-03-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"2222\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(7, 6, 6, 99.90, 'BRL', 'stripe', 'pi_123789456', 'paid', '2025-02-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"3333\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(8, 7, 7, 29.90, 'BRL', 'stripe', 'pi_456123789', 'paid', '2025-04-19 16:45:31', '{\"method\": \"debit_card\", \"last4\": \"4444\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(9, 4, 9, 99.90, 'BRL', 'stripe', 'pi_789456123', 'paid', '2025-05-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"6666\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(10, 1, 10, 49.90, 'BRL', 'stripe', 'pi_321789654', 'paid', '2025-05-19 16:45:31', '{\"method\": \"credit_card\", \"last4\": \"7777\"}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(11, 12, 11, 99.90, 'BRL', 'simulator', 'sim_682b774511cac', 'paid', '2025-05-19 18:24:05', NULL, '2025-05-19 18:24:05', '2025-05-19 18:24:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `trial_days` int(11) NOT NULL DEFAULT 0,
  `interval` varchar(255) NOT NULL COMMENT 'monthly, yearly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Recursos do plano em JSON' CHECK (json_valid(`features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `plans`
--

INSERT INTO `plans` (`id`, `name`, `slug`, `description`, `price`, `trial_days`, `interval`, `is_active`, `features`, `created_at`, `updated_at`) VALUES
(1, 'Básico', 'basico', 'Plano básico com acesso limitado', 29.90, 7, 'monthly', 1, '{\"telas_simultaneas\": 1, \"qualidade\": \"HD\", \"conteudos\": [\"filmes\", \"series\"]}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(2, 'Padrão', 'padrao', 'Plano com recursos intermediários', 49.90, 14, 'monthly', 1, '{\"telas_simultaneas\": 2, \"qualidade\": \"FullHD\", \"conteudos\": [\"filmes\", \"series\", \"documentarios\"]}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(3, 'Premium', 'premium', 'Plano completo com todos os recursos', 99.90, 30, 'monthly', 1, '{\"telas_simultaneas\": 4, \"qualidade\": \"4K\", \"conteudos\": [\"filmes\", \"series\", \"documentarios\", \"infantis\"]}', '2025-05-19 16:45:31', '2025-05-19 16:45:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Ex: admin, manager, support',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permissões em JSON' CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', '{\"all\": true}', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(2, 'support', '{\"users\": [\"view\", \"edit\"], \"subscriptions\": [\"view\", \"cancel\"]}', '2025-05-19 16:45:31', '2025-05-19 16:45:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'active, canceled, expired, trial',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `starts_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ends_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `canceled_at` timestamp NULL DEFAULT NULL,
  `gateway` varchar(255) NOT NULL COMMENT 'stripe, paypal, etc',
  `gateway_id` varchar(255) NOT NULL COMMENT 'ID no gateway de pagamento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_id`, `status`, `trial_ends_at`, `starts_at`, `ends_at`, `canceled_at`, `gateway`, `gateway_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', NULL, '2025-05-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_123456789', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(2, 2, 2, 'active', NULL, '2025-05-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_987654321', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(3, 3, 3, 'active', NULL, '2025-05-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_456789123', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(4, 4, 1, 'inativo', NULL, '2025-03-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_321654987', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(5, 5, 2, 'canceled', NULL, '2025-02-19 16:45:31', '2025-04-19 16:45:31', '2025-04-19 16:45:31', 'stripe', 'sub_654987321', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(6, 6, 3, 'canceled', NULL, '2025-01-19 16:45:31', '2025-03-19 16:45:31', '2025-03-19 16:45:31', 'stripe', 'sub_789123456', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(7, 7, 1, 'active', NULL, '2025-04-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_159753486', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(8, 8, 2, 'trial', '2025-06-02 16:45:31', '2025-05-19 16:45:31', '2025-06-02 16:45:31', NULL, 'stripe', 'sub_357159486', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(9, 4, 3, 'active', NULL, '2025-05-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_852741963', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(10, 1, 2, 'active', NULL, '2024-11-19 16:45:31', '2025-06-19 16:45:31', NULL, 'stripe', 'sub_963852741', '2025-05-19 16:45:31', '2025-05-19 16:45:31'),
(11, 12, 3, 'paid', '2025-06-18 23:24:05', '2025-05-19 18:24:05', '1970-01-01 04:00:00', NULL, 'simulator', 'sim_682b774511cac', '2025-05-19 18:24:05', '2025-05-19 18:24:05');

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
(12, 'a', 'wda@a', NULL, '', NULL, NULL, NULL, NULL, NULL, 'customer', '2025-05-19 18:24:05', '2025-05-19 18:24:05', '(12) 131313131313131', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `created_at`) VALUES
(9, 1, '2025-05-19 16:45:31'),
(10, 2, '2025-05-19 16:45:31');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_user_id_foreign` (`user_id`),
  ADD KEY `payments_subscription_id_foreign` (`subscription_id`),
  ADD KEY `payments_status_index` (`status`),
  ADD KEY `payments_paid_at_index` (`paid_at`);

--
-- Índices de tabela `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plans_slug_unique` (`slug`);

--
-- Índices de tabela `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_user_id_foreign` (`user_id`),
  ADD KEY `subscriptions_plan_id_foreign` (`plan_id`),
  ADD KEY `subscriptions_status_index` (`status`),
  ADD KEY `subscriptions_ends_at_index` (`ends_at`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_stripe_id_index` (`stripe_id`);

--
-- Índices de tabela `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
