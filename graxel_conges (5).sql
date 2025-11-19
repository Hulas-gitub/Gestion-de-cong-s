-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 14 nov. 2025 à 14:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `graxel_conges`
--

-- --------------------------------------------------------

--
-- Structure de la table `account_activations`
--

CREATE TABLE `account_activations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `account_activations`
--

INSERT INTO `account_activations` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'prefnapassy@gmail.com', '$2y$12$ljqexe6lotK6PfymVhkFMOzSQhlj92ve4jjxmhdOiPXkImpb6U9IW', '2025-11-10 13:18:44');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_conges`
--

CREATE TABLE `demandes_conges` (
  `id_demande` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type_conge_id` bigint(20) UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` int(11) NOT NULL,
  `motif` text DEFAULT NULL,
  `statut` enum('En attente','Approuvé','Refusé') NOT NULL DEFAULT 'En attente',
  `commentaire_refus` text DEFAULT NULL,
  `validateur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date_validation` timestamp NULL DEFAULT NULL,
  `document_justificatif` text DEFAULT NULL,
  `document_de_validation` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `demandes_conges`
--

INSERT INTO `demandes_conges` (`id_demande`, `user_id`, `type_conge_id`, `date_debut`, `date_fin`, `nb_jours`, `motif`, `statut`, `commentaire_refus`, `validateur_id`, `date_validation`, `document_justificatif`, `document_de_validation`, `created_at`, `updated_at`) VALUES
(3, 23, 6, '2025-11-20', '2025-11-22', 2, 'Moirt de mon oncle', 'En attente', NULL, NULL, NULL, 'uploads/justificatifs/1763079777_EMP008_1763069575_EMP008_6_1762414988_4762566e54c692c6.pdf', '', NULL, '2025-11-13 23:23:32');

-- --------------------------------------------------------

--
-- Structure de la table `departements`
--

CREATE TABLE `departements` (
  `id_departement` bigint(20) UNSIGNED NOT NULL,
  `nom_departement` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `chef_departement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `couleur_calendrier` varchar(7) NOT NULL DEFAULT '#3b82f6',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departements`
--

INSERT INTO `departements` (`id_departement`, `nom_departement`, `description`, `chef_departement_id`, `couleur_calendrier`, `actif`, `created_at`) VALUES
(1, 'Développement Logiciel', 'Le service développement conçoit et réalise les solutions informatiques sur mesure. Il transforme les besoins clients en applications web et mobile fonctionnelles, en assurant l\'intégration avec les systèmes existants et la maintenance évolutive des solutions déployées.', 22, '#3b82f6', 1, '2025-11-10 12:29:27'),
(4, 'Resource Humaines', 'kkg', 28, '#3b82f6', 1, '2025-11-12 08:59:50'),
(6, 'Communication', 'informations légitimes', 28, '#3b82f6', 1, '2025-11-12 12:42:09');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_11_06_192947_create_roles_table', 1),
(4, '2025_11_06_192953_create_departements_table', 1),
(5, '2025_11_06_192954_create_types_conges_table', 1),
(6, '2025_11_06_192955_create_users_table', 1),
(7, '2025_11_06_192956_create_demandes_conges_table', 1),
(8, '2025_11_06_192957_create_notifications_table', 1),
(9, '2025_11_06_192957_create_sessions_table', 1),
(10, '2025_11_06_212256_create_sessions_table', 2),
(11, '2025_11_06_213356_create_password_resets_table', 3),
(12, '2025_11_10_113304_create_account_activations_table', 4),
(13, '2025_11_14_124244_create_password_resets_table', 5),
(14, '2025_11_14_124444_create_password_resets_table', 6);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type_notification` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `url_action` varchar(500) DEFAULT NULL,
  `document_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` bigint(20) UNSIGNED NOT NULL,
  `nom_role` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `nom_role`, `description`, `permissions`, `created_at`) VALUES
(1, 'Admin', 'Gère tout le système', '{\"access\":\"illimité\"}', '2025-11-06 20:24:30'),
(2, 'employé', 'peux effectuer une demande de congé', '{\"access\":\"limité\"}', '2025-11-06 20:29:38'),
(3, 'chef de departement', 'peux valider/refuse une demande de congé de son departement. bloquer ses employé', '{\"access\":\"limité\"}', '2025-11-06 20:29:38');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
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
-- Structure de la table `types_conges`
--

CREATE TABLE `types_conges` (
  `id_type` bigint(20) UNSIGNED NOT NULL,
  `nom_type` varchar(100) NOT NULL,
  `couleur_calendrier` varchar(7) NOT NULL,
  `duree_max_jours` int(11) DEFAULT NULL,
  `necessite_justificatif` tinyint(1) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `types_conges`
--

INSERT INTO `types_conges` (`id_type`, `nom_type`, `couleur_calendrier`, `duree_max_jours`, `necessite_justificatif`, `actif`, `created_at`) VALUES
(1, 'Congés payés', 'vert', NULL, 0, 1, '2025-11-13 13:03:03'),
(2, 'Congé maladie', 'rouge', NULL, 0, 1, '2025-11-13 13:03:03'),
(3, 'Congé maternité', 'Jaune', NULL, 0, 1, '2025-11-13 13:04:40'),
(5, 'Congé paternité', 'Bleu ci', NULL, 0, 1, '2025-11-13 13:06:52'),
(6, 'Congé autre', 'Violine', NULL, 0, 1, '2025-11-13 13:06:52');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  `matricule` varchar(20) NOT NULL,
  `date_embauche` date NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `departement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `solde_conges_annuel` int(11) NOT NULL DEFAULT 25,
  `conges_pris` int(11) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `nom`, `prenom`, `email`, `password`, `telephone`, `profession`, `photo_url`, `matricule`, `date_embauche`, `role_id`, `departement_id`, `solde_conges_annuel`, `conges_pris`, `actif`, `created_at`, `updated_at`) VALUES
(2, 'DJYEMBI', 'Hulas', 'cedricmoussavou25@gmail.com', '$2y$12$5bKp/LIPfzt61gkgx/SWGuTp8kpg5g0rwapHx5vy47Xdjx4LTjxFC', '+241556982', 'Dévéloppeur', NULL, 'ADM001', '2025-04-15', 1, NULL, 25, 0, 1, NULL, NULL),
(20, 'BOUSSOUSGOU', 'Hulas', 'prefnachacha@gmail.com', '$2y$12$e0iYlqZrcJ9NMFQPENlrhuSu9Ear2c3LLp5BbApHoNLsUxuCPNoS2', '+24177521772', 'Cybersécurité', NULL, 'EMP007', '2025-11-12', 2, 1, 30, 0, 1, '2025-11-12 09:17:06', '2025-11-12 09:18:48'),
(22, 'MARANGA', 'Boris', 'cedrickmougaingui@gmail.com', '$2y$12$W15xDyCiv0DP6KG7rqQ1mONwqgDmnyIJ8Aw4kiJaXgISxrGUayPBu', '+241778986', 'Gestion de projet', NULL, 'MGR004', '2025-04-12', 3, 4, 30, 0, 1, '2025-11-12 09:45:24', '2025-11-13 08:33:50'),
(23, 'ZEDIANE', 'Sophia', 'hulassanders@gmail.com', '$2y$12$.0k6Kv/GNm062ccoa2wGBe2eKox6ofSVMoW9QtpLNpLthX6QH1ZBm', '+24174809526', 'Agent de recrutement', NULL, 'EMP008', '2025-08-12', 2, 4, 30, 0, 1, '2025-07-09 09:54:26', '2025-11-12 11:17:56'),
(24, 'NANG NGUEMA', 'Brenn Tendresse', 'nangnguema222@gmail.com', '$2y$12$PomvRRBD3MBguuWlvB0tLuFH0H0g1D8eO0nSep.lki78F8joG.RW.', '+24174936536', 'Community manager', NULL, 'EMP009', '2025-11-13', 2, 1, 30, 0, 1, '2025-11-12 11:25:14', '2025-11-13 20:50:11'),
(25, 'MABIKA', 'Fallys', 'andymoukassa2000@gmail.com', '$2y$12$TiTfpqftWUKnNwRjgXav2e6ELwlGyZfFM5IWjZArIzIJabXiXNDaa', '+24174026220', 'Marketineur', NULL, 'EMP010', '2025-11-12', 2, 4, 30, 0, 1, '2025-11-12 11:28:30', '2025-11-12 11:29:22'),
(26, 'AJDABA', 'Vanessa', 'andymoukassa20@gmail.com', '$2y$12$5MeG3vFipd/4s75vZbMhS.XiQ.UNXrjAYRbuxHlCvclJ61Jqdm8Ve', '+241627934836', 'Controleur', NULL, 'MGR005', '2025-11-07', 3, 4, 30, 0, 0, '2025-11-12 11:30:21', '2025-11-14 10:43:53'),
(27, 'MENGUE', 'Dorcas.L', 'meguedorcas@gmail.com', '$2y$12$eEFu9Q9jtIxoRgp4JwYFj.m2u.cDNatqH9A04zOqSuq3Gzb8cD1nq', '+2417756958', 'Community manager', NULL, 'EMP011', '2025-11-12', 2, 4, 30, 0, 1, '2025-11-12 11:38:20', '2025-11-12 11:43:26'),
(28, 'MENGUE', 'Gertude', 'nangnguema22@gmail.com', '$2y$12$MIdW3LFvBBBuE13Budix2e6iKzSTOV7Py1FrnlTxFug33r9KQh3G6', '+24166898750', 'Sécretaire du RH', NULL, 'MGR006', '2025-07-25', 3, 4, 30, 0, 1, '2025-11-12 11:40:17', '2025-11-13 20:56:43'),
(29, 'BAYOS', 'Akimi', 'bayosakimi@gmail.com', '$2y$12$O/VLefwmMU9RYD1W0xolJe2QbA2KVuiryzQSvr0fwBenGYpd4WX26', '+24166986500', 'Agent de recrutement', NULL, 'EMP012', '2025-11-12', 2, 4, 30, 0, 1, '2025-11-12 11:44:32', '2025-11-12 11:44:32'),
(30, 'BOUSSOUSGOU', 'Solange', 'andymoukassasolange@gmail.com', '$2y$12$ZegVIB0DV1T/C9H.OLfaxeY4WqLQ7VHruJT76WrhWGxB0o5rUS6yK', '+2419989756', 'formateur client', NULL, 'EMP013', '2025-11-12', 2, 1, 30, 0, 1, '2025-11-12 12:00:57', '2025-11-12 12:02:05'),
(31, 'Massouga', 'Felicité', 'hdjyembi@gmail.com', '$2y$12$nSYArHg9OY0YUeQzrEvmX.CkYFllDzyne3Zipr7KqbcFhBoWvxVEa', '+24174026220', 'Community manager', NULL, 'EMP014', '2025-11-13', 2, 6, 30, 0, 1, '2025-11-13 11:26:48', '2025-11-13 11:26:48');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `account_activations`
--
ALTER TABLE `account_activations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_activations_email_index` (`email`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `demandes_conges`
--
ALTER TABLE `demandes_conges`
  ADD PRIMARY KEY (`id_demande`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type_conge_id` (`type_conge_id`),
  ADD KEY `idx_validateur_id` (`validateur_id`);

--
-- Index pour la table `departements`
--
ALTER TABLE `departements`
  ADD PRIMARY KEY (`id_departement`),
  ADD UNIQUE KEY `departements_nom_departement_unique` (`nom_departement`),
  ADD KEY `fk_departement_chef` (`chef_departement_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `idx_notif_user_id` (`user_id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `roles_nom_role_unique` (`nom_role`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `types_conges`
--
ALTER TABLE `types_conges`
  ADD PRIMARY KEY (`id_type`),
  ADD UNIQUE KEY `types_conges_nom_type_unique` (`nom_type`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_matricule_unique` (`matricule`),
  ADD KEY `idx_role_id` (`role_id`),
  ADD KEY `idx_departement_id` (`departement_id`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `account_activations`
--
ALTER TABLE `account_activations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `demandes_conges`
--
ALTER TABLE `demandes_conges`
  MODIFY `id_demande` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `departements`
--
ALTER TABLE `departements`
  MODIFY `id_departement` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `types_conges`
--
ALTER TABLE `types_conges`
  MODIFY `id_type` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demandes_conges`
--
ALTER TABLE `demandes_conges`
  ADD CONSTRAINT `fk_demande_type` FOREIGN KEY (`type_conge_id`) REFERENCES `types_conges` (`id_type`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_demande_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_demande_validateur` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `departements`
--
ALTER TABLE `departements`
  ADD CONSTRAINT `fk_departement_chef` FOREIGN KEY (`chef_departement_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_departement` FOREIGN KEY (`departement_id`) REFERENCES `departements` (`id_departement`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
