-- =========================================================
-- SCRIPT DE BASE DE DONNÉES : Gestion de Congés
-- Compatible MySQL 8.x / MariaDB
-- =========================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- TABLE: roles
CREATE TABLE roles (
  id_role INT AUTO_INCREMENT PRIMARY KEY,
  nom_role VARCHAR(50) UNIQUE NOT NULL,
  description TEXT,
  permissions JSON DEFAULT (JSON_OBJECT()),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: departements
CREATE TABLE departements (
  id_departement INT AUTO_INCREMENT PRIMARY KEY,
  nom_departement VARCHAR(100) UNIQUE NOT NULL,
  description TEXT,
  chef_departement_id INT NULL,
  couleur_calendrier VARCHAR(7) DEFAULT '#3b82f6',
  actif BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: types_conges
CREATE TABLE types_conges (
  id_type INT AUTO_INCREMENT PRIMARY KEY,
  nom_type VARCHAR(100) UNIQUE NOT NULL,
  couleur_calendrier VARCHAR(7) NOT NULL,
  duree_max_jours INT DEFAULT NULL,
  necessite_justificatif BOOLEAN DEFAULT FALSE,
  actif BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: users
CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  telephone VARCHAR(20),
  profession VARCHAR(100),
  photo_url VARCHAR(500),
  matricule VARCHAR(20) UNIQUE NOT NULL,
  date_embauche DATE NOT NULL,
  role_id INT NULL,
  departement_id INT NULL,
  solde_conges_annuel INT DEFAULT 25,
  conges_pris INT DEFAULT 0,
  actif BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_role_id (role_id),
  INDEX idx_departement_id (departement_id),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: demandes_conges
CREATE TABLE demandes_conges (
  id_demande INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type_conge_id INT NOT NULL,
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  nb_jours INT NOT NULL,
  motif TEXT,
  statut ENUM('En attente','Approuvé','Refusé') DEFAULT 'En attente',
  commentaire_refus TEXT,
  validateur_id INT NULL,
  date_validation TIMESTAMP NULL,
  document_justificatif TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_user_id (user_id),
  INDEX idx_type_conge_id (type_conge_id),
  INDEX idx_validateur_id (validateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: notifications
CREATE TABLE notifications (
  id_notification INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  type_notification ENUM('info','success','warning','error') DEFAULT 'info',
  lu BOOLEAN DEFAULT FALSE,
  url_action VARCHAR(500),
  document_info TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notif_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: session
CREATE TABLE session (
  id_session INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  session_token VARCHAR(128) NOT NULL UNIQUE,
  ip_address VARCHAR(45),
  user_agent VARCHAR(512),
  last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expires_at TIMESTAMP NULL,
  data JSON NULL,
  INDEX idx_session_user_id (user_id),
  INDEX idx_session_token (session_token),
  INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- AJOUT DES CLÉS ÉTRANGÈRES
-- =========================================================
ALTER TABLE users
  ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id_role)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_users_departement FOREIGN KEY (departement_id) REFERENCES departements(id_departement)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE departements
  ADD CONSTRAINT fk_departement_chef FOREIGN KEY (chef_departement_id) REFERENCES users(id_user)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE demandes_conges
  ADD CONSTRAINT fk_demande_user FOREIGN KEY (user_id) REFERENCES users(id_user)
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_demande_type FOREIGN KEY (type_conge_id) REFERENCES types_conges(id_type)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT fk_demande_validateur FOREIGN KEY (validateur_id) REFERENCES users(id_user)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE notifications
  ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id_user)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE session
  ADD CONSTRAINT fk_session_user FOREIGN KEY (user_id) REFERENCES users(id_user)
    ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- INSERTIONS INITIALES
-- =========================================================
INSERT INTO roles (nom_role, description) VALUES
  ('Administrateur', 'Accès total, gestion globale du système'),
  ('Employes', 'Employé standard, peut faire des demandes de congé'),
  ('chef de departement', 'Valide/Refuse les demandes des membres du département');

INSERT INTO departements (nom_departement, description) VALUES
  ('Informatique', 'Service informatique'),
  ('Ressources Humaines', 'Service RH');

