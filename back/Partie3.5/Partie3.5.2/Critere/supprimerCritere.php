<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connexion échouée : " . $conn->connect_error);

//////////////////////////////////////////// SUPPRESSION CRIT ////////////////////////////////////////////////////////

if (!isset($_GET['id_critere']) || !isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
    die("Erreur : paramètres manquants.");
}

$id_critere = intval($_GET['id_critere']);
$id_section = intval($_GET['id_section']);
$id_grille  = intval($_GET['id_grille']);

// Vérification si la grille est déjà utilisée
include("../Bouton.php");
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour à l'affichage de grille</a>";
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}

// Supprimer d'abord la liaison
$conn->query("DELETE FROM sectioncontenircriteres WHERE IdSection = $id_section AND IdCritere = $id_critere");

// Puis supprimer le critère
$conn->query("DELETE FROM critereseval WHERE IdCritere = $id_critere");

header("Location: ../Affichage.php?id_grille=$id_grille");
exit;
?>
