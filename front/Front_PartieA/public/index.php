<?php
// public/index.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['professeur_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../../../index.html');
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/SoutenanceController.php';

$controller = new SoutenanceController($pdo);
// Utiliser l'ID de l'enseignant connecté
$controller->afficherPageA($_SESSION['professeur_id']);
