ALTER TABLE `users` ADD COLUMN `status` ENUM('Aktif', 'Pending', 'Nonaktif') NOT NULL DEFAULT 'Aktif' AFTER `password`;
