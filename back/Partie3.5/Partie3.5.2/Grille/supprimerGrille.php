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


$id_grille = isset($_GET['id_grille']) ? intval($_GET['id_grille']) : 0;
if ($id_grille <= 0) {
    die("Erreur : grille non spécifiée ou invalide.");
}


$id_grille = intval($_GET['id_grille']);

// Vérification si la grille est déjà utilisée
include("../Bouton.php");
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}

// 1. Récupérer toutes les sections de la grille
$sql = "SELECT IdSection FROM sectionseval WHERE IdModeleEval = $id_grille";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_section = $row['IdSection'];

        // 2. Récupérer tous les critères de cette section
        $resCrit = $conn->query("SELECT IdCritere FROM sectioncontenircriteres WHERE IdSection = $id_section");
        $criteres = [];
        if ($resCrit && $resCrit->num_rows > 0) {
            while ($c = $resCrit->fetch_assoc()) {
                $criteres[] = $c['IdCritere'];
            }
        }

        // 3. Supprimer les liaisons section-critères
        $conn->query("DELETE FROM sectioncontenircriteres WHERE IdSection = $id_section");

        // 4. Supprimer les critères
        if (!empty($criteres)) {
            $ids = implode(",", $criteres);
            $conn->query("DELETE FROM critereseval WHERE IdCritere IN ($ids)");
        }

        // 5. Supprimer la liaison section-grille
        $conn->query("DELETE FROM sectionseval WHERE IdSection = $id_section AND IdModeleEval = $id_grille");

        // 6. Supprimer la section
        $conn->query("DELETE FROM sectioncritereeval WHERE IdSection = $id_section");
    }
}

// 7. Enfin supprimer la grille
$conn->query("DELETE FROM modelesgrilleeval WHERE IdModeleEval = $id_grille");

// Retour à la liste des grilles
header("Location: ../Grille.php");
exit;

$conn->close();
?>
