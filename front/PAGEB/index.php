<?php
// index.php (version de test avec faux id par défaut)

// charge la configuration / modèle / contrôleur
require_once 'config.php';
require_once 'grilleController.php';

$idUser;
$idEtudiant;

// Si l'application gère une session d'authentification, on pourrait préférer $_SESSION.
// Ici on supporte GET pour tester facilement depuis l'URL.
//$idUser     = isset($_GET['idUser']) ? (int)$_GET['idUser'] : null;
session_start();
if (isset($_SESSION['identifiant'])) {
	$idUser = $_SESSION['identifiant'];
} else {
	$idUser = 1;
}

// Prioriser le paramètre GET si présent (lien depuis la page A)
if (isset($_GET['etudiant_id'])) {
	$idEtudiant = (int)$_GET['etudiant_id'];
} elseif (isset($_SESSION['etudiant_id'])) {
	// fallback historique (si d'autres pages écrivent dans la session)
	$idEtudiant = (int)$_SESSION['etudiant_id'];
} else {
	$idEtudiant = 1;
}

// Valeurs de test par défaut (à adapter si besoin)
//$DEFAULT_USER_ID     = 1; // faux enseignant / secrétaire
//$DEFAULT_ETUDIANT_ID = 1; // faux étudiant

// Utilise les valeurs par défaut si les paramètres manquent
//if (!$idUser) {
//    $idUser = $DEFAULT_USER_ID;
//}
//if (!$idEtudiant) {
//    $idEtudiant = $DEFAULT_ETUDIANT_ID;
//}
// Appelle la fonction du contrôleur qui affiche la page pour cet étudiant
// (selon ta structure, cette fonction doit exister dans grilleController.php)
afficherPageEtudiant($idUser, $idEtudiant);

// Pour tester d'autres cas, ouvre : index.php?idUser=2&idEtudiant=3
