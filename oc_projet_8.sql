-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 19 mai 2020 à 09:45
-- Version du serveur :  8.0.18
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
-- Base de données :  `oc_projet_8`
--

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20200404120758', '2020-04-04 12:08:44');

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_done` tinyint(1) NOT NULL,
  `User_create_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `User_create_ID` (`User_create_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `task`
--

INSERT INTO `task` (`id`, `created_at`, `title`, `content`, `is_done`, `User_create_ID`) VALUES
(120, '2020-05-01 10:30:38', 'test', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sit amet massa vel tellus luctus congue in eget leo. Nam ipsum nunc, rhoncus ut ligula sit amet, semper convallis ante. Nam id suscipit leo. Aenean ullamcorper lacus ut est euismod, non fringilla leo accumsan. Aliquam lobortis, lorem a hendrerit cursus, tortor tellus maximus est, fringilla feugiat arcu lacus et mi. Vivamus pretium eleifend aliquam. Aenean sodales tincidunt mi, a faucibus nibh molestie ac. Donec eu accumsan felis, vitae eleifend nisl. Nunc efficitur euismod nisl, id tristique nisi. Quisque venenatis blandit convallis. Phasellus sagittis a orci a suscipit. Fusce molestie convallis molestie.\r\n\r\nNam ut tortor at ligula consectetur dictum. Cras congue condimentum arcu, vel finibus eros. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed rhoncus laoreet odio. Quisque efficitur mauris nunc, ut rhoncus justo imperdiet sed. Cras suscipit lectus sem, vel venenatis risus imperdiet eget. In sed blandit sem, ac rhoncus purus. Etiam id ex ut enim mollis bibendum vitae eget ligula. Vivamus lectus augue, dictum sed arcu quis, placerat euismod enim. Maecenas gravida feugiat elit, vel hendrerit leo aliquam feugiat. Fusce faucibus consequat lectus. Proin tristique eleifend nibh. Nam pharetra sed ligula quis volutpat.', 0, 59),
(166, '2020-05-10 00:00:00', 'test', 'test', 0, 67);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `roles`) VALUES
(59, 'username', '$2y$13$t8kN6zaL0EH2OpSFB7RQ2OPUDAPTlpn8BsdejuN/k3ztOQC5gOrfW', 'test@test.fr', '[\"ROLE_ADMIN\"]'),
(67, 'test', '$2y$13$waI7pE2mY3u/LA3hSzM8VOSXxg9reVKoe.LG3u0qJkZ/SNPTFLxz6', 'test@test.com', '[\"ROLE_USER\"]');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_F24C741B8D702CD5` FOREIGN KEY (`User_create_ID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
