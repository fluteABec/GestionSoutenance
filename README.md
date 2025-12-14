# GestionSoutenance

## 📋 Description du projet

Application web de gestion des évaluations de stages pour l'IUT Informatique de Clermont-Ferrand. Ce projet permet de gérer l'ensemble du processus d'évaluation des stages étudiants (BUT2 et BUT3), incluant la création de grilles d'évaluation, la gestion des soutenances, l'attribution des notes et la diffusion des résultats.

## 🎯 Contexte

Projet réalisé dans le cadre de la ressource R3.07 - SAE S3.01 : Développement d'une application  
**Semestre :** 3 (BUT Informatique)  
**Année :** 2024-2025

## 🚀 Fonctionnalités principales

### 🔐 Authentification
- Connexion sécurisée pour les enseignants et administrateurs
- Gestion des sessions utilisateur
- Déconnexion sécurisée

### 👨‍🏫 Espace Administrateur (Back-office)

#### Partie 3.1 - Tableau de bord
- Vue d'ensemble des évaluations
- Statistiques et indicateurs

#### Partie 3.2 - Gestion des soutenances
- Ajout, modification et suppression de soutenances
- Attribution des salles et créneaux horaires
- Affectation des enseignants aux jurys
- Gestion des dates et horaires

#### Partie 3.3 - Remontée des notes
- Saisie et validation des notes
- Export des relevés de notes (CSV)
- Envoi automatique par email aux étudiants
- Historique des évaluations

#### Partie 3.4 - Diffusion des résultats
- Configuration de la visibilité des notes
- Consultation des résultats par type d'évaluation
- Logs des actions de diffusion
- Gestion des commentaires du jury

#### Partie 3.5 - Gestion des grilles d'évaluation
- **3.5.2** : Création et modification de grilles personnalisées
  - Définition des sections et critères
  - Attribution des notes maximales par critère
  - Simulation de notation
  - Copie et réutilisation de grilles
- **3.5.3** : Gestion des modèles de grilles
  - Bibliothèque de grilles prédéfinies
  - Duplication et adaptation de grilles existantes

#### Partie 3.6 - Administration avancée
- Gestion des utilisateurs et droits d'accès
- Configuration système
- Maintenance de la base de données

### 👨‍🎓 Espace Étudiant (Front-office)

#### Page A - Informations personnelles
- Consultation des informations de stage
- Détails du tuteur et de l'entreprise
- Suivi du statut d'évaluation

#### Page B - Grilles d'évaluation
- Visualisation des grilles d'évaluation attribuées
- Consultation des critères détaillés
- Vue par type d'évaluation (Portfolio, Rapport, Soutenance, Stage, Anglais)

#### Page C - Consultation des notes
- Visualisation des notes obtenues
- Détail par critère d'évaluation
- Commentaires du jury
- Historique complet des évaluations

## 🛠️ Technologies utilisées

### Backend
- **PHP 8.x** : Langage serveur principal
- **MySQL** : Base de données relationnelle
- **PHPMailer** : Envoi d'emails automatisés
- **Composer** : Gestionnaire de dépendances PHP

### Frontend
- **HTML5** : Structure des pages
- **CSS3** : Stylisation moderne et responsive
- **JavaScript** : Interactivité côté client
- **DataTables** : Tables de données dynamiques

### Outils et environnement
- **XAMPP** : Serveur de développement local
- **Git** : Gestion de version
- **GitHub** : Hébergement du code source

## 📁 Structure du projet

```
projet_sql-v2/
├── back/                          # Back-office (administration)
│   ├── Partie3.1/                 # Tableau de bord
│   ├── Partie3.2/                 # Gestion des soutenances
│   ├── Partie3.3/                 # Remontée des notes
│   ├── Partie3.4/                 # Diffusion des résultats
│   ├── Partie3.5/                 # Gestion des grilles
│   │   ├── Partie3.5.2/          # Éditeur de grilles
│   │   │   ├── Grille/           # CRUD grilles
│   │   │   ├── Section/          # CRUD sections
│   │   │   └── Critere/          # CRUD critères
│   │   └── Partie3.5.3/          # Gestion modèles
│   ├── Partie3.6/                 # Administration
│   ├── navbar.php                 # Navigation principale
│   ├── navbarAdmin.php            # Navigation admin
│   ├── navbarGrilles.php          # Navigation grilles
│   ├── mainAdministration.php     # Page d'accueil admin
│   └── deconnexion.php            # Déconnexion
├── front/                         # Front-office (étudiants)
│   ├── Front_PartieA/            # Informations personnelles
│   ├── PAGEB/                    # Grilles d'évaluation
│   ├── Page C/                   # Consultation des notes
│   ├── headerFront.php           # En-tête front-office
│   └── front_office.php          # Page d'accueil étudiants
├── SUJET + REQUETES DE TABLES/   # Documentation et SQL
│   ├── SujetR3.07 Evaluation Stages.pdf
│   └── evaluationstages.sql      # Structure de la BDD
├── index.html                     # Page de connexion
├── action.php                     # Traitement authentification
├── db.php                         # Configuration BDD
├── stylee.css                     # Feuille de style principale
└── README.md                      # Documentation

```

## 🗄️ Base de données

### Tables principales

- **enseignants** : Informations des enseignants/jurys
- **etudiantsbut2ou3** : Informations des étudiants
- **modelesgrilleeval** : Modèles de grilles d'évaluation
- **sectionsgrilleeval** : Sections des grilles
- **critereseval** : Critères d'évaluation
- **sectioncontenircriteres** : Liaison sections-critères
- **evalportfolio**, **evalrapport**, **evalsoutenance**, **evalstage**, **evalanglais** : Évaluations par type
- **lescriteresnotesXXX** : Notes attribuées par critère
- **statutseval** : Statuts des évaluations (Planifiée, Réalisée, Diffusée)
- **salles** : Salles de soutenance

## ⚙️ Installation

### Prérequis
- XAMPP (ou LAMP/WAMP/MAMP) avec PHP 8.x et MySQL
- Composer (pour les dépendances PHP)
- Navigateur web moderne

### Étapes d'installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/fluteABec/GestionSoutenance.git
   cd GestionSoutenance
   ```

2. **Configurer la base de données**
   - Créer une base de données `evaluationstages` dans phpMyAdmin
   - Importer le fichier SQL : `SUJET + REQUETES DE TABLES/evaluationstages.sql`

3. **Configurer la connexion à la base**
   - Éditer `db.php` avec vos identifiants MySQL :
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "evaluationstages";
     ```

4. **Installer les dépendances PHP** (pour la partie 3.3)
   ```bash
   cd back/Partie3.3
   composer install
   ```

5. **Configurer PHPMailer** (partie 3.3)
   - Éditer `back/Partie3.3/config/database.php` avec vos paramètres SMTP

6. **Démarrer XAMPP**
   - Lancer Apache et MySQL
   - Accéder à l'application : `http://localhost/GestionSoutenance/`

## 👤 Connexion

### Comptes de test
Les identifiants de connexion sont disponibles dans la base de données (table `enseignants`).

**Format de connexion :**
- Email : adresse email de l'enseignant
- Mot de passe : mot de passe hashé (voir `hash_passwords.php` pour générer de nouveaux mots de passe)

## 🎨 Charte graphique

- **Couleur principale** : Bleu universitaire (#006C82)
- **Couleur secondaire** : Turquoise (#178F96)
- **Couleur accent** : Orange (#FF6E00)
- **Police principale** : Inter, Segoe UI, Roboto
- **Police titres** : Barlow (weights: 400, 600, 700, 900)

## 📝 Fonctionnalités détaillées

### Gestion des grilles (Partie 3.5.2)
- Création de grilles modulaires avec sections et critères
- Attribution de notes maximales par critère
- Simulation de notation avant validation
- Copie de grilles existantes pour modification
- Suppression sécurisée (vérification si grille utilisée)

### Remontée des notes (Partie 3.3)
- Saisie des notes par type d'évaluation
- Validation automatique (notes entre 0 et max)
- Export CSV formaté pour BUT2 et BUT3
- Envoi automatique par email avec PHPMailer
- Historique des actions

### Consultation étudiant (Front)
- Interface épurée et intuitive
- Visualisation des grilles d'évaluation
- Consultation détaillée des notes par critère
- Commentaires du jury accessibles

## 🔒 Sécurité

- Mot de passe hashés (password_hash/verify)
- Sessions PHP sécurisées
- Requêtes préparées (protection SQL injection)
- Validation des données côté serveur
- Gestion des droits d'accès (admin/enseignant/étudiant)

## 📊 Logs et traçabilité

- Logs des actions de diffusion (`back/Partie3.4/logs/actions.log`)
- Logs des envois d'emails (`back/Partie3.4/logs/emails.log`)
- Historique des modifications de grilles

## 🤝 Contributeurs

Projet développé par les étudiants de BUT3 Informatique - IUT Clermont-Ferrand

## 📄 Licence

Projet universitaire - IUT Informatique Clermont-Ferrand

## 📞 Support

Pour toute question ou problème :
- Consulter le sujet : `SUJET + REQUETES DE TABLES/SujetR3.07 Evaluation Stages.pdf`
- Vérifier les logs d'erreurs PHP
- Consulter la documentation de la base de données

---

**Version :** 1.0  
**Dernière mise à jour :** Décembre 2025
