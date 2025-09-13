<?php
session_start(); //sécurisation
if (!isset($_SESSION['role'])) {
    header("Location: ../index.html");
    exit();
}
// Vérifie le rôle si nécessaire
if (basename(__DIR__) === 'back' && $_SESSION['role'] !== 'secretaire') {
    header("Location: ../index.html");
    exit();
}
if (basename(__DIR__) === 'front' && $_SESSION['role'] !== 'professeur') {
    header("Location: ../index.html");
    exit();
}
?>