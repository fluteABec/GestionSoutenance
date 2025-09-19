-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 16 sep. 2025 à 11:05
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `EvaluationStages`
--

-- --------------------------------------------------------

--
-- Structure de la table `AnneeGrilleEval`
--

CREATE TABLE `AnneeGrilleEval` (
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `AnneeStage`
--

CREATE TABLE `AnneeStage` (
  `anneeDebut` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `IdEntreprise` smallint(6) DEFAULT NULL,
  `but3sinon2` tinyint(1) NOT NULL,
  `alternanceBUT3` tinyint(1) NOT NULL,
  `nomMaitreStageApp` varchar(50) DEFAULT NULL,
  `sujet` varchar(200) NOT NULL,
  `noteEntreprise` float DEFAULT NULL CHECK (`noteEntreprise` > 0.5),
  `typeMission` varchar(50) DEFAULT NULL,
  `cadreMission` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `AnneeStage`
--

INSERT INTO `AnneeStage` (`anneeDebut`, `IdEtudiant`, `IdEntreprise`, `but3sinon2`, `alternanceBUT3`, `nomMaitreStageApp`, `sujet`, `noteEntreprise`, `typeMission`, `cadreMission`) VALUES
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
-- Structure de la table `AnneesUniversitaires`
--

CREATE TABLE `AnneesUniversitaires` (
  `anneeDebut` smallint(6) NOT NULL CHECK (`anneeDebut` > 2020),
  `fin` smallint(6) NOT NULL CHECK (`fin` > `anneeDebut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `AnneesUniversitaires`
--

INSERT INTO `AnneesUniversitaires` (`anneeDebut`, `fin`) VALUES
(2022, 2023),
(2023, 2024),
(2024, 2025),
(2025, 2026);

-- --------------------------------------------------------

--
-- Structure de la table `CriteresEval`
--

CREATE TABLE `CriteresEval` (
  `IdCritere` smallint(6) NOT NULL,
  `descLongue` varchar(500) DEFAULT NULL,
  `descCourte` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CriteresEval`
--

INSERT INTO `CriteresEval` (`IdCritere`, `descLongue`, `descCourte`) VALUES
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
-- Structure de la table `Enseignants`
--

CREATE TABLE `Enseignants` (
  `IdEnseignant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Enseignants`
--

INSERT INTO `Enseignants` (`IdEnseignant`, `nom`, `prenom`, `mail`, `mdp`) VALUES
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
-- Structure de la table `Entreprises`
--

CREATE TABLE `Entreprises` (
  `IdEntreprise` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `villeE` varchar(50) NOT NULL,
  `codePostal` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Entreprises`
--

INSERT INTO `Entreprises` (`IdEntreprise`, `nom`, `villeE`, `codePostal`) VALUES
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
-- Structure de la table `EtudiantsBUT2ou3`
--

CREATE TABLE `EtudiantsBUT2ou3` (
  `IdEtudiant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `EtudiantsBUT2ou3`
--

INSERT INTO `EtudiantsBUT2ou3` (`IdEtudiant`, `nom`, `prenom`, `mail`) VALUES
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
-- Structure de la table `EvalAnglais`
--

CREATE TABLE `EvalAnglais` (
  `IdEvalAnglais` smallint(6) NOT NULL,
  `dateS` datetime DEFAULT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0),
  `commentaireJury` varchar(200) DEFAULT NULL,
  `Statut` varchar(15) NOT NULL,
  `IdSalle` varchar(10) NOT NULL,
  `IdEnseignant` smallint(6) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `EvalPortFolio`
--

CREATE TABLE `EvalPortFolio` (
  `IdEvalPortfolio` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0),
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `Statut` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `EvalRapport`
--

CREATE TABLE `EvalRapport` (
  `IdEvalRapport` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0.5),
  `commentaireJury` varchar(200) DEFAULT NULL,
  `Statut` varchar(15) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `EvalSoutenance`
--

CREATE TABLE `EvalSoutenance` (
  `IdEvalSoutenance` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0),
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `Statut` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `EvalStage`
--

CREATE TABLE `EvalStage` (
  `IdEvalStage` smallint(6) NOT NULL,
  `note` float DEFAULT NULL,
  `commentaireJury` varchar(200) DEFAULT NULL,
  `presenceMaitreStageApp` tinyint(1) DEFAULT NULL,
  `confidentiel` tinyint(1) DEFAULT NULL,
  `date_h` datetime DEFAULT NULL,
  `IdEnseignantTuteur` smallint(6) NOT NULL,
  `Statut` varchar(15) NOT NULL,
  `IdSecondEnseignant` smallint(6) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `IdSalle` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LesCriteresNotesAnglais`
--

CREATE TABLE `LesCriteresNotesAnglais` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalAnglais` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LesCriteresNotesPortFolio`
--

CREATE TABLE `LesCriteresNotesPortFolio` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalPortfolio` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LesCriteresNotesRapport`
--

CREATE TABLE `LesCriteresNotesRapport` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalRapport` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL CHECK (`noteCritere` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LesCriteresNotesSoutenance`
--

CREATE TABLE `LesCriteresNotesSoutenance` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalSoutenance` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LesCriteresNotesStage`
--

CREATE TABLE `LesCriteresNotesStage` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalStage` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ModelesGrilleEval`
--

CREATE TABLE `ModelesGrilleEval` (
  `IdModeleEval` smallint(6) NOT NULL,
  `natureGrille` enum('ANGLAIS','SOUTENANCE','RAPPORT',' STAGE',' PORTFOLIO') NOT NULL,
  `noteMaxGrille` float DEFAULT NULL CHECK (`noteMaxGrille` > 0),
  `nomModuleGrilleEvaluation` varchar(80) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ModelesGrilleEval`
--

INSERT INTO `ModelesGrilleEval` (`IdModeleEval`, `natureGrille`, `noteMaxGrille`, `nomModuleGrilleEvaluation`, `anneeDebut`) VALUES
(1, 'SOUTENANCE', 10, 'Grille d\'évaluation des soutenances de stage 2025', 2025),
(2, ' STAGE', 20, 'la grille d\'évaluation de stage BUT2 ou BUT3  2025 2026', 2025);

-- --------------------------------------------------------

--
-- Structure de la table `Salles`
--

CREATE TABLE `Salles` (
  `IdSalle` varchar(10) NOT NULL,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Salles`
--

INSERT INTO `Salles` (`IdSalle`, `description`) VALUES
('261', 'Salle de TD avec Grand Ecran  Interactif'),
('264', 'Salle de TD&TP  avec Grand Ecran  Interactif'),
('355', 'Salle de TP avec Grand Ecran Tactile'),
('AmphiB1', 'Amphi 90 places avec vidéoprojecteur'),
('T21', 'Salle de TP du plateau technique (BUT3)'),
('T22', 'Salle de TP du plateau technique (BUT3)');

-- --------------------------------------------------------

--
-- Structure de la table `SectionContenirCriteres`
--

CREATE TABLE `SectionContenirCriteres` (
  `IdCritere` smallint(6) NOT NULL,
  `IdSection` smallint(6) NOT NULL,
  `ValeurMaxCritereEVal` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SectionContenirCriteres`
--

INSERT INTO `SectionContenirCriteres` (`IdCritere`, `IdSection`, `ValeurMaxCritereEVal`) VALUES
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
-- Structure de la table `SectionCritereEval`
--

CREATE TABLE `SectionCritereEval` (
  `IdSection` smallint(6) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `description` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SectionCritereEval`
--

INSERT INTO `SectionCritereEval` (`IdSection`, `titre`, `description`) VALUES
(1, 'Evaluation du stage', 'rassembler les 4 critères d\'évaluation : note Entreprise , note Ttueur , note soutance et note rapport, chacune sur 10. La note finale sera une note sur 20'),
(2, 'Forme de la soutenance', 'évaluer la soutenance à travers des critères de forme'),
(3, 'Contenu de la soutenance', 'évaluer la soutenance pour ce qui a été décidé de mettre en avant');

-- nouvelles données
-- un ancienne grille d'évaluation d'anglais de 2023 
-- un ancienne grille d'évaluation de stage  de 2023 
-- une grille de portfolio 2025 2026 (le modele, son unique section  , ses critères .... cf annexe du sujet )
-- une grille de rapport de stage  2025 2026 (le modele, ses 2 sections  , ses critères .... cf annexe du sujet )
-- 3  étudiants en 2023  (un but2 sans stage, un but2 avec stage ,un bu3 avec appretnissage) 


INSERT INTO `modelesgrilleeval` (`IdModeleEval`, `natureGrille`, `noteMaxGrille`, `nomModuleGrilleEvaluation`, `anneeDebut`) VALUES
(4, 'ANGLAIS', 20, 'Avienne grille 2024 2025 (ne devrait plus être utilisée !!)', 2024),
(5, 'STAGE', 2023, 'c\'est une anvienne grille d\'évaluation de stage 2023 ', 2023),
(6, 'PORTFOLIO', 20, 'Grille portfolio  \r\nCréer pour l\'année 2025 2026 ', 2025),
(7, 'RAPPORT', 10, 'Grille d\'évaluation du rapport de stage \r\ncréee pendant l\'annéé 2025 2026 ', 2025);

INSERT INTO `sectioncritereeval` (`IdSection`, `titre`, `description`) VALUES
(5, 'Section eval stage 2023 ', 'une section d\'une acienne grille 2023  ... ne devrait pas apparaitre '),
(6, 'Section EVal Portfolio ', 'unique session de la grille d\'évaluation du portfolio 2025 2026 '),
(7, 'Eval Rapport : Section Contenu 2025 ', 'criteres d\'évaluation sur le contenu du rapport  2025 2026 '),
(8, 'Eval Rapport : Section FORME 2025 ', 'criteres d\'évaluation sur la FORME du rapport  2025 2026 ');


INSERT INTO `sectionseval` (`IdSection`, `IdModeleEval`) VALUES
(5, 5),
(6, 6),
(7, 7),
(8, 7);

INSERT INTO `critereseval` (`IdCritere`, `descLongue`, `descCourte`) VALUES
(17, 'criter eval stage 2023 \r\n\r\nc\'est un critere d\'une ancienne grille . ne devrait pas apparaître . conservé pour historique', 'criter eval stage 2023 '),
(18, 'Présentation claire de l’organisation générale du portfolio (page, d’accueil, catégories, navigation, types de contenus). Explication du lien entre la construction du portfolio et le projet personnel et professionnel de l’étudiant (PPP).', 'Introduction du portfolio'),
(19, 'Utilisation de visuels pertinents (captures, vidéos, schémas…). Mise en page lisible, hiérarchie claire de l\'information, design cohérent avec le projet professionnel', 'Qualité et pertinence du portfolio'),
(20, 'Contexte, objectifs, étapes du projet et outils utilisés expliqués de manière synthétique et structurée.', 'Présentation d’un projet spécifique'),
(21, 'Capacité à identifier, illustrer et articuler les compétences développées (techniques et humaines), avec un lien explicite au PPP.', 'Approche réflexive'),
(22, 'Posture, gestion du temps, articulation, regard, gestuelle. L’étudiant capte l’attention du jury', 'Communication orale'),
(23, 'L’étudiant s’appuie habilement sur son portfolio pour illustrer son propos', 'Appui du support pour la mise en valeur'),
(24, 'Originalité de la présentation ou difficulté dans la réalisation du portfolio', 'BONUS'),
(25, 'Contexte bien posé, objectifs clairs, cheminement annoncé. Conclusion synthétique, bilan et ouverture professionnelle.', 'Pertinence de l’introduction et de la conclusion'),
(26, 'Entreprise bien décrite (structure, organisation, équipe, missions). Place de l\'étudiant clairement identifiée.', 'Présentation du contexte professionnel'),
(27, 'Présentation claire des missions, structuration en partie générale et technique. Problèmes identifiés et solutions argumentées.', 'Qualité de l’analyse expérimentale'),
(28, 'Compétences techniques et humaines identifiées. Lien explicite avec le projet personnel et professionnel.', 'Développement des compétences et PPP'),
(29, 'Qualité des procédés graphiques utilisés (captures d’écran, schémas, images, photos) et pertinence de l’explication associée. Présence d’au moins 10 sources de qualité (ouvrages de référence, articles scientifiques, sources spécialisées). Bibliographie structurée et correctement référencée.', 'Utilisation de procédés graphiques et sources pertinentes'),
(30, 'Présence de tous les éléments attendus : couverture, page de garde, sommaire, tables des illustrations, pagination, remerciements, illustrations légendées, bibliographie, annexes.', 'Respect de la structure demandée'),
(31, 'Titres hiérarchisés, alinéas, texte justifié, présentation agréable. Graphiques et tableaux lisibles, bien insérés et numérotés.', 'Clarté de la mise en page'),
(32, 'Orthographe correcte, syntaxe claire, vocabulaire maîtrisé. Pas de fautes récurrentes ou grossières. Un style d’écriture grossièrement robotique est pénalisé.', 'Langue et orthographe'),
(33, 'Légendes aux illustrations, renvois aux annexes, index/glossaire si nécessaire. Table des illustrations placée après le sommaire', 'Cohérence graphique et rigueur documentaire'),
(34, 'Longueur du texte (20–25 pages hors annexes), format PDF, poids < 10 Mo, rendu via UCA Suivi Stage.', 'Respect des consignes de rendu');


INSERT INTO `sectioncontenircriteres` (`IdCritere`, `IdSection`, `ValeurMaxCritereEVal`) VALUES
(17, 5, 2023),
(18, 6, 3),
(19, 6, 3),
(20, 6, 3),
(21, 6, 3),
(22, 6, 3),
(23, 6, 2),
(24, 6, 2),
(25, 7, 1),
(26, 7, 1),
(27, 7, 2),
(28, 7, 0.5),
(29, 7, 0.5),
(30, 8, 1),
(31, 8, 1),
(32, 8, 1.5),
(33, 8, 1),
(34, 8, 0.5);


INSERT INTO `anneestage` (`anneeDebut`, `IdEtudiant`, `IdEntreprise`, `but3sinon2`, `alternanceBUT3`, `nomMaitreStageApp`, `sujet`, `noteEntreprise`, `typeMission`, `cadreMission`) VALUES
(2023, 40, NULL, 0, 0, NULL, '', NULL, NULL, NULL),
(2023, 41, 1, 0, 0, 'maitre stape airbus 2023 ', 'sujet stage airbus 2023 but2 ', NULL, NULL, 'mission airbus 2023 but2 '),
(2023, 42, 2, 1, 1, 'maitre app renault 2023 but3 app ', 'sujet  app renault 2023 but3 app ', NULL, NULL, 'mission  app renault 2023 but3 app ');

-- --------------------------------------------------------

--
-- Structure de la table `SectionsEval`
--

CREATE TABLE `SectionsEval` (
  `IdSection` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SectionsEval`
--

INSERT INTO `SectionsEval` (`IdSection`, `IdModeleEval`) VALUES
(1, 2),
(2, 1),
(3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `StatutsEval`
--

CREATE TABLE `StatutsEval` (
  `Statut` varchar(15) NOT NULL CHECK (`Statut` in ('SAISIE','BLOQUEE','REMONTEE','VALIDEE','DIFFUSEE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `StatutsEval`
--

INSERT INTO `StatutsEval` (`Statut`) VALUES
('BLOQUEE'),
('DIFFUSEE'),
('REMONTEE'),
('SAISIE'),
('VALIDEE');

-- --------------------------------------------------------

--
-- Structure de la table `UtilisateursBackOffice`
--

CREATE TABLE `UtilisateursBackOffice` (
  `Identifiant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `AnneeGrilleEval`
--
ALTER TABLE `AnneeGrilleEval`
  ADD PRIMARY KEY (`anneeDebut`,`IdModeleEval`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `AnneeStage`
--
ALTER TABLE `AnneeStage`
  ADD PRIMARY KEY (`anneeDebut`,`IdEtudiant`),
  ADD KEY `IdEtudiant` (`IdEtudiant`),
  ADD KEY `IdEntreprise` (`IdEntreprise`);

--
-- Index pour la table `AnneesUniversitaires`
--
ALTER TABLE `AnneesUniversitaires`
  ADD PRIMARY KEY (`anneeDebut`),
  ADD UNIQUE KEY `fin` (`fin`);

--
-- Index pour la table `CriteresEval`
--
ALTER TABLE `CriteresEval`
  ADD PRIMARY KEY (`IdCritere`);

--
-- Index pour la table `Enseignants`
--
ALTER TABLE `Enseignants`
  ADD PRIMARY KEY (`IdEnseignant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `Entreprises`
--
ALTER TABLE `Entreprises`
  ADD PRIMARY KEY (`IdEntreprise`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `EtudiantsBUT2ou3`
--
ALTER TABLE `EtudiantsBUT2ou3`
  ADD PRIMARY KEY (`IdEtudiant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `EvalAnglais`
--
ALTER TABLE `EvalAnglais`
  ADD PRIMARY KEY (`IdEvalAnglais`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `IdSalle` (`IdSalle`),
  ADD KEY `IdEnseignant` (`IdEnseignant`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `EvalPortFolio`
--
ALTER TABLE `EvalPortFolio`
  ADD PRIMARY KEY (`IdEvalPortfolio`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `EvalRapport`
--
ALTER TABLE `EvalRapport`
  ADD PRIMARY KEY (`IdEvalRapport`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `EvalSoutenance`
--
ALTER TABLE `EvalSoutenance`
  ADD PRIMARY KEY (`IdEvalSoutenance`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `EvalStage`
--
ALTER TABLE `EvalStage`
  ADD PRIMARY KEY (`IdEvalStage`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `IdEnseignantTuteur` (`IdEnseignantTuteur`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `IdSecondEnseignant` (`IdSecondEnseignant`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`),
  ADD KEY `IdSalle` (`IdSalle`);

--
-- Index pour la table `LesCriteresNotesAnglais`
--
ALTER TABLE `LesCriteresNotesAnglais`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalAnglais`),
  ADD KEY `IdEvalAnglais` (`IdEvalAnglais`);

--
-- Index pour la table `LesCriteresNotesPortFolio`
--
ALTER TABLE `LesCriteresNotesPortFolio`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalPortfolio`),
  ADD KEY `IdEvalPortfolio` (`IdEvalPortfolio`);

--
-- Index pour la table `LesCriteresNotesRapport`
--
ALTER TABLE `LesCriteresNotesRapport`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalRapport`),
  ADD KEY `IdEvalRapport` (`IdEvalRapport`);

--
-- Index pour la table `LesCriteresNotesSoutenance`
--
ALTER TABLE `LesCriteresNotesSoutenance`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalSoutenance`),
  ADD KEY `IdEvalSoutenance` (`IdEvalSoutenance`);

--
-- Index pour la table `LesCriteresNotesStage`
--
ALTER TABLE `LesCriteresNotesStage`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalStage`),
  ADD KEY `IdEvalStage` (`IdEvalStage`);

--
-- Index pour la table `ModelesGrilleEval`
--
ALTER TABLE `ModelesGrilleEval`
  ADD PRIMARY KEY (`IdModeleEval`),
  ADD UNIQUE KEY `nomModuleGrilleEvaluation` (`nomModuleGrilleEvaluation`),
  ADD KEY `anneeDebut` (`anneeDebut`);

--
-- Index pour la table `Salles`
--
ALTER TABLE `Salles`
  ADD PRIMARY KEY (`IdSalle`);

--
-- Index pour la table `SectionContenirCriteres`
--
ALTER TABLE `SectionContenirCriteres`
  ADD PRIMARY KEY (`IdCritere`,`IdSection`),
  ADD KEY `IdSection` (`IdSection`);

--
-- Index pour la table `SectionCritereEval`
--
ALTER TABLE `SectionCritereEval`
  ADD PRIMARY KEY (`IdSection`);

--
-- Index pour la table `SectionsEval`
--
ALTER TABLE `SectionsEval`
  ADD PRIMARY KEY (`IdSection`,`IdModeleEval`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `StatutsEval`
--
ALTER TABLE `StatutsEval`
  ADD PRIMARY KEY (`Statut`);

--
-- Index pour la table `UtilisateursBackOffice`
--
ALTER TABLE `UtilisateursBackOffice`
  ADD PRIMARY KEY (`Identifiant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `CriteresEval`
--
ALTER TABLE `CriteresEval`
  MODIFY `IdCritere` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `Enseignants`
--
ALTER TABLE `Enseignants`
  MODIFY `IdEnseignant` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `Entreprises`
--
ALTER TABLE `Entreprises`
  MODIFY `IdEntreprise` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `EtudiantsBUT2ou3`
--
ALTER TABLE `EtudiantsBUT2ou3`
  MODIFY `IdEtudiant` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `EvalAnglais`
--
ALTER TABLE `EvalAnglais`
  MODIFY `IdEvalAnglais` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `EvalPortFolio`
--
ALTER TABLE `EvalPortFolio`
  MODIFY `IdEvalPortfolio` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `EvalRapport`
--
ALTER TABLE `EvalRapport`
  MODIFY `IdEvalRapport` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `EvalSoutenance`
--
ALTER TABLE `EvalSoutenance`
  MODIFY `IdEvalSoutenance` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `EvalStage`
--
ALTER TABLE `EvalStage`
  MODIFY `IdEvalStage` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ModelesGrilleEval`
--
ALTER TABLE `ModelesGrilleEval`
  MODIFY `IdModeleEval` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `SectionCritereEval`
--
ALTER TABLE `SectionCritereEval`
  MODIFY `IdSection` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `AnneeGrilleEval`
--
ALTER TABLE `AnneeGrilleEval`
  ADD CONSTRAINT `anneegrilleeval_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `anneegrilleeval_ibfk_2` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`);

--
-- Contraintes pour la table `AnneeStage`
--
ALTER TABLE `AnneeStage`
  ADD CONSTRAINT `anneestage_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `anneestage_ibfk_2` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`),
  ADD CONSTRAINT `anneestage_ibfk_3` FOREIGN KEY (`IdEntreprise`) REFERENCES `Entreprises` (`IdEntreprise`);

--
-- Contraintes pour la table `EvalAnglais`
--
ALTER TABLE `EvalAnglais`
  ADD CONSTRAINT `evalanglais_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `StatutsEval` (`Statut`),
  ADD CONSTRAINT `evalanglais_ibfk_2` FOREIGN KEY (`IdSalle`) REFERENCES `Salles` (`IdSalle`),
  ADD CONSTRAINT `evalanglais_ibfk_3` FOREIGN KEY (`IdEnseignant`) REFERENCES `Enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalanglais_ibfk_4` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalanglais_ibfk_5` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`),
  ADD CONSTRAINT `evalanglais_ibfk_6` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `EvalPortFolio`
--
ALTER TABLE `EvalPortFolio`
  ADD CONSTRAINT `evalportfolio_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `StatutsEval` (`Statut`),
  ADD CONSTRAINT `evalportfolio_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalportfolio_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`),
  ADD CONSTRAINT `evalportfolio_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `EvalRapport`
--
ALTER TABLE `EvalRapport`
  ADD CONSTRAINT `evalrapport_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `StatutsEval` (`Statut`),
  ADD CONSTRAINT `evalrapport_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalrapport_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`),
  ADD CONSTRAINT `evalrapport_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `EvalSoutenance`
--
ALTER TABLE `EvalSoutenance`
  ADD CONSTRAINT `evalsoutenance_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `StatutsEval` (`Statut`),
  ADD CONSTRAINT `evalsoutenance_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalsoutenance_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`),
  ADD CONSTRAINT `evalsoutenance_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `EvalStage`
--
ALTER TABLE `EvalStage`
  ADD CONSTRAINT `evalstage_ibfk_1` FOREIGN KEY (`IdEnseignantTuteur`) REFERENCES `Enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalstage_ibfk_2` FOREIGN KEY (`Statut`) REFERENCES `StatutsEval` (`Statut`),
  ADD CONSTRAINT `evalstage_ibfk_3` FOREIGN KEY (`IdSecondEnseignant`) REFERENCES `Enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalstage_ibfk_4` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalstage_ibfk_5` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`),
  ADD CONSTRAINT `evalstage_ibfk_6` FOREIGN KEY (`IdEtudiant`) REFERENCES `EtudiantsBUT2ou3` (`IdEtudiant`),
  ADD CONSTRAINT `evalstage_ibfk_7` FOREIGN KEY (`IdSalle`) REFERENCES `salles` (`IdSalle`);

--
-- Contraintes pour la table `LesCriteresNotesAnglais`
--
ALTER TABLE `LesCriteresNotesAnglais`
  ADD CONSTRAINT `lescriteresnotesanglais_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesanglais_ibfk_2` FOREIGN KEY (`IdEvalAnglais`) REFERENCES `EvalAnglais` (`IdEvalAnglais`);

--
-- Contraintes pour la table `LesCriteresNotesPortFolio`
--
ALTER TABLE `LesCriteresNotesPortFolio`
  ADD CONSTRAINT `lescriteresnotesportfolio_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesportfolio_ibfk_2` FOREIGN KEY (`IdEvalPortfolio`) REFERENCES `EvalPortFolio` (`IdEvalPortfolio`);

--
-- Contraintes pour la table `LesCriteresNotesRapport`
--
ALTER TABLE `LesCriteresNotesRapport`
  ADD CONSTRAINT `lescriteresnotesrapport_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesrapport_ibfk_2` FOREIGN KEY (`IdEvalRapport`) REFERENCES `EvalRapport` (`IdEvalRapport`);

--
-- Contraintes pour la table `LesCriteresNotesSoutenance`
--
ALTER TABLE `LesCriteresNotesSoutenance`
  ADD CONSTRAINT `lescriteresnotessoutenance_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotessoutenance_ibfk_2` FOREIGN KEY (`IdEvalSoutenance`) REFERENCES `EvalSoutenance` (`IdEvalSoutenance`);

--
-- Contraintes pour la table `LesCriteresNotesStage`
--
ALTER TABLE `LesCriteresNotesStage`
  ADD CONSTRAINT `lescriteresnotesstage_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesstage_ibfk_2` FOREIGN KEY (`IdEvalStage`) REFERENCES `EvalStage` (`IdEvalStage`);

--
-- Contraintes pour la table `ModelesGrilleEval`
--
ALTER TABLE `ModelesGrilleEval`
  ADD CONSTRAINT `modelesgrilleeval_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `AnneesUniversitaires` (`anneeDebut`);

--
-- Contraintes pour la table `SectionContenirCriteres`
--
ALTER TABLE `SectionContenirCriteres`
  ADD CONSTRAINT `sectioncontenircriteres_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `CriteresEval` (`IdCritere`),
  ADD CONSTRAINT `sectioncontenircriteres_ibfk_2` FOREIGN KEY (`IdSection`) REFERENCES `SectionCritereEval` (`IdSection`);

--
-- Contraintes pour la table `SectionsEval`
--
ALTER TABLE `SectionsEval`
  ADD CONSTRAINT `sectionseval_ibfk_1` FOREIGN KEY (`IdSection`) REFERENCES `SectionCritereEval` (`IdSection`),
  ADD CONSTRAINT `sectionseval_ibfk_2` FOREIGN KEY (`IdModeleEval`) REFERENCES `ModelesGrilleEval` (`IdModeleEval`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
