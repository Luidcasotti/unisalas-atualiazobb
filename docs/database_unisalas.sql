-- Script consolidado do banco de dados do projeto UniSalas
-- Banco: reserva_salas
-- SGBD recomendado: MySQL 8+ ou MariaDB 10.4+
-- Senha dos usuarios de teste: 12345678

SET NAMES utf8mb4;
SET time_zone = '-03:00';

CREATE DATABASE IF NOT EXISTS reserva_salas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE reserva_salas;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notificacao_visualizadas;
DROP TABLE IF EXISTS mensagens_diretas;
DROP TABLE IF EXISTS avisos;
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS salas;
DROP TABLE IF EXISTS blocos;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  tipo VARCHAR(255) NOT NULL DEFAULT 'professor',
  telefone VARCHAR(255) NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL,
  PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
  id VARCHAR(255) NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload LONGTEXT NOT NULL,
  last_activity INT NOT NULL,
  PRIMARY KEY (id),
  KEY sessions_user_id_index (user_id),
  KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache (
  `key` VARCHAR(255) NOT NULL,
  value MEDIUMTEXT NOT NULL,
  expiration INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY cache_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
  `key` VARCHAR(255) NOT NULL,
  owner VARCHAR(255) NOT NULL,
  expiration INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY cache_locks_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  queue VARCHAR(255) NOT NULL,
  payload LONGTEXT NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL,
  reserved_at INT UNSIGNED NULL,
  available_at INT UNSIGNED NOT NULL,
  created_at INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_batches (
  id VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  total_jobs INT NOT NULL,
  pending_jobs INT NOT NULL,
  failed_jobs INT NOT NULL,
  failed_job_ids LONGTEXT NOT NULL,
  options MEDIUMTEXT NULL,
  cancelled_at INT NULL,
  created_at INT NOT NULL,
  finished_at INT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid VARCHAR(255) NOT NULL,
  connection TEXT NOT NULL,
  queue TEXT NOT NULL,
  payload LONGTEXT NOT NULL,
  exception LONGTEXT NOT NULL,
  failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY failed_jobs_uuid_unique (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blocos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  cor VARCHAR(255) NOT NULL DEFAULT '#7010a8',
  manutencao_ativa TINYINT(1) NOT NULL DEFAULT 0,
  manutencao_fim DATE NULL,
  manutencao_indeterminada TINYINT(1) NOT NULL DEFAULT 0,
  manutencao_aviso TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE salas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  capacidade INT NULL,
  observacao TEXT NULL,
  bloco_id BIGINT UNSIGNED NOT NULL,
  manutencao_ativa TINYINT(1) NOT NULL DEFAULT 0,
  manutencao_fim DATE NULL,
  manutencao_indeterminada TINYINT(1) NOT NULL DEFAULT 0,
  manutencao_aviso TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY salas_bloco_id_foreign (bloco_id),
  CONSTRAINT salas_bloco_id_foreign
    FOREIGN KEY (bloco_id) REFERENCES blocos (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  sala_id BIGINT UNSIGNED NOT NULL,
  data_reserva DATE NOT NULL,
  periodo VARCHAR(255) NOT NULL,
  status VARCHAR(255) NOT NULL DEFAULT 'pendente',
  recorrente TINYINT(1) NOT NULL DEFAULT 0,
  grupo_recorrencia VARCHAR(255) NULL,
  observacao TEXT NULL,
  comentario_professor TEXT NULL,
  comentario_adm TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY reservas_user_id_foreign (user_id),
  KEY reservas_sala_id_foreign (sala_id),
  KEY reservas_sala_data_periodo_status_index (sala_id, data_reserva, periodo, status),
  KEY reservas_user_status_updated_index (user_id, status, updated_at),
  KEY reservas_grupo_recorrencia_index (grupo_recorrencia),
  CONSTRAINT reservas_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE,
  CONSTRAINT reservas_sala_id_foreign
    FOREIGN KEY (sala_id) REFERENCES salas (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE avisos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(255) NOT NULL,
  mensagem TEXT NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY avisos_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mensagens_diretas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  remetente_id BIGINT UNSIGNED NOT NULL,
  destinatario_id BIGINT UNSIGNED NOT NULL,
  mensagem TEXT NOT NULL,
  lida TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY mensagens_diretas_conversa_index (remetente_id, destinatario_id, created_at),
  KEY mensagens_diretas_destinatario_id_foreign (destinatario_id),
  CONSTRAINT mensagens_diretas_remetente_id_foreign
    FOREIGN KEY (remetente_id) REFERENCES users (id)
    ON DELETE CASCADE,
  CONSTRAINT mensagens_diretas_destinatario_id_foreign
    FOREIGN KEY (destinatario_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notificacao_visualizadas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  referencia VARCHAR(160) NOT NULL,
  visualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY notificacao_visualizadas_unique (user_id, tipo, referencia),
  KEY notificacao_visualizadas_user_tipo_index (user_id, tipo),
  CONSTRAINT notificacao_visualizadas_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, tipo, telefone, email_verified_at, password, remember_token, is_admin, created_at, updated_at) VALUES
('Administrador UniSalas', 'l@gmail.com', 'admin', NULL, NULL, '$2y$10$iv2MBHdSaY1siXzBqD6kTeqAGg6fWtiv8gJ5L/gf2yikM597Fb6hm', NULL, 1, NOW(), NOW()),
('Professor Demo', 'professor@unisalas.local', 'professor', NULL, NULL, '$2y$10$iv2MBHdSaY1siXzBqD6kTeqAGg6fWtiv8gJ5L/gf2yikM597Fb6hm', NULL, 0, NOW(), NOW());

INSERT INTO blocos (nome, cor, manutencao_ativa, manutencao_indeterminada, created_at, updated_at) VALUES
('Bloco A', '#7010a8', 0, 0, NOW(), NOW()),
('Bloco B', '#2563eb', 0, 0, NOW(), NOW());

INSERT INTO salas (nome, capacidade, observacao, bloco_id, manutencao_ativa, manutencao_indeterminada, created_at, updated_at) VALUES
('Sala 101', NULL, 'Sala comum para aulas teoricas.', 1, 0, 0, NOW(), NOW()),
('Laboratorio 01', NULL, 'Sala indicada para aulas praticas.', 1, 0, 0, NOW(), NOW()),
('Sala 201', NULL, 'Sala comum para aulas teoricas.', 2, 0, 0, NOW(), NOW());
