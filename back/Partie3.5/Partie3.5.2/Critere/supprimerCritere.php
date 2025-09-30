<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
//echo "Connexion réussie !"; // Test connexion

//////////////////////////////////////////////// SUPPRESSION ///////////////////////////////////////////////////////////////


// Vérifier qu’on a bien reçu les paramètres
if (!isset($_GET['id_critere']) || !isset($_GET['id_section'])) {
    die("Erreur : critère, section ou grille non spécifiée.");
}

$id_critere = intval($_GET['id_critere']);
$id_section = intval($_GET['id_section']);
$id_grille  = intval($_GET['id_grille']);

// Étape 1 : supprimer la liaison section <-> critère
$sql1 = "DELETE FROM sectioncontenircriteres WHERE IdCritere = $id_critere AND IdSection = $id_section";
if (!$conn->query($sql1)) {
    die("Erreur suppression liaison : " . $conn->error);
}

// Étape 2 : supprimer le critère lui-même
$sql2 = "DELETE FROM critereseval WHERE IdCritere = $id_critere";
if (!$conn->query($sql2)) {
    die("Erreur suppression critère : " . $conn->error);
}

echo "✅ Critère supprimé avec succès.";
header("Location: ../Affichage.php?id_grille=$id_grille");
exit; 

$conn->close();
?>