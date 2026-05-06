-- Backup database parkir_db
-- Generated: 2026-05-04 02:45:36

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `backup_logs`;
CREATE TABLE `backup_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `kendaraans`;
CREATE TABLE `kendaraans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `plat_nomor` varchar(255) NOT NULL,
  `tipe` enum('motor','mobil') NOT NULL,
  `merk` varchar(255) NOT NULL,
  `warna` varchar(255) NOT NULL,
  `qr_code_hash` varchar(255) DEFAULT NULL,
  `qr_token` text DEFAULT NULL,
  `qr_expired_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kendaraans_plat_nomor_unique` (`plat_nomor`),
  KEY `kendaraans_user_id_foreign` (`user_id`),
  CONSTRAINT `kendaraans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kendaraans` (`id`, `user_id`, `plat_nomor`, `tipe`, `merk`, `warna`, `qr_code_hash`, `qr_token`, `qr_expired_at`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES ('1', '1', 'B 4170 XYZ', 'mobil', 'Honda', 'Putih', NULL, NULL, NULL, '1', NULL, NULL, '2026-05-03 09:21:17', '2026-05-03 09:21:17');
INSERT INTO `kendaraans` (`id`, `user_id`, `plat_nomor`, `tipe`, `merk`, `warna`, `qr_code_hash`, `qr_token`, `qr_expired_at`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES ('2', '1', 'B 9576 ABC', 'motor', 'Yamaha', 'Hitam', NULL, NULL, NULL, '1', NULL, NULL, '2026-05-03 09:21:18', '2026-05-03 09:21:18');

DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_aktivitas_user_id_foreign` (`user_id`),
  CONSTRAINT `log_aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `login_histories`;
CREATE TABLE `login_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2026_05_03_065858_create_roles_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_05_03_065902_create_permissions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_05_03_065904_create_kendaraans_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_05_03_065910_create_parkirs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_05_03_065913_create_log_aktivitas_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_05_03_065917_create_login_histories_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2026_05_03_065920_create_audit_logs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2026_05_03_065923_create_security_logs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2026_05_03_065928_create_backup_logs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('13', '2026_05_03_070147_create_settings_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('14', '2026_05_03_070501_create_role_permissions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('15', '2026_05_03_070625_add_fields_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('16', '2026_05_03_083715_create_personal_access_tokens_table', '2');

DROP TABLE IF EXISTS `parkirs`;
CREATE TABLE `parkirs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `petugas_id` bigint(20) unsigned DEFAULT NULL,
  `check_in` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `check_out` timestamp NULL DEFAULT NULL,
  `durasi` int(11) DEFAULT NULL,
  `status` enum('active','completed','violation') NOT NULL,
  `scan_device_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scan_device_info`)),
  `qr_data_hash` varchar(255) DEFAULT NULL,
  `duplicate_attempt` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parkirs_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `parkirs_user_id_foreign` (`user_id`),
  KEY `parkirs_petugas_id_foreign` (`petugas_id`),
  CONSTRAINT `parkirs_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parkirs_petugas_id_foreign` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parkirs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('1', 'user', 'create', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('2', 'user', 'read', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('3', 'user', 'update', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('4', 'user', 'delete', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('5', 'user', 'export', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('6', 'user', 'backup', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('7', 'user', 'restore', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('8', 'user', 'approve', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('9', 'vehicle', 'create', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('10', 'vehicle', 'read', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('11', 'vehicle', 'update', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('12', 'vehicle', 'delete', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('13', 'vehicle', 'export', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('14', 'vehicle', 'backup', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('15', 'vehicle', 'restore', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('16', 'vehicle', 'approve', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('17', 'parking', 'create', NULL, '2026-05-03 08:12:24', '2026-05-03 08:12:24');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('18', 'parking', 'read', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('19', 'parking', 'update', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('20', 'parking', 'delete', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('21', 'parking', 'export', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('22', 'parking', 'backup', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('23', 'parking', 'restore', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('24', 'parking', 'approve', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('25', 'report', 'create', NULL, '2026-05-03 08:12:25', '2026-05-03 08:12:25');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('26', 'report', 'read', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('27', 'report', 'update', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('28', 'report', 'delete', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('29', 'report', 'export', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('30', 'report', 'backup', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('31', 'report', 'restore', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('32', 'report', 'approve', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('33', 'backup', 'create', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('34', 'backup', 'read', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('35', 'backup', 'update', NULL, '2026-05-03 08:12:26', '2026-05-03 08:12:26');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('36', 'backup', 'delete', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('37', 'backup', 'export', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('38', 'backup', 'backup', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('39', 'backup', 'restore', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('40', 'backup', 'approve', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('41', 'setting', 'create', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('42', 'setting', 'read', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('43', 'setting', 'update', NULL, '2026-05-03 08:12:27', '2026-05-03 08:12:27');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('44', 'setting', 'delete', NULL, '2026-05-03 08:12:28', '2026-05-03 08:12:28');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('45', 'setting', 'export', NULL, '2026-05-03 08:12:28', '2026-05-03 08:12:28');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('46', 'setting', 'backup', NULL, '2026-05-03 08:12:28', '2026-05-03 08:12:28');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('47', 'setting', 'restore', NULL, '2026-05-03 08:12:29', '2026-05-03 08:12:29');
INSERT INTO `permissions` (`id`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES ('48', 'setting', 'approve', NULL, '2026-05-03 08:12:29', '2026-05-03 08:12:29');

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '26');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '27');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '28');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '29');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '30');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '31');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '32');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '33');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '34');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '35');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '36');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '37');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '38');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '39');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '40');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '41');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '42');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '43');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '44');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '45');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '46');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '47');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '48');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '26');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '27');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '28');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '29');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '10');

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('1', 'Super Admin', 'superadmin', NULL, '2026-05-03 08:12:22', '2026-05-03 08:12:22');
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('2', 'Admin', 'admin', NULL, '2026-05-03 08:12:22', '2026-05-03 08:12:22');
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('3', 'Petugas', 'petugas', NULL, '2026-05-03 08:12:22', '2026-05-03 08:12:22');
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('4', 'Mahasiswa', 'mahasiswa', NULL, '2026-05-03 08:12:23', '2026-05-03 08:12:23');

DROP TABLE IF EXISTS `security_logs`;
CREATE TABLE `security_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `severity` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `security_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `security_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `nim` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nim_unique` (`nim`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `active`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`, `phone`, `nim`, `avatar`, `last_login_at`, `last_login_ip`, `login_attempts`, `locked_until`, `created_by`, `updated_by`, `deleted_at`) VALUES ('1', 'Super Admin', 'superadmin@kampus.ac.id', NULL, '1', '$2y$12$sZt9eEnlUSFDwOsR.foByuruOh96XL9rj0dmSRlql.jKG2r1cjcaa', 'xpbtely5u3uo5MTmf1PlA9KnOqSnBG3IzLTHn30ZaNvA00bZ47vL6V68E8Nn', '2026-05-03 08:12:39', '2026-05-03 08:12:39', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS=1;
