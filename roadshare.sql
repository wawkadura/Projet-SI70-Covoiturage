-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 21 nov. 2020 à 23:00
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `roadshare`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresse_postale`
--

DROP TABLE IF EXISTS `adresse_postale`;
CREATE TABLE IF NOT EXISTS `adresse_postale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_rue` int(11) DEFAULT NULL,
  `rue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adresse_postale`
--

INSERT INTO `adresse_postale` (`id`, `numero_rue`, `rue`, `ville`) VALUES
(9, 10, 'silicone valley', 'grenoble'),
(10, 54, 'silicone valley', 'lyon'),
(11, 13, 'silicone valley', 'paris'),
(12, 18, 'silicone valley', 'belfort'),
(13, 20, 'silicone valley', 'lille'),
(14, 99, 'RueDomicile', 'VilleDomicile'),
(15, 99, 'rue jean jaurés', 'grenoble'),
(16, 99, 'rue charles de gaules', 'lyon');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destinataire_id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F91ABF0A4F84F6E` (`destinataire_id`),
  KEY `IDX_8F91ABF010335F61` (`expediteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `destinataire_id`, `expediteur_id`, `message`, `note`) VALUES
(11, 20, 11, ' salut je suis kadura et j\'ai adorer être avec toi !', 4.5),
(12, 19, 12, ' salut je suis modric et j\'ai adorer être avec toi !', 4.5),
(13, 18, 13, ' salut je suis pavard et j\'ai adorer être avec toi !', 4.5),
(14, 17, 14, ' salut je suis lionel et j\'ai adorer être avec toi !', 4.5),
(15, 16, 15, ' salut je suis Du sud et j\'ai adorer être avec toi !', 4.5),
(16, 15, 16, ' salut je suis bertran et j\'ai adorer être avec toi !', 4.5),
(17, 14, 17, ' salut je suis koman et j\'ai adorer être avec toi !', 4.5),
(18, 13, 18, ' salut je suis tse et j\'ai adorer être avec toi !', 4.5),
(19, 12, 19, ' salut je suis wiliam et j\'ai adorer être avec toi !', 4.5),
(20, 11, 20, ' salut je suis kawafi et j\'ai adorer être avec toi !', 4.5);

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`id`, `email`, `mot_de_passe`) VALUES
(11, 'walid@yahoo.com', '$2y$10$2TwhDaZsRPm61uHl3lgQM.HGjcqt0fxQ4168ZzU/n2sM7vqaby60q'),
(12, 'lucas@yahoo.com', '$2y$10$voD4Pu3J5OliF8LvlbbfM.EV9mg3FFBL3mRBgIqaSMdyeF3B/LlGy'),
(13, 'benjamin@yahoo.com', '$2y$10$noiQsJIIVjUwfkubxOVaKuqD4UkdEPz.Zm8feFhzoIK3lrUI1SXGG'),
(14, 'muftah@yahoo.com', '$2y$10$Y/euXr6bUKxXSr..QONNuOt6I0J97WflcIRXD2jXdBNvVZChVQs/6'),
(15, 'thomas@yahoo.com', '$2y$10$gyFebjvu7kOKmRgeXGjXKO/ZKbHEIGQJcCA0k/4zIWoOpiI3oseBy'),
(16, 'annie@yahoo.com', '$2y$10$KVfjBMXsKZ3oQMfMI453geTlvF0iExQdGSzJCEuMa9QZ9rWohPy4u'),
(17, 'elise@yahoo.com', '$2y$10$lvoPHnek.XwW0EWy63LG7exZn7AlbG34uFjvff3HZocQ2MKwMR7X.'),
(18, 'melodie@yahoo.com', '$2y$10$GXwtotiX6.2Rw8FxLh0AE.v9U5UjUL06N4syI.tltoFgIiRg.kSXC'),
(19, 'john@yahoo.com', '$2y$10$cqdr2AuXsRShVPXWDMjzYeca/DBLJ0FJ5IxxCOL7YwzPyS1vmqqMS'),
(20, 'charlotte@yahoo.com', '$2y$10$sOsnC6o8vwMjJ9d6dfVPwOiOvehuY7JC6bXo0.rjVV2DQZz1JnyPq');

-- --------------------------------------------------------

--
-- Structure de la table `description`
--

DROP TABLE IF EXISTS `description`;
CREATE TABLE IF NOT EXISTS `description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mini_bio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voyager_avec_fumeur` tinyint(1) NOT NULL,
  `voyager_avec_musique` tinyint(1) NOT NULL,
  `voyager_avec_animaux` tinyint(1) NOT NULL,
  `centre_interets` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `description`
--

INSERT INTO `description` (`id`, `mini_bio`, `voyager_avec_fumeur`, `voyager_avec_musique`, `voyager_avec_animaux`, `centre_interets`) VALUES
(11, NULL, 1, 1, 0, NULL),
(12, NULL, 0, 1, 0, NULL),
(13, NULL, 1, 1, 1, NULL),
(14, NULL, 1, 1, 0, NULL),
(15, NULL, 0, 1, 0, NULL),
(16, NULL, 1, 1, 1, NULL),
(17, NULL, 1, 1, 0, NULL),
(18, NULL, 0, 1, 0, NULL),
(19, NULL, 1, 1, 1, NULL),
(20, NULL, 1, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20201121001840', '2020-11-21 00:18:49', 1815);

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse_postale_id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D19FA60C96EEC07` (`adresse_postale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id`, `adresse_postale_id`, `nom`) VALUES
(6, 9, 'Google'),
(7, 10, 'Amazon'),
(8, 11, 'Intel'),
(9, 12, 'Facebook'),
(10, 13, 'Apple');

-- --------------------------------------------------------

--
-- Structure de la table `information_travail`
--

DROP TABLE IF EXISTS `information_travail`;
CREATE TABLE IF NOT EXISTS `information_travail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entreprise_id` int(11) NOT NULL,
  `horaire_debut` time NOT NULL,
  `horaire_fin` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8DB8DA2AA4AEAFEA` (`entreprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `information_travail`
--

INSERT INTO `information_travail` (`id`, `entreprise_id`, `horaire_debut`, `horaire_fin`) VALUES
(11, 6, '08:00:00', '17:00:00'),
(12, 7, '07:00:00', '16:00:00'),
(13, 8, '09:00:00', '18:00:00'),
(14, 9, '09:30:00', '18:30:00'),
(15, 6, '08:00:00', '16:00:00'),
(16, 7, '08:00:00', '17:00:00'),
(17, 8, '08:00:00', '17:00:00'),
(18, 9, '08:00:00', '17:00:00'),
(19, 6, '08:00:00', '17:00:00'),
(20, 7, '08:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demandeur_id` int(11) NOT NULL,
  `trajet_id` int(11) NOT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42C8495595A6EE59` (`demandeur_id`),
  KEY `IDX_42C84955D12A823` (`trajet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `demandeur_id`, `trajet_id`, `etat`) VALUES
(29, 11, 30, 'ACCEPTER'),
(30, 12, 29, 'EN_ATTENTE'),
(31, 13, 28, 'EN_ATTENTE'),
(32, 14, 27, 'EN_ATTENTE'),
(33, 15, 26, 'EN_ATTENTE'),
(34, 16, 25, 'EN_ATTENTE'),
(35, 17, 24, 'EN_ATTENTE'),
(36, 18, 23, 'EN_ATTENTE'),
(37, 19, 22, 'EN_ATTENTE'),
(38, 20, 21, 'EN_ATTENTE'),
(39, 12, 21, 'REFUSER'),
(40, 13, 21, 'EN_ATTENTE'),
(41, 14, 21, 'EN_ATTENTE'),
(42, 15, 21, 'REFUSER'),
(43, 16, 21, 'EN_ATTENTE'),
(44, 17, 21, 'EN_ATTENTE'),
(45, 18, 21, 'REFUSER'),
(46, 19, 21, 'EN_ATTENTE'),
(47, 20, 21, 'EN_ATTENTE'),
(48, 11, 22, 'REFUSER'),
(49, 11, 23, 'EN_ATTENTE'),
(50, 11, 24, 'EN_ATTENTE'),
(51, 11, 25, 'REFUSER'),
(52, 11, 26, 'EN_ATTENTE'),
(53, 11, 27, 'EN_ATTENTE'),
(54, 11, 28, 'REFUSER'),
(55, 11, 29, 'EN_ATTENTE'),
(56, 11, 30, 'EN_ATTENTE');

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

DROP TABLE IF EXISTS `trajet`;
CREATE TABLE IF NOT EXISTS `trajet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conducteur_id` int(11) NOT NULL,
  `adresse_depart_id` int(11) NOT NULL,
  `adresse_arrivee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `heure_depart` time NOT NULL,
  `heure_arrivee` time NOT NULL,
  `nb_places` int(11) NOT NULL,
  `prix` double NOT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2B5BA98CF16F4AC6` (`conducteur_id`),
  KEY `IDX_2B5BA98C305689D` (`adresse_depart_id`),
  KEY `IDX_2B5BA98C85ED0E35` (`adresse_arrivee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trajet`
--

INSERT INTO `trajet` (`id`, `conducteur_id`, `adresse_depart_id`, `adresse_arrivee_id`, `date`, `heure_depart`, `heure_arrivee`, `nb_places`, `prix`, `etat`) VALUES
(21, 11, 15, 16, '2020-11-21', '07:00:00', '08:00:00', 3, 9, 'EN_COURS'),
(22, 12, 15, 9, '2020-11-21', '07:00:00', '08:00:00', 3, 10, 'EN_COURS'),
(23, 13, 15, 9, '2020-11-21', '07:00:00', '08:00:00', 3, 11, 'EN_COURS'),
(24, 14, 15, 16, '2020-11-21', '10:00:00', '11:00:00', 3, 7, 'EN_COURS'),
(25, 15, 15, 16, '2020-11-21', '11:00:00', '12:00:00', 3, 8, 'EN_COURS'),
(26, 16, 15, 16, '2020-11-21', '12:00:00', '13:00:00', 3, 9, 'EN_COURS'),
(27, 17, 15, 16, '2020-11-21', '13:00:00', '14:00:00', 3, 13, 'EN_COURS'),
(28, 18, 15, 16, '2020-11-21', '15:00:00', '16:00:00', 3, 12, 'EN_COURS'),
(29, 19, 15, 16, '2020-11-21', '18:00:00', '19:00:00', 3, 8, 'EN_COURS'),
(30, 20, 15, 16, '2020-11-21', '20:00:00', '21:00:00', 3, 9, 'EN_COURS'),
(31, 11, 15, 16, '2020-11-21', '07:00:00', '08:00:00', 3, 9, 'EFFECTUE'),
(32, 12, 15, 16, '2020-11-21', '07:00:00', '08:00:00', 3, 10, 'EFFECTUE'),
(33, 13, 15, 16, '2020-11-21', '07:00:00', '08:00:00', 3, 11, 'EFFECTUE'),
(34, 14, 15, 16, '2020-11-21', '10:00:00', '11:00:00', 3, 7, 'EFFECTUE'),
(35, 15, 15, 16, '2020-11-21', '11:00:00', '12:00:00', 3, 8, 'EFFECTUE'),
(36, 16, 15, 16, '2020-11-21', '12:00:00', '13:00:00', 3, 9, 'ANNULER'),
(37, 17, 15, 16, '2020-11-21', '13:00:00', '14:00:00', 3, 13, 'EFFECTUE'),
(38, 18, 15, 16, '2020-11-21', '15:00:00', '16:00:00', 3, 12, 'ANNULER'),
(39, 19, 15, 16, '2020-11-21', '18:00:00', '19:00:00', 3, 8, 'EFFECTUE'),
(40, 20, 15, 16, '2020-11-21', '20:00:00', '21:00:00', 3, 9, 'EFFECTUE');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse_postale_id` int(11) NOT NULL,
  `description_id` int(11) NOT NULL,
  `voiture_id` int(11) DEFAULT NULL,
  `compte_id` int(11) NOT NULL,
  `information_travail_id` int(11) DEFAULT NULL,
  `nom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_de_naissance` date NOT NULL,
  `telephone` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3D9F966B` (`description_id`),
  UNIQUE KEY `UNIQ_1D1C63B3F2C56620` (`compte_id`),
  UNIQUE KEY `UNIQ_1D1C63B3181A8BA` (`voiture_id`),
  UNIQUE KEY `UNIQ_1D1C63B3EDF610A0` (`information_travail_id`),
  KEY `IDX_1D1C63B3C96EEC07` (`adresse_postale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `adresse_postale_id`, `description_id`, `voiture_id`, `compte_id`, `information_travail_id`, `nom`, `prenom`, `date_de_naissance`, `telephone`) VALUES
(11, 14, 11, 11, 11, 11, 'kadura', 'walid', '1999-04-24', '0612345678'),
(12, 14, 12, 12, 12, 12, 'modric', 'lucas', '2000-04-24', '0612345678'),
(13, 14, 13, 13, 13, 13, 'pavard', 'benjamin', '1995-04-24', '0612345678'),
(14, 14, 14, 14, 14, 14, 'lionel', 'muftah', '1994-04-12', '0612345678'),
(15, 14, 15, 15, 15, 15, 'Du sud', 'thomas', '1998-04-03', '0612345678'),
(16, 14, 16, 16, 16, 16, 'bertran', 'annie', '1995-04-24', '0612345678'),
(17, 14, 17, 17, 17, 17, 'koman', 'elise', '1990-06-05', '0612345678'),
(18, 14, 18, 18, 18, 18, 'tse', 'melodie', '1999-04-10', '0612345678'),
(19, 14, 19, 19, 19, 19, 'wiliam', 'john', '1999-04-15', '0612345678'),
(20, 14, 20, 20, 20, 20, 'kawafi', 'charlotte', '2001-07-23', '0612345678');

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

DROP TABLE IF EXISTS `voiture`;
CREATE TABLE IF NOT EXISTS `voiture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `immatriculation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`id`, `marque`, `couleur`, `immatriculation`, `model`) VALUES
(11, 'Renault', 'Rouge', 'AB-000-CD', 'Clio'),
(12, 'Renault', 'Rouge', 'AB-001-CD', 'Clio'),
(13, 'Renault', 'Rouge', 'AB-002-CD', 'Clio'),
(14, 'Renault', 'Rouge', 'AB-003-CD', 'Clio'),
(15, 'Renault', 'Rouge', 'AB-004-CD', 'Clio'),
(16, 'Renault', 'Rouge', 'AB-005-CD', 'Clio'),
(17, 'Renault', 'Rouge', 'AB-006-CD', 'Clio'),
(18, 'Renault', 'Rouge', 'AB-007-CD', 'Clio'),
(19, 'Renault', 'Rouge', 'AB-008-CD', 'Clio'),
(20, 'Renault', 'Rouge', 'AB-009-CD', 'Clio');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `FK_8F91ABF010335F61` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_8F91ABF0A4F84F6E` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD CONSTRAINT `FK_D19FA60C96EEC07` FOREIGN KEY (`adresse_postale_id`) REFERENCES `adresse_postale` (`id`);

--
-- Contraintes pour la table `information_travail`
--
ALTER TABLE `information_travail`
  ADD CONSTRAINT `FK_8DB8DA2AA4AEAFEA` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprise` (`id`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_42C8495595A6EE59` FOREIGN KEY (`demandeur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_42C84955D12A823` FOREIGN KEY (`trajet_id`) REFERENCES `trajet` (`id`);

--
-- Contraintes pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD CONSTRAINT `FK_2B5BA98C305689D` FOREIGN KEY (`adresse_depart_id`) REFERENCES `adresse_postale` (`id`),
  ADD CONSTRAINT `FK_2B5BA98C85ED0E35` FOREIGN KEY (`adresse_arrivee_id`) REFERENCES `adresse_postale` (`id`),
  ADD CONSTRAINT `FK_2B5BA98CF16F4AC6` FOREIGN KEY (`conducteur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_1D1C63B3181A8BA` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_1D1C63B3C96EEC07` FOREIGN KEY (`adresse_postale_id`) REFERENCES `adresse_postale` (`id`),
  ADD CONSTRAINT `FK_1D1C63B3D9F966B` FOREIGN KEY (`description_id`) REFERENCES `description` (`id`),
  ADD CONSTRAINT `FK_1D1C63B3EDF610A0` FOREIGN KEY (`information_travail_id`) REFERENCES `information_travail` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_1D1C63B3F2C56620` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
