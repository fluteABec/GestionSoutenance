-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 03 oct. 2025 à 08:56
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

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

CREATE TABLE `anneegrilleeval` (
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `anneestage`
--

CREATE TABLE `anneestage` (
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
-- Déchargement des données de la table `anneestage`
--

INSERT INTO `anneestage` (`anneeDebut`, `IdEtudiant`, `IdEntreprise`, `but3sinon2`, `alternanceBUT3`, `nomMaitreStageApp`, `sujet`, `noteEntreprise`, `typeMission`, `cadreMission`) VALUES
(2023, 40, NULL, 0, 0, NULL, '', NULL, NULL, NULL),
(2023, 41, 1, 0, 0, 'maitre stape airbus 2023 ', 'sujet stage airbus 2023 but2 ', NULL, NULL, 'mission airbus 2023 but2 '),
(2023, 42, 2, 1, 1, 'maitre app renault 2023 but3 app ', 'sujet  app renault 2023 but3 app ', NULL, NULL, 'mission  app renault 2023 but3 app '),
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

CREATE TABLE `anneesuniversitaires` (
  `anneeDebut` smallint(6) NOT NULL CHECK (`anneeDebut` > 2020),
  `fin` smallint(6) NOT NULL CHECK (`fin` > `anneeDebut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `critereseval` (
  `IdCritere` smallint(6) NOT NULL,
  `descLongue` varchar(500) DEFAULT NULL,
  `descCourte` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 'Posture professionnelle, écoute active, capacité à accueillir les remarques pendant les questions.', 'Attitude et maturité'),
(14, '1pt : PAragraphe succintement le dossier    3pts : presente le theme et le dossier succintement et simplement  4pts : mets en evidence le thème du dossier  6pt : présente le dossier d\'une faon claire et organisée', 'Expression orale en CONTINU'),
(15, '2pt : peut intervenir mais la communication repose sur l\'aide apportée par l\'examinateur   3pt : repond et régit de facon simple mais sans prendre l\'initiative    5pts : s\'implique dans l\'échange   7pt : parvient a faire ressortir de facon convaincante ce qu\'il a compris des documents ', 'Expression Orale en INTERACTION'),
(16, '1pt : est partiellement compréhensible    3pt : s\'exprime dans une langue globalement compréhensible    5pts: s\'exprime dans une langue globalement correcte et intelligible    7pt : s\'exprime dans une langue fluide et correcte', 'compétences linguistiques'),
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

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `IdEnseignant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`IdEnseignant`, `nom`, `prenom`, `mail`, `mdp`) VALUES
(1, 'Martin', 'Sophie', 'sophie.martin@univ-lyon.fr', '$2y$10$7zJYbiSwneTiJZZbqQt/Suw4T6K.3FEeTKDwwcay/7409ZnGoeSIe'),
(2, 'Durand', 'Pierre', 'pierre.durand@univ-lyon.fr', '$2y$10$2n8rsNOA2HySZCuoB7kwO.slzJHv3vK9vcZo/phhEL1tl9GSZ21JS'),
(3, 'Bernard', 'Claire', 'claire.bernard@univ-lyon.fr', '$2y$10$2.ZnsNpXU5vDT7AEZPx.YuDOMxVGEPM4B0EKxQ8BLSMh9Yhjsa4XW'),
(4, 'Petit', 'Julien', 'julien.petit@univ-lyon.fr', '$2y$10$XYIgKI5QUSg12UDVSJfhluKrKpDmiLOGpXre/bOuT6NZvrNp1N4m2'),
(5, 'Robert', 'Isabelle', 'isabelle.robert@univ-lyon.fr', '$2y$10$Z.nyCYVp9s2WJzsJrhroaOlLkj2v3Co8jMzFxSAzva2UVT5xU9.PO'),
(6, 'Richard', 'Thomas', 'thomas.richard@univ-lyon.fr', '$2y$10$lTb7xS1qmUCGSssqVQJRpuVhLtgr78.85/jnZINN.p5GMevlsxyoi'),
(7, 'Durieux', 'Camille', 'camille.durieux@univ-lyon.fr', '$2y$10$CxQDeJR49LzpYXggBqmqDugQ8PvhKWEsclJgzzvYrLPjovi1duS/y'),
(8, 'Moreau', 'Lucas', 'lucas.moreau@univ-lyon.fr', '$2y$10$lfYKqsTpV/3DcDnnfVzIi.jj1xveD1FHLiEwfEDPJPHac6OVW752m'),
(9, 'Simon', 'Nathalie', 'nathalie.simon@univ-lyon.fr', '$2y$10$4uqOPpKspU.ECCQFMlcjFuO/0CmoWwb1bFraPEbfXI6PCpB1OJZ1S'),
(10, 'Laurent', 'Antoine', 'antoine.laurent@univ-lyon.fr', '$2y$10$Swow2okb3rj.WpDUgiNC4Ozey7hUXWUNJh6Jliam9k/LmFxDTlmoC'),
(11, 'Michel', 'Elise', 'elise.michel@univ-lyon.fr', '$2y$10$4fbnkWiVwRximiVMSuaH2OtIN9E1fjeTpGTRoZJGo2nM9TBSj3df2'),
(12, 'Garcia', 'David', 'david.garcia@univ-lyon.fr', '$2y$10$d3BnRJ.kwW9wu3F570YOqexY6NsuMvcN.49RtkvnHadpBTbQ9JXve'),
(13, 'Roux', 'Caroline', 'caroline.roux@univ-lyon.fr', '$2y$10$1qaxduyRbof6Nbd6aH6sJ.lumXj9AdQWoIaGQ4G1d95XrBpy2Ghvm'),
(14, 'Fournier', 'Alexandre', 'alexandre.fournier@univ-lyon.fr', '$2y$10$jPRF6/OzQVTaVRNCY1WQ7O2tQV6hbAjPyvAiN6Bbfvs7q5jSh/DCO'),
(15, 'Girard', 'Hélène', 'helene.girard@univ-lyon.fr', '$2y$10$vR2yaV/ac8Bc6XCvoGh.neE77BeZlJw08h18olDIW7T/mNaNqoR5i');

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

CREATE TABLE `entreprises` (
  `IdEntreprise` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `villeE` varchar(50) NOT NULL,
  `codePostal` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `etudiantsbut2ou3` (
  `IdEtudiant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `evalanglais` (
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

--
-- Déchargement des données de la table `evalanglais`
--

INSERT INTO `evalanglais` (`IdEvalAnglais`, `dateS`, `note`, `commentaireJury`, `Statut`, `IdSalle`, `IdEnseignant`, `anneeDebut`, `IdModeleEval`, `IdEtudiant`) VALUES
(1, '2026-06-21 09:00:46', 9, 'commentaire anglais et 7 ', 'SAISIE', '261', 1, 2025, 3, 7),
(2, '2024-06-23 10:51:05', 18, 'comm anglais but3  2023 ! ', 'DIFFUSEE', '261', 4, 2023, 3, 42),
(3, '2025-01-15 10:15:00', NULL, NULL, 'SAISIE', '264', 3, 2025, 1, 1),
(4, '2025-06-20 14:30:00', 16.5, 'Excellent niveau d\'anglais, très bon oral', 'BLOQUEE', '355', 2, 2025, 3, 2),
(5, '2025-06-21 10:00:00', 14, 'Bon niveau, quelques hésitations', 'BLOQUEE', '261', 3, 2025, 3, 3),
(6, '2025-06-22 09:15:00', 12.5, 'Niveau correct, peut mieux faire', 'SAISIE', '264', 1, 2025, 3, 4),
(7, '2025-06-23 15:45:00', 18, 'Très bon niveau, expression fluide', 'BLOQUEE', '355', 4, 2025, 3, 5),
(8, '2025-06-24 11:30:00', NULL, NULL, 'SAISIE', '261', 2, 2025, 3, 6),
(9, '2025-06-25 08:45:00', 15.5, 'Bonne maîtrise de la langue, accent correct', 'BLOQUEE', '264', 1, 2025, 3, 8),
(10, '2025-06-26 14:20:00', 13, 'Niveau moyen, vocabulaire à enrichir', 'BLOQUEE', '355', 3, 2025, 3, 9),
(11, '2025-06-27 10:30:00', 17, 'Très bon niveau, aisance remarquable', 'BLOQUEE', 'AmphiB1', 2, 2025, 3, 10),
(12, '2025-06-28 16:15:00', NULL, 'En cours d\'évaluation', 'SAISIE', '261', 4, 2025, 3, 11),
(13, '2025-06-29 09:00:00', 14.5, 'Bon niveau général, quelques erreurs mineures', 'BLOQUEE', 'T21', 1, 2025, 3, 12),
(14, '2025-06-30 11:45:00', 16, 'Excellente compréhension, expression claire', 'BLOQUEE', '264', 3, 2025, 3, 13),
(15, '2025-07-01 15:30:00', 12, 'Niveau correct mais manque de fluidité', 'SAISIE', '355', 2, 2025, 3, 14);

-- --------------------------------------------------------

--
-- Structure de la table `evalportfolio`
--

CREATE TABLE `evalportfolio` (
  `IdEvalPortfolio` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0),
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `Statut` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evalportfolio`
--

INSERT INTO `evalportfolio` (`IdEvalPortfolio`, `note`, `commentaireJury`, `anneeDebut`, `IdModeleEval`, `IdEtudiant`, `Statut`) VALUES
(1, 5, 'commentaires portfolio et 7 ', 2025, 6, 7, 'BLOQUEE'),
(2, 10, 'commentaire portfolio BUT2  etu 6', 2025, 6, 6, 'BLOQUEE'),
(3, 10, 'commentaire portfolio BUT2  etu 42  annéé 2023', 2023, 6, 42, 'BLOQUEE'),
(4, 15.5, 'Portfolio très bien structuré, compétences bien démontrées', 2025, 6, 1, 'SAISIE'),
(5, 17, 'Excellent portfolio, réflexion approfondie sur les compétences', 2025, 6, 2, 'BLOQUEE'),
(6, 13.5, 'Portfolio correct, manque quelques éléments de réflexion', 2025, 6, 3, 'BLOQUEE'),
(7, 12, 'Portfolio à améliorer, réflexion superficielle', 2025, 6, 4, 'SAISIE'),
(8, 16, 'Très bon portfolio, analyse pertinente des compétences', 2025, 6, 5, 'BLOQUEE'),
(9, NULL, NULL, 2025, 6, 40, 'SAISIE'),
(10, 14, 'Portfolio bien organisé, bonne progression visible', 2025, 6, 41, 'BLOQUEE'),
(11, 15, 'Portfolio complet, bonne analyse des apprentissages', 2025, 6, 8, 'BLOQUEE'),
(12, 13, 'Portfolio satisfaisant, quelques lacunes dans l\'analyse', 2025, 6, 9, 'BLOQUEE'),
(13, 16.5, 'Excellent travail de synthèse, réflexion maturée', 2025, 6, 10, 'BLOQUEE'),
(14, NULL, 'En cours de finalisation', 2025, 6, 11, 'SAISIE'),
(15, 14.5, 'Bon portfolio, démonstration claire des compétences', 2025, 6, 12, 'BLOQUEE'),
(16, 17.5, 'Portfolio remarquable, analyse très approfondie', 2025, 6, 13, 'BLOQUEE'),
(17, 11.5, 'Portfolio en cours d\'amélioration', 2025, 6, 14, 'SAISIE'),
(18, 15.5, 'Très bon travail, présentation soignée', 2025, 6, 15, 'BLOQUEE'),
(19, 12.5, 'Portfolio correct, peut être enrichi', 2025, 6, 16, 'BLOQUEE'),
(20, 16, 'Portfolio bien construit, bonnes références', 2025, 6, 17, 'BLOQUEE');

-- --------------------------------------------------------

--
-- Structure de la table `evalrapport`
--

CREATE TABLE `evalrapport` (
  `IdEvalRapport` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0.5),
  `commentaireJury` varchar(200) DEFAULT NULL,
  `Statut` varchar(15) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evalrapport`
--

INSERT INTO `evalrapport` (`IdEvalRapport`, `note`, `commentaireJury`, `Statut`, `anneeDebut`, `IdModeleEval`, `IdEtudiant`) VALUES
(1, 5, 'commentaire rapport etu 7', 'VALIDEE', 2025, 7, 7),
(2, 5, 'comm rapport but2  etu 76', 'VALIDEE', 2025, 7, 6),
(3, 10, 'but3 etu42  annee 2023 ', 'BLOQUEE', 2023, 7, 42),
(4, 14.5, 'Rapport bien structuré, analyse technique approfondie', 'BLOQUEE', 2025, 7, 1),
(5, 16, 'Excellent rapport, très bonne analyse des enjeux', 'BLOQUEE', 2025, 7, 2),
(6, 13, 'Rapport correct, manque de détails techniques', 'BLOQUEE', 2025, 7, 3),
(7, 11.5, 'Rapport à améliorer, analyse superficielle', 'SAISIE', 2025, 7, 4),
(8, 15.5, 'Très bon rapport, conclusions pertinentes', 'BLOQUEE', 2025, 7, 5),
(9, NULL, NULL, 'SAISIE', 2025, 7, 40),
(10, 12.5, 'Rapport bien rédigé mais manque d\'originalité', 'BLOQUEE', 2025, 7, 41),
(11, 14, 'Rapport complet, bonne méthodologie appliquée', 'BLOQUEE', 2025, 7, 8),
(12, 13.5, 'Rapport satisfaisant, quelques points à développer', 'BLOQUEE', 2025, 7, 9),
(13, 16.5, 'Excellent rapport, analyse très poussée', 'BLOQUEE', 2025, 7, 10),
(14, NULL, 'Rapport en cours de rédaction', 'SAISIE', 2025, 7, 11),
(15, 15, 'Très bon rapport, présentation claire', 'BLOQUEE', 2025, 7, 12),
(16, 17, 'Rapport exceptionnel, innovation remarquable', 'BLOQUEE', 2025, 7, 13),
(17, 12, 'Rapport en révision, améliorations nécessaires', 'SAISIE', 2025, 7, 14),
(18, 14.5, 'Bon rapport, solutions techniques intéressantes', 'BLOQUEE', 2025, 7, 15),
(19, 13, 'Rapport correct, manque de profondeur', 'BLOQUEE', 2025, 7, 16),
(20, 15.5, 'Très bon travail, analyse pertinente', 'BLOQUEE', 2025, 7, 17);

-- --------------------------------------------------------

--
-- Structure de la table `evalsoutenance`
--

CREATE TABLE `evalsoutenance` (
  `IdEvalSoutenance` smallint(6) NOT NULL,
  `note` float DEFAULT NULL CHECK (`note` > 0),
  `commentaireJury` varchar(500) DEFAULT NULL,
  `anneeDebut` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL,
  `IdEtudiant` smallint(6) NOT NULL,
  `Statut` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evalsoutenance`
--

INSERT INTO `evalsoutenance` (`IdEvalSoutenance`, `note`, `commentaireJury`, `anneeDebut`, `IdModeleEval`, `IdEtudiant`, `Statut`) VALUES
(1, 5, 'commentaire soutenance etudiant 7 ', 2025, 1, 7, 'VALIDEE'),
(2, 6, 'comm soutenance BUT2 Etu 6 ', 2025, 1, 6, 'VALIDEE'),
(3, 7, 'coutenance but3 etu42  annéé 2023 ', 2023, 1, 42, 'BLOQUEE'),
(4, 16, 'Excellente soutenance, présentation claire et maîtrisée', 2025, 1, 1, 'BLOQUEE'),
(5, 17.5, 'Soutenance remarquable, très bonne gestion des questions', 2025, 1, 2, 'BLOQUEE'),
(6, 14, 'Bonne soutenance, quelques hésitations sur les questions', 2025, 1, 3, 'BLOQUEE'),
(7, 12, 'Soutenance correcte mais manque de confiance', 2025, 1, 4, 'SAISIE'),
(8, 15, 'Très bonne présentation, démonstration convaincante', 2025, 1, 5, 'BLOQUEE'),
(9, NULL, NULL, 2025, 1, 40, 'SAISIE'),
(10, 13.5, 'Soutenance bien préparée, réponses satisfaisantes', 2025, 1, 41, 'BLOQUEE');

-- --------------------------------------------------------

--
-- Structure de la table `evalstage`
--

CREATE TABLE `evalstage` (
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

--
-- Déchargement des données de la table `evalstage`
--

INSERT INTO `evalstage` (`IdEvalStage`, `note`, `commentaireJury`, `presenceMaitreStageApp`, `confidentiel`, `date_h`, `IdEnseignantTuteur`, `Statut`, `IdSecondEnseignant`, `anneeDebut`, `IdModeleEval`, `IdEtudiant`, `IdSalle`) VALUES
(1, NULL, 'commentaire eval stage et 7', 0, 0, '2026-06-22 09:32:02', 1, 'SAISIE', 6, 2025, 2, 7, 'T21'),
(2, NULL, 'comm eval stage but2 et 6', 0, 1, '2026-06-24 10:06:00', 13, 'SAISIE', 1, 2025, 5, 6, 'AmphiB1'),
(3, 10, 'stage but3 etu 42 année 2023 ', 1, 1, '2024-07-22 10:59:58', 3, 'DIFFUSEE', 5, 2023, 2, 42, 'T22'),
(4, 15.5, 'Excellent stage, étudiant très impliqué', 1, 0, '2025-06-20 09:00:00', 1, 'BLOQUEE', 2, 2025, 2, 1, '261'),
(5, 17, 'Stage remarquable, très bonne adaptation en entreprise', 1, 0, '2025-06-21 14:30:00', 2, 'BLOQUEE', 3, 2025, 2, 2, '264'),
(6, 13, 'Stage satisfaisant, quelques difficultés au début', 1, 1, '2025-06-22 10:15:00', 3, 'BLOQUEE', 4, 2025, 2, 3, '355'),
(7, 12, 'Stage correct mais manque d\'initiative', 0, 0, '2025-06-23 16:00:00', 4, 'SAISIE', 1, 2025, 2, 4, 'T21'),
(8, 16, 'Très bon stage, excellente intégration équipe', 1, 1, '2025-06-24 11:45:00', 5, 'BLOQUEE', 6, 2025, 2, 5, 'AmphiB1'),
(9, NULL, NULL, NULL, NULL, '2025-06-25 09:30:00', 6, 'SAISIE', 7, 2025, 2, 40, '261'),
(10, 14, 'Bon stage, travail sérieux et régulier', 1, 0, '2025-06-26 15:15:00', 7, 'BLOQUEE', 8, 2025, 2, 41, '264');

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesanglais`
--

CREATE TABLE `lescriteresnotesanglais` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalAnglais` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lescriteresnotesanglais`
--

INSERT INTO `lescriteresnotesanglais` (`IdCritere`, `IdEvalAnglais`, `noteCritere`) VALUES
(14, 1, '4'),
(15, 1, '7'),
(17, 1, '2');

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesportfolio`
--

CREATE TABLE `lescriteresnotesportfolio` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalPortfolio` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lescriteresnotesportfolio`
--

INSERT INTO `lescriteresnotesportfolio` (`IdCritere`, `IdEvalPortfolio`, `noteCritere`) VALUES
(18, 1, '3'),
(18, 2, '0.5'),
(19, 1, '3'),
(19, 2, '0.5'),
(20, 1, '3'),
(20, 2, '0.5'),
(21, 1, '3'),
(21, 2, '0.5'),
(22, 1, '3'),
(22, 2, '0.5'),
(23, 1, '3'),
(23, 2, '0.5'),
(24, 1, '3'),
(24, 2, '0.5');

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesrapport`
--

CREATE TABLE `lescriteresnotesrapport` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalRapport` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL CHECK (`noteCritere` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lescriteresnotesrapport`
--

INSERT INTO `lescriteresnotesrapport` (`IdCritere`, `IdEvalRapport`, `noteCritere`) VALUES
(25, 1, '0.5'),
(25, 2, '0.5'),
(26, 1, '0.5'),
(26, 2, '0.5'),
(27, 1, '0.5'),
(27, 2, '0.5'),
(28, 1, '0.5'),
(28, 2, '0.5'),
(29, 1, '0.5'),
(29, 2, '0.5'),
(30, 1, '0.5'),
(30, 2, '0.5'),
(31, 1, '0.5'),
(31, 2, '0.5'),
(32, 1, '0.5'),
(32, 2, '0.5'),
(33, 1, '0.5'),
(33, 2, '0.5'),
(34, 1, '0.5'),
(34, 2, '0.5');

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotessoutenance`
--

CREATE TABLE `lescriteresnotessoutenance` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalSoutenance` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lescriteresnotessoutenance`
--

INSERT INTO `lescriteresnotessoutenance` (`IdCritere`, `IdEvalSoutenance`, `noteCritere`) VALUES
(5, 1, '0'),
(5, 2, '0.5'),
(6, 1, '0.5'),
(6, 2, '0.5'),
(7, 1, '0.5'),
(7, 2, '0.5'),
(8, 1, '0.5'),
(8, 2, '0.5'),
(9, 1, '0'),
(9, 2, '0.5'),
(10, 1, '0.5'),
(10, 2, '0.5'),
(11, 1, '0.5'),
(11, 2, '0.5'),
(12, 1, '0'),
(12, 2, '0.5'),
(13, 1, '0'),
(13, 2, '0.5');

-- --------------------------------------------------------

--
-- Structure de la table `lescriteresnotesstage`
--

CREATE TABLE `lescriteresnotesstage` (
  `IdCritere` smallint(6) NOT NULL,
  `IdEvalStage` smallint(6) NOT NULL,
  `noteCritere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lescriteresnotesstage`
--

INSERT INTO `lescriteresnotesstage` (`IdCritere`, `IdEvalStage`, `noteCritere`) VALUES
(1, 1, '5'),
(1, 2, '8'),
(2, 1, '5'),
(2, 2, '8'),
(3, 1, '5'),
(3, 2, '8'),
(4, 1, '5'),
(4, 2, '8');

-- --------------------------------------------------------

--
-- Structure de la table `modelesgrilleeval`
--

CREATE TABLE `modelesgrilleeval` (
  `IdModeleEval` smallint(6) NOT NULL,
  `natureGrille` enum('ANGLAIS','SOUTENANCE','RAPPORT',' STAGE',' PORTFOLIO') NOT NULL,
  `noteMaxGrille` float DEFAULT NULL CHECK (`noteMaxGrille` > 0),
  `nomModuleGrilleEvaluation` varchar(80) NOT NULL,
  `anneeDebut` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modelesgrilleeval`
--

INSERT INTO `modelesgrilleeval` (`IdModeleEval`, `natureGrille`, `noteMaxGrille`, `nomModuleGrilleEvaluation`, `anneeDebut`) VALUES
(1, 'SOUTENANCE', 10, 'Grille d\'évaluation des soutenances de stage 2025', 2025),
(2, '', 20, 'la grille d\'évaluation de stage BUT2 ou BUT3  2025 2026', 2025),
(3, 'ANGLAIS', 20, 'Grille Evaluation Soutenance de stage en ANGLAIS 2025 -2026', 2025),
(4, 'ANGLAIS', 20, 'Avienne grille 2024 2025 (ne devrait plus être utilisée !!)', 2024),
(5, '', 2023, 'c\'est une anvienne grille d\'évaluation de stage 2023 ', 2023),
(6, '', 20, 'Grille portfolio  \r\nCréer pour l\'année 2025 2026 ', 2025),
(7, 'RAPPORT', 10, 'Grille d\'évaluation du rapport de stage \r\ncréee pendant l\'annéé 2025 2026 ', 2025);

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

CREATE TABLE `salles` (
  `IdSalle` varchar(10) NOT NULL,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `sectioncontenircriteres` (
  `IdCritere` smallint(6) NOT NULL,
  `IdSection` smallint(6) NOT NULL,
  `ValeurMaxCritereEVal` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 2, 1),
(14, 4, 6),
(15, 4, 7),
(16, 4, 7),
(17, 5, 2),
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

-- --------------------------------------------------------

--
-- Structure de la table `sectioncritereeval`
--

CREATE TABLE `sectioncritereeval` (
  `IdSection` smallint(6) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `description` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sectioncritereeval`
--

INSERT INTO `sectioncritereeval` (`IdSection`, `titre`, `description`) VALUES
(1, 'Evaluation du stage', 'rassembler les 4 critères d\'évaluation : note Entreprise , note Ttueur , note soutance et note rapport, chacune sur 10. La note finale sera une note sur 20'),
(2, 'Forme de la soutenance', 'évaluer la soutenance à travers des critères de forme'),
(3, 'Contenu de la soutenance', 'évaluer la soutenance pour ce qui a été décidé de mettre en avant'),
(4, 'Evaluation Soutenance Anglais ', 'Les critères d\'expression orale en continu, en interaction et les compétences linguistiques '),
(5, 'Section eval stage 2023 ', 'une section d\'une acienne grille 2023  ... ne devrait pas apparaitre '),
(6, 'Section EVal Portfolio ', 'unique session de la grille d\'évaluation du portfolio 2025 2026 '),
(7, 'Eval Rapport : Section Contenu 2025 ', 'criteres d\'évaluation sur le contenu du rapport  2025 2026 '),
(8, 'Eval Rapport : Section FORME 2025 ', 'criteres d\'évaluation sur la FORME du rapport  2025 2026 ');

-- --------------------------------------------------------

--
-- Structure de la table `sectionseval`
--

CREATE TABLE `sectionseval` (
  `IdSection` smallint(6) NOT NULL,
  `IdModeleEval` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sectionseval`
--

INSERT INTO `sectionseval` (`IdSection`, `IdModeleEval`) VALUES
(1, 2),
(2, 1),
(3, 1),
(4, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 7);

-- --------------------------------------------------------

--
-- Structure de la table `statutseval`
--

CREATE TABLE `statutseval` (
  `Statut` varchar(15) NOT NULL CHECK (`Statut` in ('SAISIE','BLOQUEE','REMONTEE','VALIDEE','DIFFUSEE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `utilisateursbackoffice` (
  `Identifiant` smallint(6) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateursbackoffice`
--

INSERT INTO `utilisateursbackoffice` (`Identifiant`, `nom`, `prenom`, `mail`, `mdp`) VALUES
(1, 'Mi', 'Coton', 'micoton@etu.uca.fr', '$2y$10$7UwNWvqLwfAqt1R51Frod.zXGD.pUc8.7HqpCqtS0gJvXGw2jxT56'),
(2, 'admin', 'admin', 'Admin@gmail.com', '123456');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_taches_enseignants`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vue_taches_enseignants` (
`IdEnseignant` smallint(6)
,`NomEnseignant` varchar(50)
,`PrenomEnseignant` varchar(50)
,`IdEtudiant` smallint(6)
,`NomEtudiant` varchar(50)
,`PrenomEtudiant` varchar(50)
,`TypeEvaluation` varchar(19)
,`Statut` varchar(15)
,`DateEval` datetime
,`Salle` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure de la vue `vue_taches_enseignants`
--
DROP TABLE IF EXISTS `vue_taches_enseignants`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_taches_enseignants`  AS SELECT `e`.`IdEnseignant` AS `IdEnseignant`, `e`.`nom` AS `NomEnseignant`, `e`.`prenom` AS `PrenomEnseignant`, `et`.`IdEtudiant` AS `IdEtudiant`, `et`.`nom` AS `NomEtudiant`, `et`.`prenom` AS `PrenomEtudiant`, 'Soutenance de Stage' AS `TypeEvaluation`, `ev`.`Statut` AS `Statut`, `ev`.`date_h` AS `DateEval`, `s`.`description` AS `Salle` FROM (((`evalstage` `ev` join `enseignants` `e` on(`ev`.`IdEnseignantTuteur` = `e`.`IdEnseignant`)) join `etudiantsbut2ou3` `et` on(`ev`.`IdEtudiant` = `et`.`IdEtudiant`)) join `salles` `s` on(`ev`.`IdSalle` = `s`.`IdSalle`))union select NULL AS `NULL`,NULL AS `NULL`,NULL AS `NULL`,`et`.`IdEtudiant` AS `IdEtudiant`,`et`.`nom` AS `nom`,`et`.`prenom` AS `prenom`,'Rapport' AS `TypeEvaluation`,`ev`.`Statut` AS `Statut`,NULL AS `DateEval`,NULL AS `Salle` from (`evalrapport` `ev` join `etudiantsbut2ou3` `et` on(`ev`.`IdEtudiant` = `et`.`IdEtudiant`)) union select NULL AS `NULL`,NULL AS `NULL`,NULL AS `NULL`,`et`.`IdEtudiant` AS `IdEtudiant`,`et`.`nom` AS `nom`,`et`.`prenom` AS `prenom`,'Portfolio' AS `TypeEvaluation`,`ev`.`Statut` AS `Statut`,NULL AS `DateEval`,NULL AS `Salle` from (`evalportfolio` `ev` join `etudiantsbut2ou3` `et` on(`ev`.`IdEtudiant` = `et`.`IdEtudiant`)) union select NULL AS `NULL`,NULL AS `NULL`,NULL AS `NULL`,`et`.`IdEtudiant` AS `IdEtudiant`,`et`.`nom` AS `nom`,`et`.`prenom` AS `prenom`,'Soutenance' AS `TypeEvaluation`,`ev`.`Statut` AS `Statut`,NULL AS `DateEval`,NULL AS `Salle` from (`evalsoutenance` `ev` join `etudiantsbut2ou3` `et` on(`ev`.`IdEtudiant` = `et`.`IdEtudiant`)) union select `e`.`IdEnseignant` AS `IdEnseignant`,`e`.`nom` AS `nom`,`e`.`prenom` AS `prenom`,`et`.`IdEtudiant` AS `IdEtudiant`,`et`.`nom` AS `nom`,`et`.`prenom` AS `prenom`,'Anglais' AS `TypeEvaluation`,`ev`.`Statut` AS `Statut`,`ev`.`dateS` AS `DateEval`,`s`.`description` AS `Salle` from (((`evalanglais` `ev` join `enseignants` `e` on(`ev`.`IdEnseignant` = `e`.`IdEnseignant`)) join `etudiantsbut2ou3` `et` on(`ev`.`IdEtudiant` = `et`.`IdEtudiant`)) join `salles` `s` on(`ev`.`IdSalle` = `s`.`IdSalle`))  ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `anneegrilleeval`
--
ALTER TABLE `anneegrilleeval`
  ADD PRIMARY KEY (`anneeDebut`,`IdModeleEval`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `anneestage`
--
ALTER TABLE `anneestage`
  ADD PRIMARY KEY (`anneeDebut`,`IdEtudiant`),
  ADD KEY `IdEtudiant` (`IdEtudiant`),
  ADD KEY `IdEntreprise` (`IdEntreprise`);

--
-- Index pour la table `anneesuniversitaires`
--
ALTER TABLE `anneesuniversitaires`
  ADD PRIMARY KEY (`anneeDebut`),
  ADD UNIQUE KEY `fin` (`fin`);

--
-- Index pour la table `critereseval`
--
ALTER TABLE `critereseval`
  ADD PRIMARY KEY (`IdCritere`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`IdEnseignant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`IdEntreprise`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `etudiantsbut2ou3`
--
ALTER TABLE `etudiantsbut2ou3`
  ADD PRIMARY KEY (`IdEtudiant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `evalanglais`
--
ALTER TABLE `evalanglais`
  ADD PRIMARY KEY (`IdEvalAnglais`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `IdSalle` (`IdSalle`),
  ADD KEY `IdEnseignant` (`IdEnseignant`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `evalportfolio`
--
ALTER TABLE `evalportfolio`
  ADD PRIMARY KEY (`IdEvalPortfolio`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `evalrapport`
--
ALTER TABLE `evalrapport`
  ADD PRIMARY KEY (`IdEvalRapport`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `evalsoutenance`
--
ALTER TABLE `evalsoutenance`
  ADD PRIMARY KEY (`IdEvalSoutenance`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `evalstage`
--
ALTER TABLE `evalstage`
  ADD PRIMARY KEY (`IdEvalStage`),
  ADD UNIQUE KEY `IdEtudiant` (`IdEtudiant`,`IdModeleEval`,`anneeDebut`),
  ADD KEY `IdEnseignantTuteur` (`IdEnseignantTuteur`),
  ADD KEY `Statut` (`Statut`),
  ADD KEY `IdSecondEnseignant` (`IdSecondEnseignant`),
  ADD KEY `anneeDebut` (`anneeDebut`),
  ADD KEY `IdModeleEval` (`IdModeleEval`),
  ADD KEY `IdSalle` (`IdSalle`);

--
-- Index pour la table `lescriteresnotesanglais`
--
ALTER TABLE `lescriteresnotesanglais`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalAnglais`),
  ADD KEY `IdEvalAnglais` (`IdEvalAnglais`);

--
-- Index pour la table `lescriteresnotesportfolio`
--
ALTER TABLE `lescriteresnotesportfolio`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalPortfolio`),
  ADD KEY `IdEvalPortfolio` (`IdEvalPortfolio`);

--
-- Index pour la table `lescriteresnotesrapport`
--
ALTER TABLE `lescriteresnotesrapport`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalRapport`),
  ADD KEY `IdEvalRapport` (`IdEvalRapport`);

--
-- Index pour la table `lescriteresnotessoutenance`
--
ALTER TABLE `lescriteresnotessoutenance`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalSoutenance`),
  ADD KEY `IdEvalSoutenance` (`IdEvalSoutenance`);

--
-- Index pour la table `lescriteresnotesstage`
--
ALTER TABLE `lescriteresnotesstage`
  ADD PRIMARY KEY (`IdCritere`,`IdEvalStage`),
  ADD KEY `IdEvalStage` (`IdEvalStage`);

--
-- Index pour la table `modelesgrilleeval`
--
ALTER TABLE `modelesgrilleeval`
  ADD PRIMARY KEY (`IdModeleEval`),
  ADD UNIQUE KEY `nomModuleGrilleEvaluation` (`nomModuleGrilleEvaluation`),
  ADD KEY `anneeDebut` (`anneeDebut`);

--
-- Index pour la table `salles`
--
ALTER TABLE `salles`
  ADD PRIMARY KEY (`IdSalle`);

--
-- Index pour la table `sectioncontenircriteres`
--
ALTER TABLE `sectioncontenircriteres`
  ADD PRIMARY KEY (`IdCritere`,`IdSection`),
  ADD KEY `IdSection` (`IdSection`);

--
-- Index pour la table `sectioncritereeval`
--
ALTER TABLE `sectioncritereeval`
  ADD PRIMARY KEY (`IdSection`);

--
-- Index pour la table `sectionseval`
--
ALTER TABLE `sectionseval`
  ADD PRIMARY KEY (`IdSection`,`IdModeleEval`),
  ADD KEY `IdModeleEval` (`IdModeleEval`);

--
-- Index pour la table `statutseval`
--
ALTER TABLE `statutseval`
  ADD PRIMARY KEY (`Statut`);

--
-- Index pour la table `utilisateursbackoffice`
--
ALTER TABLE `utilisateursbackoffice`
  ADD PRIMARY KEY (`Identifiant`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `critereseval`
--
ALTER TABLE `critereseval`
  MODIFY `IdCritere` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `IdEnseignant` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `IdEntreprise` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `etudiantsbut2ou3`
--
ALTER TABLE `etudiantsbut2ou3`
  MODIFY `IdEtudiant` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `evalanglais`
--
ALTER TABLE `evalanglais`
  MODIFY `IdEvalAnglais` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `evalportfolio`
--
ALTER TABLE `evalportfolio`
  MODIFY `IdEvalPortfolio` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `evalrapport`
--
ALTER TABLE `evalrapport`
  MODIFY `IdEvalRapport` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `evalsoutenance`
--
ALTER TABLE `evalsoutenance`
  MODIFY `IdEvalSoutenance` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `evalstage`
--
ALTER TABLE `evalstage`
  MODIFY `IdEvalStage` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `modelesgrilleeval`
--
ALTER TABLE `modelesgrilleeval`
  MODIFY `IdModeleEval` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `sectioncritereeval`
--
ALTER TABLE `sectioncritereeval`
  MODIFY `IdSection` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `anneegrilleeval`
--
ALTER TABLE `anneegrilleeval`
  ADD CONSTRAINT `anneegrilleeval_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `anneegrilleeval_ibfk_2` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`);

--
-- Contraintes pour la table `anneestage`
--
ALTER TABLE `anneestage`
  ADD CONSTRAINT `anneestage_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `anneestage_ibfk_2` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`),
  ADD CONSTRAINT `anneestage_ibfk_3` FOREIGN KEY (`IdEntreprise`) REFERENCES `entreprises` (`IdEntreprise`);

--
-- Contraintes pour la table `evalanglais`
--
ALTER TABLE `evalanglais`
  ADD CONSTRAINT `evalanglais_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `statutseval` (`Statut`),
  ADD CONSTRAINT `evalanglais_ibfk_2` FOREIGN KEY (`IdSalle`) REFERENCES `salles` (`IdSalle`),
  ADD CONSTRAINT `evalanglais_ibfk_3` FOREIGN KEY (`IdEnseignant`) REFERENCES `enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalanglais_ibfk_4` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalanglais_ibfk_5` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`),
  ADD CONSTRAINT `evalanglais_ibfk_6` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `evalportfolio`
--
ALTER TABLE `evalportfolio`
  ADD CONSTRAINT `evalportfolio_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `statutseval` (`Statut`),
  ADD CONSTRAINT `evalportfolio_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalportfolio_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`),
  ADD CONSTRAINT `evalportfolio_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `evalrapport`
--
ALTER TABLE `evalrapport`
  ADD CONSTRAINT `evalrapport_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `statutseval` (`Statut`),
  ADD CONSTRAINT `evalrapport_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalrapport_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`),
  ADD CONSTRAINT `evalrapport_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `evalsoutenance`
--
ALTER TABLE `evalsoutenance`
  ADD CONSTRAINT `evalsoutenance_ibfk_1` FOREIGN KEY (`Statut`) REFERENCES `statutseval` (`Statut`),
  ADD CONSTRAINT `evalsoutenance_ibfk_2` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalsoutenance_ibfk_3` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`),
  ADD CONSTRAINT `evalsoutenance_ibfk_4` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`);

--
-- Contraintes pour la table `evalstage`
--
ALTER TABLE `evalstage`
  ADD CONSTRAINT `evalstage_ibfk_1` FOREIGN KEY (`IdEnseignantTuteur`) REFERENCES `enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalstage_ibfk_2` FOREIGN KEY (`Statut`) REFERENCES `statutseval` (`Statut`),
  ADD CONSTRAINT `evalstage_ibfk_3` FOREIGN KEY (`IdSecondEnseignant`) REFERENCES `enseignants` (`IdEnseignant`),
  ADD CONSTRAINT `evalstage_ibfk_4` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`),
  ADD CONSTRAINT `evalstage_ibfk_5` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`),
  ADD CONSTRAINT `evalstage_ibfk_6` FOREIGN KEY (`IdEtudiant`) REFERENCES `etudiantsbut2ou3` (`IdEtudiant`),
  ADD CONSTRAINT `evalstage_ibfk_7` FOREIGN KEY (`IdSalle`) REFERENCES `salles` (`IdSalle`);

--
-- Contraintes pour la table `lescriteresnotesanglais`
--
ALTER TABLE `lescriteresnotesanglais`
  ADD CONSTRAINT `lescriteresnotesanglais_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesanglais_ibfk_2` FOREIGN KEY (`IdEvalAnglais`) REFERENCES `evalanglais` (`IdEvalAnglais`);

--
-- Contraintes pour la table `lescriteresnotesportfolio`
--
ALTER TABLE `lescriteresnotesportfolio`
  ADD CONSTRAINT `lescriteresnotesportfolio_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesportfolio_ibfk_2` FOREIGN KEY (`IdEvalPortfolio`) REFERENCES `evalportfolio` (`IdEvalPortfolio`);

--
-- Contraintes pour la table `lescriteresnotesrapport`
--
ALTER TABLE `lescriteresnotesrapport`
  ADD CONSTRAINT `lescriteresnotesrapport_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesrapport_ibfk_2` FOREIGN KEY (`IdEvalRapport`) REFERENCES `evalrapport` (`IdEvalRapport`);

--
-- Contraintes pour la table `lescriteresnotessoutenance`
--
ALTER TABLE `lescriteresnotessoutenance`
  ADD CONSTRAINT `lescriteresnotessoutenance_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotessoutenance_ibfk_2` FOREIGN KEY (`IdEvalSoutenance`) REFERENCES `evalsoutenance` (`IdEvalSoutenance`);

--
-- Contraintes pour la table `lescriteresnotesstage`
--
ALTER TABLE `lescriteresnotesstage`
  ADD CONSTRAINT `lescriteresnotesstage_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `lescriteresnotesstage_ibfk_2` FOREIGN KEY (`IdEvalStage`) REFERENCES `evalstage` (`IdEvalStage`);

--
-- Contraintes pour la table `modelesgrilleeval`
--
ALTER TABLE `modelesgrilleeval`
  ADD CONSTRAINT `modelesgrilleeval_ibfk_1` FOREIGN KEY (`anneeDebut`) REFERENCES `anneesuniversitaires` (`anneeDebut`);

--
-- Contraintes pour la table `sectioncontenircriteres`
--
ALTER TABLE `sectioncontenircriteres`
  ADD CONSTRAINT `sectioncontenircriteres_ibfk_1` FOREIGN KEY (`IdCritere`) REFERENCES `critereseval` (`IdCritere`),
  ADD CONSTRAINT `sectioncontenircriteres_ibfk_2` FOREIGN KEY (`IdSection`) REFERENCES `sectioncritereeval` (`IdSection`);

--
-- Contraintes pour la table `sectionseval`
--
ALTER TABLE `sectionseval`
  ADD CONSTRAINT `sectionseval_ibfk_1` FOREIGN KEY (`IdSection`) REFERENCES `sectioncritereeval` (`IdSection`),
  ADD CONSTRAINT `sectionseval_ibfk_2` FOREIGN KEY (`IdModeleEval`) REFERENCES `modelesgrilleeval` (`IdModeleEval`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
