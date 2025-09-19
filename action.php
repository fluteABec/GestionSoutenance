<?php

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
require 'db.php';

// Récupération des données du formulaire
$identifiant = $_POST['identifiant'];
$motdepasse = $_POST['motdepasse'];

// Préparer et exécuter la requête pour vérifier les informations d'identification => connexion 
// Vérifier d'abord dans la table Enseignants
$sql = "SELECT nom, prenom, mail, mdp FROM Enseignants WHERE mail = :identifiant AND mdp = :motdepasse";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':identifiant', $identifiant);
$stmt->bindParam(':motdepasse', $motdepasse);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    session_start();
    $_SESSION['identifiant'] = $row['mail'];
    $_SESSION['professeur_id'] = $row['IdEnseignant'];
    header("Location: front/front_office.php");
    exit();
}

// Si non trouvé dans Enseignants, vérifier dans UtilisateursBackOffice
$sql = "SELECT identifiant, nom, prenom, mail, mdp FROM UtilisateursBackOffice WHERE mail = :identifiant AND mdp = :motdepasse";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':identifiant', $identifiant);
$stmt->bindParam(':motdepasse', $motdepasse);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    session_start();
    $_SESSION['identifiant'] = $row['mail'];
    header("Location: back/mainAdministration.php");
    exit();
}

 else {
// Redirection avec un paramètre d'erreur
header("Location: index.html?error=1");
 }

 exit();
?>