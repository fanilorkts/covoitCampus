-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 09 oct. 2026 à 09:27
-- Version du serveur : 8.0.40
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `covoiturage_campus`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int NOT NULL,
  `note` int NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `id_trajet` int NOT NULL,
  `id_auteur` int NOT NULL,
  `id_cible` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `note`, `commentaire`, `date`, `id_trajet`, `id_auteur`, `id_cible`) VALUES
(1, 5, 'Conductrice ponctuelle et très sympathique.', '2026-09-19', 1, 3, 2),
(2, 4, 'Trajet agréable, un peu de retard au départ.', '2026-09-19', 1, 4, 2),
(3, 5, 'Passager ponctuel, rien à dire.', '2026-09-19', 1, 2, 3),
(4, 3, 'Un peu en retard au point de rendez-vous.', '2026-09-20', 1, 2, 4),
(5, 5, 'Très bon conducteur, conduite souple.', '2026-09-20', 2, 2, 3),
(6, 4, 'Passagère agréable.', '2026-09-20', 2, 3, 2),
(7, 5, 'Trajet parfait, merci David !', '2026-09-27', 3, 6, 5),
(8, 1, 'Nul, à éviter absolument !', '2026-09-27', 3, 7, 5),
(9, 5, 'Passagère agréable et ponctuelle.', '2026-09-27', 3, 5, 6),
(10, 2, 'En retard au rendez-vous.', '2026-09-27', 3, 5, 7);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260930082627', '2026-09-30 08:26:29', 193),
('DoctrineMigrations\\Version20261001181136', '2026-10-01 18:13:04', 86),
('DoctrineMigrations\\Version20261001224220', '2026-10-01 22:42:56', 216),
('DoctrineMigrations\\Version20261002081811', NULL, NULL),
('DoctrineMigrations\\Version20261002084148', '2026-10-02 08:42:15', 335),
('DoctrineMigrations\\Version20261002084406', '2026-10-02 08:44:42', 36),
('DoctrineMigrations\\Version20261002092632', NULL, NULL),
('DoctrineMigrations\\Version20261006090549', '2026-10-06 09:07:19', 52),
('DoctrineMigrations\\Version20261009100000', '2026-10-09 09:17:11', 288);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int NOT NULL,
  `statut` varchar(255) NOT NULL,
  `date_reservation` date NOT NULL,
  `id_trajet` int DEFAULT NULL,
  `id_passager` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `statut`, `date_reservation`, `id_trajet`, `id_passager`) VALUES
(1, 'Confirmée', '2026-09-10', 1, 3),
(2, 'Confirmée', '2026-09-11', 1, 4),
(3, 'Refusée', '2026-09-12', 1, 5),
(4, 'Confirmée', '2026-09-12', 2, 2),
(5, 'Confirmée', '2026-09-20', 3, 6),
(6, 'Confirmée', '2026-09-21', 3, 7),
(7, 'Confirmée', '2026-09-22', 3, 4),
(8, 'Confirmée', '2026-09-30', 4, 3),
(9, 'Confirmée', '2026-10-01', 4, 5),
(10, 'Refusée', '2026-10-01', 4, 6),
(11, 'Confirmée', '2026-10-01', 6, 8),
(12, 'Confirmée', '2026-10-02', 6, 3),
(13, 'Confirmée', '2026-09-28', 7, 2),
(14, 'Confirmée', '2026-09-29', 7, 5),
(15, 'Confirmée', '2026-09-30', 8, 7),
(16, 'En attente', '2026-10-01', 8, 3),
(17, 'Confirmée', '2026-09-27', 9, 4);

-- --------------------------------------------------------

--
-- Structure de la table `trajets`
--

CREATE TABLE `trajets` (
  `id` int NOT NULL,
  `origine` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `date_heure` datetime NOT NULL,
  `places_totales` int NOT NULL,
  `places_restantes` int NOT NULL,
  `statut` varchar(255) NOT NULL,
  `id_conducteur` int DEFAULT NULL,
  `prix` decimal(6,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `trajets`
--

INSERT INTO `trajets` (`id`, `origine`, `destination`, `date_heure`, `places_totales`, `places_restantes`, `statut`, `id_conducteur`, `prix`) VALUES
(1, 'Campus', 'Gare', '2026-09-18 08:00:00', 3, 1, 'Terminé', 2, 0.00),
(2, 'Gare', 'Campus', '2026-09-19 18:30:00', 2, 1, 'Terminé', 3, 0.00),
(3, 'Campus', 'Lyon', '2026-09-26 07:30:00', 3, 0, 'Terminé', 5, 0.00),
(4, 'Campus', 'Lyon', '2026-10-10 17:30:00', 3, 1, 'Ouvert', 2, 0.00),
(5, 'Campus', 'Paris', '2026-10-15 06:45:00', 4, 4, 'Ouvert', 6, 0.00),
(6, 'Gare', 'Campus', '2026-10-12 07:45:00', 2, 0, 'Terminé', 7, 0.00),
(7, 'Campus', 'Gare', '2026-10-09 08:15:00', 2, 0, 'Terminé', 4, 0.00),
(8, 'Campus', 'Aéroport', '2026-10-20 05:30:00', 1, 0, 'Complet', 8, 0.00),
(9, 'Campus', 'Gare', '2026-10-01 18:00:00', 3, 2, 'Terminé', 3, 0.00),
(10, 'Hitema', 'Issy les Moulineaux', '2026-10-15 09:21:00', 5, 5, 'Ouvert', 1, 7.00);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `email` varchar(180) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `role` varchar(20) NOT NULL,
  `biographie` longtext,
  `centres_interet` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `nom`, `password`, `prenom`, `date_naissance`, `role`, `biographie`, `centres_interet`) VALUES
(1, 'admin@campus.fr', 'Admin', '$2y$13$cfv4/S905jfGRA12s6ufz.d/JKcVl/irmm1tPT8cdPZWXOs96ogKK', 'Campus', '1990-01-01', 'ROLE_ADMIN', 'Administratrice de la plateforme CoVoiturage Campus. Je veille au bon fonctionnement du service et à la qualité des échanges entre étudiants.', 'modération, vie étudiante, numérique'),
(2, 'alice@campus.fr', 'Martin', '$2y$13$XddGJKORy8vQZXi3W/fsI.8pKfq3.JElRLZ32dT8.9Z9ELCA1mnBe', 'Alice', '2003-04-12', 'ROLE_USER', 'Étudiante en informatique, je rentre chez mes parents à Lyon presque tous les week-ends. Ponctuelle et discrète, j\'aime les trajets calmes avec un peu de musique.', 'musique, randonnée, cinéma'),
(3, 'bob@campus.fr', 'Durand', '$2y$13$k5XuCukcidp92nV8VSgZvOUQU8SioJjDT02znYFvD48YDnGUbX7Hy', 'Bob', '2002-09-30', 'ROLE_USER', 'Étudiant en master, conducteur régulier entre le campus et la gare. Je pars toujours à l\'heure et je prends volontiers des passagers pour partager les frais.', 'sport, jeux vidéo, podcasts'),
(4, 'chloe@campus.fr', 'Bernard', '$2y$12$xE1QYcuIGYK9c6L27zNpM.WSjarHtvFvtryxVZLHkYMosF48lC/X6', 'Chloé', '2004-01-22', 'ROLE_USER', 'Étudiante en deuxième année, je fais souvent le trajet campus-gare pour les cours du matin. Toujours partante pour discuter pendant la route.', 'photographie, lecture, cuisine'),
(5, 'david@campus.fr', 'Petit', '$2y$12$xE1QYcuIGYK9c6L27zNpM.WSjarHtvFvtryxVZLHkYMosF48lC/X6', 'David', '2003-11-05', 'ROLE_USER', 'Conducteur sur la ligne Campus-Lyon, je propose des places chaque fin de semaine. Conduite souple et respect des horaires, c\'est ma priorité.', 'football, voyages, mécanique'),
(6, 'emma@campus.fr', 'Moreau', '$2y$12$xE1QYcuIGYK9c6L27zNpM.WSjarHtvFvtryxVZLHkYMosF48lC/X6', 'Emma', '2005-02-17', 'ROLE_USER', 'Première année sur le campus, je découvre le covoiturage pour mes déplacements vers Paris. J\'aime rencontrer de nouvelles personnes pendant les trajets.', 'danse, séries, mode'),
(7, 'hugo@campus.fr', 'Laurent', '$2y$13$FGCpI3bZ1TDrNNueByddTu26uK7MTEpRXaoWN6shFgxVJZdadDXHi', 'Hugo', '2001-07-08', 'ROLE_USER', 'Étudiant en fin de cursus, je fais la navette entre la gare et le campus tôt le matin. Je préfère les trajets tranquilles, mais je suis toujours sympa.', 'cyclisme, musique électro, astronomie'),
(8, 'ines@campus.fr', 'Garcia', '$2y$12$xE1QYcuIGYK9c6L27zNpM.WSjarHtvFvtryxVZLHkYMosF48lC/X6', 'Inès', '2004-12-03', 'ROLE_USER', 'Je prends régulièrement la route vers l\'aéroport pour mes déplacements. Organisée et fiable, je préviens toujours en cas de changement.', 'voyages, langues étrangères, yoga');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `id` int NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `couleur` varchar(30) DEFAULT NULL,
  `annee` smallint DEFAULT NULL,
  `immatriculation` varchar(15) NOT NULL,
  `nb_places` smallint NOT NULL,
  `id_proprietaire` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `marque`, `modele`, `couleur`, `annee`, `immatriculation`, `nb_places`, `id_proprietaire`) VALUES
(1, 'Renault', 'Espace', 'Gris', 2019, 'FG-214-HK', 7, 1),
(2, 'Peugeot', '208', 'Bleu', 2018, 'EA-482-QX', 5, 2),
(3, 'Volkswagen', 'Golf', 'Blanc', 2020, 'GB-731-TL', 5, 3),
(4, 'Citroën', 'C3', 'Rouge', 2017, 'DR-359-MS', 5, 4),
(5, 'Renault', 'Clio', 'Noir', 2016, 'CW-846-PN', 5, 5),
(6, 'Dacia', 'Duster', 'Vert', 2021, 'GX-127-BV', 5, 6),
(7, 'Fiat', '500', 'Jaune', 2015, 'DM-593-ZK', 4, 7),
(8, 'Smart', 'ForTwo', 'Blanc', 2020, 'GH-305-WA', 2, 8);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8F91ABF0D6C1C61` (`id_trajet`),
  ADD KEY `IDX_8F91ABF0236D04AD` (`id_auteur`),
  ADD KEY `IDX_8F91ABF0FD8AD0B` (`id_cible`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_42C84955D6C1C61` (`id_trajet`),
  ADD KEY `IDX_42C84955EF2FC27C` (`id_passager`);

--
-- Index pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FF2B5BA986EDF194` (`id_conducteur`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_VEHICULE_IMMATRICULATION` (`immatriculation`),
  ADD KEY `IDX_292FFF1D4A22ECA4` (`id_proprietaire`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `trajets`
--
ALTER TABLE `trajets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `FK_8F91ABF0236D04AD` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `FK_8F91ABF0D6C1C61` FOREIGN KEY (`id_trajet`) REFERENCES `trajets` (`id`),
  ADD CONSTRAINT `FK_8F91ABF0FD8AD0B` FOREIGN KEY (`id_cible`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_42C84955D6C1C61` FOREIGN KEY (`id_trajet`) REFERENCES `trajets` (`id`),
  ADD CONSTRAINT `FK_42C84955EF2FC27C` FOREIGN KEY (`id_passager`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD CONSTRAINT `FK_FF2B5BA986EDF194` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `FK_292FFF1D4A22ECA4` FOREIGN KEY (`id_proprietaire`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
