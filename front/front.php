<?php
session_start(); // Démarrer la session => permet de sécuriser les pages
if (!isset($_SESSION['role'])) {
    header("Location: ../index.html");
    exit();
}
// Connexion à la base de données
require_once '../db.php';
?>
