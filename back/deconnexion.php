<?php
// Démarrer la session
session_start();

// Supprimer toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header("Location:../../index.html");
exit();
?>
