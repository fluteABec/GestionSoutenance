<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'professeur') {
    header("Location: ../index.html");
    exit();
}

// Connexion à la base de données
require_once '../db.php';

// Récupérer l'identifiant du professeur depuis la session
$identifiant = $_SESSION['identifiant'];

// Récupérer le nom et le prénom du professeur depuis la base de données
$sql = "SELECT nom, prenom FROM Enseignants WHERE mail = :identifiant";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':identifiant', $identifiant);
$stmt->execute();
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le professeur a été trouvé
if ($professor) {
    $professorName = $professor['prenom'] . ' ' . $professor['nom'];
} else {
    $professorName = 'Inconnu';
}

// Passer le nom du professeur à la page HTML via une variable JavaScript
echo "<script>var professorName = " . json_encode($professorName) . ";</script>";
?>
