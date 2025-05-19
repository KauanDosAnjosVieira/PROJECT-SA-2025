create database licithub;
use licithub;

-- Tabela de usuários (users)

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de planos
-- --------------------------------------------------------
CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `trial_days` int(11) NOT NULL DEFAULT 0,
  `interval` varchar(255) NOT NULL COMMENT 'monthly, yearly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `features` json DEFAULT NULL COMMENT 'Recursos do plano em JSON',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de assinaturas
-- --------------------------------------------------------
CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'active, canceled, expired, trial',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `starts_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ends_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `gateway` varchar(255) NOT NULL COMMENT 'stripe, paypal, etc',
  `gateway_id` varchar(255) NOT NULL COMMENT 'ID no gateway de pagamento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_status_index` (`status`),
  KEY `subscriptions_ends_at_index` (`ends_at`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de pagamentos
-- --------------------------------------------------------
CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `gateway` varchar(255) NOT NULL,
  `gateway_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'paid, pending, failed, refunded',
  `paid_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL COMMENT 'Dados adicionais',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_subscription_id_foreign` (`subscription_id`),
  KEY `payments_status_index` (`status`),
  KEY `payments_paid_at_index` (`paid_at`),
  CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de cargos/funções (para funcionários)
-- --------------------------------------------------------
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Ex: admin, manager, support',
  `permissions` json DEFAULT NULL COMMENT 'Permissões em JSON',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de relação usuário-cargos
-- --------------------------------------------------------
CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de senhas resetadas
-- --------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de sessões
-- --------------------------------------------------------
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT,
  status ENUM('pendente', 'respondido', 'cancelado') DEFAULT 'pendente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN phone VARCHAR(20);



-- --------------------------------------------------------
-- INSERTS DE EXEMPLO
-- --------------------------------------------------------

-- Inserção de planos (3 planos)
INSERT INTO `plans` (`name`, `slug`, `description`, `price`, `trial_days`, `interval`, `is_active`, `features`, `created_at`, `updated_at`) VALUES
('Básico', 'basico', 'Plano básico com acesso limitado', 29.90, 7, 'monthly', 1, '{"telas_simultaneas": 1, "qualidade": "HD", "conteudos": ["filmes", "series"]}', NOW(), NOW()),
('Padrão', 'padrao', 'Plano com recursos intermediários', 49.90, 14, 'monthly', 1, '{"telas_simultaneas": 2, "qualidade": "FullHD", "conteudos": ["filmes", "series", "documentarios"]}', NOW(), NOW()),
('Premium', 'premium', 'Plano completo com todos os recursos', 99.90, 30, 'monthly', 1, '{"telas_simultaneas": 4, "qualidade": "4K", "conteudos": ["filmes", "series", "documentarios", "infantis"]}', NOW(), NOW());

-- Inserção de usuários (10 usuários)
INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `user_type`, `created_at`, `updated_at`) VALUES
('João Silva', 'joao@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Maria Souza', 'maria@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Carlos Oliveira', 'carlos@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Ana Santos', 'ana@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Pedro Costa', 'pedro@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Lucia Pereira', 'lucia@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Marcos Rocha', 'marcos@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Fernanda Lima', 'fernanda@exemplo.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
('Admin', 'admin@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('Suporte', 'suporte@exemplo.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

-- Inserção de assinaturas (10 assinaturas)
INSERT INTO `subscriptions` (`user_id`, `plan_id`, `status`, `trial_ends_at`, `starts_at`, `ends_at`, `canceled_at`, `gateway`, `gateway_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'active', NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_123456789', NOW(), NOW()),
(2, 2, 'active', NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_987654321', NOW(), NOW()),
(3, 3, 'active', NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_456789123', NOW(), NOW()),
(4, 1, 'active', NULL, DATE_SUB(NOW(), INTERVAL 2 MONTH), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_321654987', NOW(), NOW()),
(5, 2, 'canceled', NULL, DATE_SUB(NOW(), INTERVAL 3 MONTH), DATE_SUB(NOW(), INTERVAL 1 MONTH), DATE_SUB(NOW(), INTERVAL 1 MONTH), 'stripe', 'sub_654987321', NOW(), NOW()),
(6, 3, 'canceled', NULL, DATE_SUB(NOW(), INTERVAL 4 MONTH), DATE_SUB(NOW(), INTERVAL 2 MONTH), DATE_SUB(NOW(), INTERVAL 2 MONTH), 'stripe', 'sub_789123456', NOW(), NOW()),
(7, 1, 'active', NULL, DATE_SUB(NOW(), INTERVAL 1 MONTH), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_159753486', NOW(), NOW()),
(8, 2, 'trial', DATE_ADD(NOW(), INTERVAL 14 DAY), NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), NULL, 'stripe', 'sub_357159486', NOW(), NOW()),
(4, 3, 'active', NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_852741963', NOW(), NOW()),
(1, 2, 'active', NULL, DATE_SUB(NOW(), INTERVAL 6 MONTH), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, 'stripe', 'sub_963852741', NOW(), NOW());

-- Inserção de pagamentos (10 pagamentos)
INSERT INTO `payments` (`user_id`, `subscription_id`, `amount`, `currency`, `gateway`, `gateway_id`, `status`, `paid_at`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 29.90, 'BRL', 'stripe', 'pi_123456789', 'paid', NOW(), '{"method": "credit_card", "last4": "4242"}', NOW(), NOW()),
(2, 2, 49.90, 'BRL', 'stripe', 'pi_987654321', 'paid', NOW(), '{"method": "credit_card", "last4": "5555"}', NOW(), NOW()),
(3, 3, 99.90, 'BRL', 'stripe', 'pi_456789123', 'paid', NOW(), '{"method": "credit_card", "last4": "1111"}', NOW(), NOW()),
(4, 4, 29.90, 'BRL', 'stripe', 'pi_321654987', 'paid', DATE_SUB(NOW(), INTERVAL 1 MONTH), '{"method": "pix"}', NOW(), NOW()),
(4, 4, 29.90, 'BRL', 'stripe', 'pi_654123987', 'paid', NOW(), '{"method": "pix"}', NOW(), NOW()),
(5, 5, 49.90, 'BRL', 'stripe', 'pi_987321654', 'paid', DATE_SUB(NOW(), INTERVAL 2 MONTH), '{"method": "credit_card", "last4": "2222"}', NOW(), NOW()),
(6, 6, 99.90, 'BRL', 'stripe', 'pi_123789456', 'paid', DATE_SUB(NOW(), INTERVAL 3 MONTH), '{"method": "credit_card", "last4": "3333"}', NOW(), NOW()),
(7, 7, 29.90, 'BRL', 'stripe', 'pi_456123789', 'paid', DATE_SUB(NOW(), INTERVAL 1 MONTH), '{"method": "debit_card", "last4": "4444"}', NOW(), NOW()),
(4, 9, 99.90, 'BRL', 'stripe', 'pi_789456123', 'paid', NOW(), '{"method": "credit_card", "last4": "6666"}', NOW(), NOW()),
(1, 10, 49.90, 'BRL', 'stripe', 'pi_321789654', 'paid', NOW(), '{"method": "credit_card", "last4": "7777"}', NOW(), NOW());

-- Inserção de cargos/funções (2 cargos)
INSERT INTO `roles` (`name`, `permissions`, `created_at`, `updated_at`) VALUES
('admin', '{"all": true}', NOW(), NOW()),
('support', '{"users": ["view", "edit"], "subscriptions": ["view", "cancel"]}', NOW(), NOW());

-- Atribuição de cargos aos funcionários (2 atribuições)
INSERT INTO `user_roles` (`user_id`, `role_id`, `created_at`) VALUES
(9, 1, NOW()),  -- Admin tem role de admin
(10, 2, NOW()); -- Suporte tem role de support

ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN reset_token_expires DATETIME DEFAULT NULL;