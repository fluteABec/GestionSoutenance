<?php
// index.php (version de test avec faux id par défaut)

session_start();
// Initialisation du nom du professeur si absent
if (!isset($_SESSION['professeur_nom'])) {
    $_SESSION['professeur_nom'] = 'Professeur'; // Remplace par la vraie valeur si besoin
}
// charge la configuration / modèle / contrôleur
require_once 'config.php';
require_once 'grilleController.php';

$idUser;
$idEtudiant;



// Si l'application gère une session d'authentification, on pourrait préférer $_SESSION.
// Ici on supporte GET pour tester facilement depuis l'URL.
//$idUser     = isset($_GET['idUser']) ? (int)$_GET['idUser'] : null;
if (isset($_SESSION['idUser'])) 
	$idUser = $_SESSION['idUser'];
else
	$idUser = 1;

if (isset($_GET['etudiant_id'])){
	$idEtudiant = $_GET['etudiant_id'];
    $_SESSION['idEtudiant'] = $idEtudiant;
}
// Capture le contenu généré par afficherPageEtudiant
ob_start();
afficherPageEtudiant($idUser, $idEtudiant);
$pageEtudiantContent = ob_get_clean();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Information Étudiant</title>
    <link rel="stylesheet" href="../../stylee.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php include '../headerFront.php'; ?>
<div class="admin-block" style="max-width:900px;width:96%;margin:60px auto 0 auto;box-sizing:border-box;">
    <?php echo $pageEtudiantContent; ?>
    <a href="../Front_PartieA/public/index.php" class="btn-retour mb-3">← Retour</a>
</div>
</body>

<?php


