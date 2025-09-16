-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 16 sep. 2025 à 08:55
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `evaluationstages`
--

-- --------------------------------------------------------

--
-- Structure de la table `anneegrilleeval`
--

DROP TABLE IF EXISTS `anneegrilleeval`;
CREATE TABLE IF NOT EXISTS `anneegrilleeval` (
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  PRIMARY KEY (`anneeDebut`,`IdModeleEval`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `anneestage`
--

DROP TABLE IF EXISTS `anneestage`;
CREATE TABLE IF NOT EXISTS `anneestage` (
  `anneeDebut` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  `IdEntreprise` smallint DEFAULT NULL,
  `but3sinon2` tinyint(1) NOT NULL,
  `alternanceBUT3` tinyint(1) NOT NULL,
  `nomMaitreStageApp` varchar(50) DEFAULT NULL,
  `sujet` varchar(200) NOT NULL,
  `noteEntreprise` float DEFAULT NULL,
  `typeMission` varchar(50) DEFAULT NULL,
  `cadreMission` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`anneeDebut`,`IdEtudiant`),
  KEY `IdEtudiant` (`IdEtudiant`),
  KEY `IdEntreprise` (`IdEntreprise`)
) ;

--
-- Déchargement des données de la table `anneestage`
--

INSERT INTO `anneestage` (`anneeDebut`, `IdEtudiant`, `IdEntreprise`, `but3sinon2`, `alternanceBUT3`, `nomMaitreStageApp`, `sujet`, `noteEntreprise`, `typeMission`, `cadreMission`) VALUES
(2024, 1, 11, 0, 0, 'Maitre app CAPGEMINI 2024 ', 'app web ', NULL, 'dev back office', NULL),
(2025, 1, 20, 1, 0, 'maitre app dubois', 'creation d\'une site web', NULL, NULL, NULL),
(2025, 2, 2, 1, 1, 'maitre app Renault', 'application VR avec Unity ', NULL, 'dev 3D ', NULL),
(2025, 3, 3, 0, 0, 'MAitre Stage BUT2  ', 'dev en pyhton - info indus.', NULL, 'programmation', NULL),
(2025, 4, 3, 1, 1, 'Maitre App Toal ', 'solution de simulation ', NULL, 'dev3D & VR', '?'),
(2025, 5, 4, 1, 0, 'maitre stage orange', 'sitre intranet', NULL, 'dev web ', NULL),
(2025, 6, 6, 0, 0, 'maitre app EDF 2025 ', 'dev app mobile ', NULL, 'developpement et UI ', NULL),
(2025, 7, 7, 1, 1, 'MA Dassault 2025 ', 'App RA ', NULL, 'dev c# ', 'Confidentiel  ');

-- --------------------------------------------------------

--
-- Structure de la table `anneesuniversitaires`
--

DROP TABLE IF EXISTS `anneesuniversitaires`;
CREATE TABLE IF NOT EXISTS `anneesuniversitaires` (
  `anneeDebut` smallint NOT NULL,
  `fin` smallint NOT NULL,
  PRIMARY KEY (`anneeDebut`),
  UNIQUE KEY `fin` (`fin`)
) ;

--
-- Déchargement des données de la table `anneesuniversitaires`
--

INSERT INTO `anneesuniversitaires` (`anneeDebut`, `fin`) VALUES
(2022, 2023),
(2023, 2024),
(2024, 2025),
(2025, 2026);

-- --------------------------------------------------------

--
-- Structure de la table `critereseval`
--

DROP TABLE IF EXISTS `critereseval`;
CREATE TABLE IF NOT EXISTS `critereseval` (
  `IdCritere` smallint NOT NULL AUTO_INCREMENT,
  `descLongue` varchar(500) DEFAULT NULL,
  `descCourte` varchar(100) NOT NULL,
  PRIMARY KEY (`IdCritere`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `critereseval`
--

INSERT INTO `critereseval` (`IdCritere`, `descLongue`, `descCourte`) VALUES
(1, 'à remplir uniquement par l\'enseignant responsable du stage : l\'ENSEIGNANT TUTEUR\nRespecter scrupuleusement l\'évaluation fournis par l\'entreprise', 'Evaluation du stage par l\'entreprise'),
(2, 'A titre indicatif : ci-dessous les critères d’évaluation  Niveau de travail demandé / 5 :Le niveau de travail demandé par l entreprise était :\\r\\n 0 Insuffisant 0.5 Très Facile 1 Facile 2 Classique 3 Difficile 4 Très difficile 5 exceptionnel (dépassant le BUT) \\r\\n\\r\\nLa qualité / quantité du travail fourni /5    \\n La qualité et  la quantité du travail fourni par le stagiaire était : 0 Très 0.5 Insuffisant 1 Insuffisant 2 Moyen 3 Bon 4 Très bon 5 exceptionnel (dépassant le BUT)', 'Evaluation du stage par l\'enseignant TUTEUR'),
(3, 'Cette note est le résultat de la grille d\'évaluation du rapport de stage, remplie par l\'enseignant tuteur qui a suivi le stage', 'Evaluation du rapport de stage'),
(4, 'cette note est le résultat des grilles d\'évaluation des stages remplies par les 2 enseignants du jury.  Elle ne devrait pas être modifiée ', 'Evaluation de la soutenance de stage'),
(5, 'Présentation claire de l’entreprise et de son secteur d’activité, des enjeux de la structure, et des missions confiées ainsi que de l’utilité du travail réalisé pour l’entreprise.', 'Contexte et missions'),
(6, 'Le point technique présenté est pertinent, bien expliqué, avec un bon niveau de complexité.', 'Choix du point technique et complexité'),
(7, 'L’étudiant utilise un vocabulaire professionnel précis, adapté au domaine d’activité. Il montre qu’il comprend ce qu’il dit et qu’il maîtrise la thématique abordée. Il sait vulgariser les concepts techniques pour un public non expert. Il sait approfondir son propos face à un public expert.', 'Maîtrise du langage professionnel'),
(8, 'La démonstration doit porter sur un livrable finalisé ou un outil utilisé durant le stage. Elle est en lien avec les missions présentées. Les explications sont claires pendant la manipulation. Le discours est accessible, vulgarisé et appuyé d’exemples concrets. La démonstration est intégrée avec fluidité dans la présentation.', 'Qualité de la démonstration'),
(9, 'La conclusion permet l’identification des compétences techniques et humaines acquises. L’étudiant fait le lien avec son projet professionnel. Une conclusion improvisée sera pénalisée.', 'Développement des compétences et PPP'),
(10, 'Diapositives lisibles, structurées, illustrées (graphiques, images, schémas légendés), sans surcharge. Cohérence visuelle. Pas de fautes récurrentes ou grossières. Aucun texte seul. La diapo conclusion est structurée.', 'Qualité du support visuel'),
(11, 'Le support est utilisé pour illustrer activement les propos (pas un simple décor ni une lecture de texte).', 'Exploitation du support'),
(12, 'Expression fluide, rythme, voix posée, regard, gestuelle, gestion du temps, langage soutenu. Pas d’expression ou de mot familiers.', 'Communication orale'),
(13, 'Posture professionnelle, écoute active, capacité à accueillir les remarques pendant les questions.', 'Attitude et maturité');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `IdEnseignant` smallint NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  PRIMARY KEY (`IdEnseignant`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`IdEnseignant`, `nom`, `prenom`, `mail`, `mdp`) VALUES
(1, 'Martin', 'Sophie', 'sophie.martin@univ-lyon.fr', 'pwdSophie1'),
(2, 'Durand', 'Pierre', 'pierre.durand@univ-lyon.fr', 'pwdPierre2'),
(3, 'Bernard', 'Claire', 'claire.bernard@univ-lyon.fr', 'pwdClaire3'),
(4, 'Petit', 'Julien', 'julien.petit@univ-lyon.fr', 'pwdJulien4'),
(5, 'Robert', 'Isabelle', 'isabelle.robert@univ-lyon.fr', 'pwdIsa5'),
(6, 'Richard', 'Thomas', 'thomas.richard@univ-lyon.fr', 'pwdThomas6'),
(7, 'Durieux', 'Camille', 'camille.durieux@univ-lyon.fr', 'pwdCam7'),
(8, 'Moreau', 'Lucas', 'lucas.moreau@univ-lyon.fr', 'pwdLucas8'),
(9, 'Simon', 'Nathalie', 'nathalie.simon@univ-lyon.fr', 'pwdNath9'),
(10, 'Laurent', 'Antoine', 'antoine.laurent@univ-lyon.fr', 'pwdAntoine10'),
(11, 'Michel', 'Elise', 'elise.michel@univ-lyon.fr', 'pwdElise11'),
(12, 'Garcia', 'David', 'david.garcia@univ-lyon.fr', 'pwdDavid12'),
(13, 'Roux', 'Caroline', 'caroline.roux@univ-lyon.fr', 'pwdCaro13'),
(14, 'Fournier', 'Alexandre', 'alexandre.fournier@univ-lyon.fr', 'pwdAlex14'),
(15, 'Girard', 'Hélène', 'helene.girard@univ-lyon.fr', 'pwdHelene15');

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

DROP TABLE IF EXISTS `entreprises`;
CREATE TABLE IF NOT EXISTS `entreprises` (
  `IdEntreprise` smallint NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `villeE` varchar(50) NOT NULL,
  `codePostal` varchar(6) NOT NULL,
  PRIMARY KEY (`IdEntreprise`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`IdEntreprise`, `nom`, `villeE`, `codePostal`) VALUES
(1, 'Airbus', 'Toulouse', '31000'),
(2, 'Renault', 'Boulogne-Billancourt', '92100'),
(3, 'TotalEnergies', 'Courbevoie', '92400'),
(4, 'Orange', 'Paris', '75015'),
(5, 'SNCF', 'Saint-Denis', '93200'),
(6, 'EDF', 'Paris', '75008'),
(7, 'Dassault Aviation', 'Saint-Cloud', '92210'),
(8, 'Safran', 'Issy-les-Moulineaux', '92130'),
(9, 'Thales', 'La Défense', '92000'),
(10, 'Bouygues Construction', 'Guyancourt', '78280'),
(11, 'Capgemini', 'Paris', '75017'),
(12, 'Engie', 'Courbevoie', '92400'),
(13, 'Suez', 'La Défense', '92000'),
(14, 'Veolia', 'Aubervilliers', '93300'),
(15, 'Saint-Gobain', 'Courbevoie', '92400'),
(16, 'PSA Peugeot Citroën', 'Poissy', '78300'),
(17, 'Michelin', 'Clermont-Ferrand', '63000'),
(18, 'Alstom', 'Saint-Ouen', '93400'),
(19, 'Vinci', 'Rueil-Malmaison', '92500'),
(20, 'AccorHotels', 'Issy-les-Moulineaux', '92130'),
(21, 'Publicis Groupe', 'Paris', '75008'),
(22, 'Société Générale', 'Paris', '75009'),
(23, 'BNP Paribas', 'Paris', '75009'),
(24, 'Crédit Agricole', 'Montrouge', '92120'),
(25, 'AXA', 'Paris', '75008'),
(26, 'La Poste', 'Paris', '75015'),
(27, 'Carrefour', 'Massy', '91300'),
(28, 'Auchan Retail', 'Villeneuve-d’Ascq', '59650'),
(29, 'Decathlon', 'Villeneuve-d’Ascq', '59650'),
(30, 'LVMH', 'Paris', '75008');

-- --------------------------------------------------------

--
-- Structure de la table `etudiantsbut2ou3`
--

DROP TABLE IF EXISTS `etudiantsbut2ou3`;
CREATE TABLE IF NOT EXISTS `etudiantsbut2ou3` (
  `IdEtudiant` smallint NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL,
  PRIMARY KEY (`IdEtudiant`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `etudiantsbut2ou3`
--

INSERT INTO `etudiantsbut2ou3` (`IdEtudiant`, `nom`, `prenom`, `mail`) VALUES
(1, 'Dubois', 'Mathieu', 'mathieu.dubois@etu.univ-lyon.fr'),
(2, 'Lefevre', 'Alice', 'alice.lefevre@etu.univ-lyon.fr'),
(3, 'Morel', 'Hugo', 'hugo.morel@etu.univ-lyon.fr'),
(4, 'Lambert', 'Emma', 'emma.lambert@etu.univ-lyon.fr'),
(5, 'Fontaine', 'Lucas', 'lucas.fontaine@etu.univ-lyon.fr'),
(6, 'Chevalier', 'Sarah', 'sarah.chevalier@etu.univ-lyon.fr'),
(7, 'Blanc', 'Thomas', 'thomas.blanc@etu.univ-lyon.fr'),
(8, 'Guillaume', 'Manon', 'manon.guillaume@etu.univ-lyon.fr'),
(9, 'Legrand', 'Nathan', 'nathan.legrand@etu.univ-lyon.fr'),
(10, 'Marchand', 'Chloé', 'chloe.marchand@etu.univ-lyon.fr'),
(11, 'Perrin', 'Julien', 'julien.perrin@etu.univ-lyon.fr'),
(12, 'Barbier', 'Camille', 'camille.barbier@etu.univ-lyon.fr'),
(13, 'Renard', 'Léo', 'leo.renard@etu.univ-lyon.fr'),
(14, 'Renaud', 'Inès', 'ines.renaud@etu.univ-lyon.fr'),
(15, 'Charles', 'Adrien', 'adrien.charles@etu.univ-lyon.fr'),
(16, 'Moulin', 'Sophie', 'sophie.moulin@etu.univ-lyon.fr'),
(17, 'Lopez', 'Antoine', 'antoine.lopez@etu.univ-lyon.fr'),
(18, 'Garnier', 'Laura', 'laura.garnier@etu.univ-lyon.fr'),
(19, 'Faure', 'Clément', 'clement.faure@etu.univ-lyon.fr'),
(20, 'Andre', 'Eva', 'eva.andre@etu.univ-lyon.fr'),
(21, 'Mercier', 'Alexandre', 'alexandre.mercier@etu.univ-lyon.fr'),
(22, 'Dupuis', 'Lina', 'lina.dupuis@etu.univ-lyon.fr'),
(23, 'Meyer', 'Maxime', 'maxime.meyer@etu.univ-lyon.fr'),
(24, 'Lucas', 'Elodie', 'elodie.lucas@etu.univ-lyon.fr'),
(25, 'Henry', 'Bastien', 'bastien.henry@etu.univ-lyon.fr'),
(26, 'Riviere', 'Amélie', 'amelie.riviere@etu.univ-lyon.fr'),
(27, 'Noel', 'Victor', 'victor.noel@etu.univ-lyon.fr'),
(28, 'Giraud', 'Mélanie', 'melanie.giraud@etu.univ-lyon.fr'),
(29, 'Francois', 'Alexis', 'alexis.francois@etu.univ-lyon.fr'),
(30, 'Collet', 'Justine', 'justine.collet@etu.univ-lyon.fr'),
(31, 'Schmitt', 'Paul', 'paul.schmitt@etu.univ-lyon.fr'),
(32, 'Fernandez', 'Clara', 'clara.fernandez@etu.univ-lyon.fr'),
(33, 'Benoit', 'Arthur', 'arthur.benoit@etu.univ-lyon.fr'),
(34, 'Perrot', 'Amandine', 'amandine.perrot@etu.univ-lyon.fr'),
(35, 'Dupont', 'Hugo', 'hugo.dupont@etu.univ-lyon.fr'),
(36, 'Masson', 'Julie', 'julie.masson@etu.univ-lyon.fr'),
(37, 'Caron', 'Romain', 'romain.caron@etu.univ-lyon.fr'),
(38, 'Pires', 'Sonia', 'sonia.pires@etu.univ-lyon.fr'),
(39, 'Bonnet', 'Quentin', 'quentin.bonnet@etu.univ-lyon.fr'),
(40, 'Colin', 'Aurélie', 'aurelie.colin@etu.univ-lyon.fr'),
(41, 'Rolland', 'Benoît', 'benoit.rolland@etu.univ-lyon.fr'),
(42, 'Olivier', 'Marion', 'marion.olivier@etu.univ-lyon.fr'),
(43, 'Da Silva', 'Kevin', 'kevin.dasilva@etu.univ-lyon.fr'),
(44, 'Hubert', 'Marine', 'marine.hubert@etu.univ-lyon.fr'),
(45, 'Gaillard', 'Samuel', 'samuel.gaillard@etu.univ-lyon.fr'),
(46, 'Brun', 'Charlotte', 'charlotte.brun@etu.univ-lyon.fr'),
(47, 'Baron', 'Florian', 'florian.baron@etu.univ-lyon.fr'),
(48, 'Menard', 'Océane', 'oceane.menard@etu.univ-lyon.fr'),
(49, 'Jacquet', 'Yanis', 'yanis.jacquet@etu.univ-lyon.fr'),
(50, 'Rodriguez', 'Anaïs', 'anais.rodriguez@etu.univ-lyon.fr');

-- --------------------------------------------------------

--
-- Structure de la table `evalanglais`
--

DROP TABLE IF EXISTS `evalanglais`;
CREATE TABLE IF NOT EXISTS `evalanglais` (
  `IdEvalAnglais` smallint NOT NULL AUTO_INCREMENT,
  `dateS` datetime DEFAULT NULL,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(200) DEFAULT NULL,
  `Statut` varchar(15) NOT NULL,
  `IdSalle` varchar(10) NOT NULL,
  `IdEnseignant` smallint NOT NULL,
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  PRIMARY KEY (`IdEvalAnglais`),
  UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  KEY `Statut` (`Statut`),
  KEY `IdSalle` (`IdSalle`),
  KEY `IdEnseignant` (`IdEnseignant`),
  KEY `anneeDebut` (`anneeDebut`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `evalportfolio`
--

DROP TABLE IF EXISTS `evalportfolio`;
CREATE TABLE IF NOT EXISTS `evalportfolio` (
  `IdEvalPortfolio` smallint NOT NULL AUTO_INCREMENT,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  `Statut` varchar(15) NOT NULL,
  PRIMARY KEY (`IdEvalPortfolio`),
  UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  KEY `Statut` (`Statut`),
  KEY `anneeDebut` (`anneeDebut`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `evalrapport`
--

DROP TABLE IF EXISTS `evalrapport`;
CREATE TABLE IF NOT EXISTS `evalrapport` (
  `IdEvalRapport` smallint NOT NULL AUTO_INCREMENT,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(200) DEFAULT NULL,
  `Statut` varchar(15) NOT NULL,
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  PRIMARY KEY (`IdEvalRapport`),
  UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  KEY `Statut` (`Statut`),
  KEY `anneeDebut` (`anneeDebut`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `evalsoutenance`
--

DROP TABLE IF EXISTS `evalsoutenance`;
CREATE TABLE IF NOT EXISTS `evalsoutenance` (
  `IdEvalSoutenance` smallint NOT NULL AUTO_INCREMENT,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  `Statut` varchar(15) NOT NULL,
  PRIMARY KEY (`IdEvalSoutenance`),
  UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  KEY `Statut` (`Statut`),
  KEY `anneeDebut` (`anneeDebut`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `evalstage`
--

DROP TABLE IF EXISTS `evalstage`;
CREATE TABLE IF NOT EXISTS `evalstage` (
  `IdEvalStage` smallint NOT NULL AUTO_INCREMENT,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(200) DEFAULT NULL,
  `presenceMaitreStageApp` tinyint(1) DEFAULT NULL,
  `confidentiel` tinyint(1) DEFAULT NULL,
  `date_h` datetime DEFAULT NULL,
  `IdEnseignantTuteur` smallint NOT NULL,
  `Statut` varchar(15) NOT NULL,
  `IdSecondEnseignant` smallint NOT NULL,
  `anneeDebut` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  `IdEtudiant` smallint NOT NULL,
  `IdSalle` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`IdEvalStage`),
  UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  KEY `IdEnseignantTuteur` (`IdEnseignantTuteur`),
  KEY `Statut` (`Statut`),
  KEY `IdSecondEnseignant` (`IdSecondEnseignant`),
  KEY `anneeDebut` (`anneeDebut`),
  KEY `IdModeleEval` (`IdModeleEval`),
  KEY `IdSalle` (`IdSalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesanglais`
--

DROP TABLE IF EXISTS `lescriteresnotesanglais`;
CREATE TABLE IF NOT EXISTS `lescriteresnotesanglais` (
  `IdCritere` smallint NOT NULL,
  `IdEvalAnglais` smallint NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCritere`,`IdEvalAnglais`),
  KEY `IdEvalAnglais` (`IdEvalAnglais`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesportfolio`
--

DROP TABLE IF EXISTS `lescriteresnotesportfolio`;
CREATE TABLE IF NOT EXISTS `lescriteresnotesportfolio` (
  `IdCritere` smallint NOT NULL,
  `IdEvalPortfolio` smallint NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCritere`,`IdEvalPortfolio`),
  KEY `IdEvalPortfolio` (`IdEvalPortfolio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesrapport`
--

DROP TABLE IF EXISTS `lescriteresnotesrapport`;
CREATE TABLE IF NOT EXISTS `lescriteresnotesrapport` (
  `IdCritere` smallint NOT NULL,
  `IdEvalRapport` smallint NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCritere`,`IdEvalRapport`),
  KEY `IdEvalRapport` (`IdEvalRapport`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotessoutenance`
--

DROP TABLE IF EXISTS `lescriteresnotessoutenance`;
CREATE TABLE IF NOT EXISTS `lescriteresnotessoutenance` (
  `IdCritere` smallint NOT NULL,
  `IdEvalSoutenance` smallint NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCritere`,`IdEvalSoutenance`),
  KEY `IdEvalSoutenance` (`IdEvalSoutenance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesstage`
--

DROP TABLE IF EXISTS `lescriteresnotesstage`;
CREATE TABLE IF NOT EXISTS `lescriteresnotesstage` (
  `IdCritere` smallint NOT NULL,
  `IdEvalStage` smallint NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCritere`,`IdEvalStage`),
  KEY `IdEvalStage` (`IdEvalStage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modelesgrilleeval`
--

DROP TABLE IF EXISTS `modelesgrilleeval`;
CREATE TABLE IF NOT EXISTS `modelesgrilleeval` (
  `IdModeleEval` smallint NOT NULL AUTO_INCREMENT,
  `natureGrille` enum('ANGLAIS','SOUTENANCE','RAPPORT',' STAGE',' PORTFOLIO') NOT NULL,
  `noteMaxGrille` float DEFAULT NULL,
  `nomModuleGrilleEvaluation` varchar(80) NOT NULL,
  `anneeDebut` smallint NOT NULL,
  PRIMARY KEY (`IdModeleEval`),
  UNIQUE KEY `nomModuleGrilleEvaluation` (`nomModuleGrilleEvaluation`),
  KEY `anneeDebut` (`anneeDebut`)
) ;

--
-- Déchargement des données de la table `modelesgrilleeval`
--

INSERT INTO `modelesgrilleeval` (`IdModeleEval`, `natureGrille`, `noteMaxGrille`, `nomModuleGrilleEvaluation`, `anneeDebut`) VALUES
(1, 'SOUTENANCE', 10, 'Grille d\'évaluation des soutenances de stage 2025', 2025),
(2, ' STAGE', 20, 'la grille d\'évaluation de stage BUT2 ou BUT3  2025 2026', 2025);

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

DROP TABLE IF EXISTS `salles`;
CREATE TABLE IF NOT EXISTS `salles` (
  `IdSalle` varchar(10) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdSalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`IdSalle`, `description`) VALUES
('261', 'Salle de TD avec Grand Ecran  Interactif'),
('264', 'Salle de TD&TP  avec Grand Ecran  Interactif'),
('355', 'Salle de TP avec Grand Ecran Tactile'),
('AmphiB1', 'Amphi 90 places avec vidéoprojecteur'),
('T21', 'Salle de TP du plateau technique (BUT3)'),
('T22', 'Salle de TP du plateau technique (BUT3)');

-- --------------------------------------------------------

--
-- Structure de la table `sectioncontenircriteres`
--

DROP TABLE IF EXISTS `sectioncontenircriteres`;
CREATE TABLE IF NOT EXISTS `sectioncontenircriteres` (
  `IdCritere` smallint NOT NULL,
  `IdSection` smallint NOT NULL,
  `ValeurMaxCritereEVal` float NOT NULL,
  PRIMARY KEY (`IdCritere`,`IdSection`),
  KEY `IdSection` (`IdSection`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sectioncontenircriteres`
--

INSERT INTO `sectioncontenircriteres` (`IdCritere`, `IdSection`, `ValeurMaxCritereEVal`) VALUES
(1, 1, 10),
(2, 1, 10),
(3, 1, 10),
(4, 1, 10),
(5, 3, 1),
(6, 3, 1),
(7, 3, 1),
(8, 3, 1),
(9, 3, 1),
(10, 2, 1.5),
(11, 2, 1.5),
(12, 2, 1),
(13, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `sectioncritereeval`
--

DROP TABLE IF EXISTS `sectioncritereeval`;
CREATE TABLE IF NOT EXISTS `sectioncritereeval` (
  `IdSection` smallint NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`IdSection`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sectioncritereeval`
--

INSERT INTO `sectioncritereeval` (`IdSection`, `titre`, `description`) VALUES
(1, 'Evaluation du stage', 'rassembler les 4 critères d\'évaluation : note Entreprise , note Ttueur , note soutance et note rapport, chacune sur 10. La note finale sera une note sur 20'),
(2, 'Forme de la soutenance', 'évaluer la soutenance à travers des critères de forme'),
(3, 'Contenu de la soutenance', 'évaluer la soutenance pour ce qui a été décidé de mettre en avant');

-- --------------------------------------------------------

--
-- Structure de la table `sectionseval`
--

DROP TABLE IF EXISTS `sectionseval`;
CREATE TABLE IF NOT EXISTS `sectionseval` (
  `IdSection` smallint NOT NULL,
  `IdModeleEval` smallint NOT NULL,
  PRIMARY KEY (`IdSection`,`IdModeleEval`),
  KEY `IdModeleEval` (`IdModeleEval`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sectionseval`
--

INSERT INTO `sectionseval` (`IdSection`, `IdModeleEval`) VALUES
(1, 2),
(2, 1),
(3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `statutseval`
--

DROP TABLE IF EXISTS `statutseval`;
CREATE TABLE IF NOT EXISTS `statutseval` (
  `Statut` varchar(15) NOT NULL,
  PRIMARY KEY (`Statut`)
) ;

--
-- Déchargement des données de la table `statutseval`
--

INSERT INTO `statutseval` (`Statut`) VALUES
('BLOQUEE'),
('DIFFUSEE'),
('REMONTEE'),
('SAISIE'),
('VALIDEE');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateursbackoffice`
--

DROP TABLE IF EXISTS `utilisateursbackoffice`;
CREATE TABLE IF NOT EXISTS `utilisateursbackoffice` (
  `Identifiant` smallint NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  PRIMARY KEY (`Identifiant`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
