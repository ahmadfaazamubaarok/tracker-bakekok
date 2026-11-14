SET NAMES utf8mb4;

CREATE TABLE `project_groups` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_groups_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `projects` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id`       INT UNSIGNED NOT NULL,
  `name`           VARCHAR(150) NOT NULL,
  `description`    TEXT NULL,
  `domain`         VARCHAR(255) NOT NULL,
  `directory_path` VARCHAR(255) NOT NULL,
  `color`          CHAR(7) NOT NULL DEFAULT '#3B82F6',
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projects_group` (`group_id`),
  CONSTRAINT `fk_projects_group`
    FOREIGN KEY (`group_id`) REFERENCES `project_groups` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `progresses` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`  INT UNSIGNED NOT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `notes`       TEXT NULL,
  `deadline`    DATE NOT NULL,
  `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `status`      ENUM('pending','wip','done') NOT NULL DEFAULT 'pending',
  `position`    INT UNSIGNED NOT NULL DEFAULT 0,
  `archived`    TINYINT(1) NOT NULL DEFAULT 0,
  `archived_at` DATETIME NULL DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_progresses_project` (`project_id`),
  KEY `idx_progresses_board` (`archived`, `status`, `position`),
  CONSTRAINT `fk_progresses_project`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_progresses` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `progress_id` INT UNSIGNED NOT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `status`      ENUM('pending','wip','done') NOT NULL DEFAULT 'pending',
  `position`    INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_progresses_progress` (`progress_id`),
  CONSTRAINT `fk_sub_progresses_progress`
    FOREIGN KEY (`progress_id`) REFERENCES `progresses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
