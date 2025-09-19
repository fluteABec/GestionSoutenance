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


if (!isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
    die("Erreur : section ou grille non spécifiée.");
}

$id_section = intval($_GET['id_section']);
$id_grille  = intval($_GET['id_grille']);

// 1. Récupérer les critères liés à cette section
$sql = "SELECT IdCritere FROM sectioncontenircriteres WHERE IdSection = $id_section";
$result = $conn->query($sql);

$criteres = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $criteres[] = $row['IdCritere'];
    }
}

// 2. Supprimer d’abord les liaisons section-critères
$conn->query("DELETE FROM sectioncontenircriteres WHERE IdSection = $id_section");

// 3. Supprimer ensuite les critères eux-mêmes (s’ils ne sont pas utilisés ailleurs)
if (!empty($criteres)) {
    $ids = implode(",", $criteres);
    $conn->query("DELETE FROM critereseval WHERE IdCritere IN ($ids)");
}

// 4. Supprimer la liaison section-grille
$conn->query("DELETE FROM sectionseval WHERE IdSection = $id_section AND IdModeleEval = $id_grille");

// 5. Supprimer la section
$conn->query("DELETE FROM sectioncritereeval WHERE IdSection = $id_section");

// Retour aux sections de la grille
header("Location: ../Section.php?id_grille=$id_grille");
exit;

$conn->close();
?>