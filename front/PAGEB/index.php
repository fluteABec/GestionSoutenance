<?php
// index.php (version de test avec faux id par défaut)
session_start();    
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

// Affichage informatif pour le debug / test
// echo "<p style='background:#f0f0f0;padding:8px;border:1px solid #ddd'>
//         Mode TEST : idUser = <strong>{$idUser}</strong>, idEtudiant = <strong>{$idEtudiant}</strong>.
//       </p>";

// Appelle la fonction du contrôleur qui affiche la page pour cet étudiant
afficherPageEtudiant($idUser, $idEtudiant);


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
    <a href="../Front_PartieA/public/index.php" class="btn-retour mb-3">← Retour</a>
    <!-- Le contenu dynamique généré par afficherPageEtudiant s'affichera ici -->
</div>
</body>

<?php
// Pour tester d'autres cas, ouvre : index.php?idUser=2&idEtudiant=3


