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

/////////////////////////////////// AFFICHAGE /////////////////////////////////////////////

// Vérifier qu'on a bien reçu l'id_section
if (isset($_GET['id_section'])) {
    $id_section = intval($_GET['id_section']); // sécurisation
    $id_grille  = intval($_GET['id_grille']);

    // Requête pour récupérer les critères liés à cette section
    $sql = "SELECT critereseval.IdCritere, critereseval.descCourte, critereseval.descLongue, sectioncontenircriteres.IdSection
            FROM critereseval
            JOIN sectioncontenircriteres ON critereseval.IdCritere = sectioncontenircriteres.IdCritere
            WHERE sectioncontenircriteres.IdSection = $id_section";

    $result = $conn->query($sql);

    echo "<h2>Critères de la section n°$id_section :</h2>";

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID Critère</th><th>Description Courte</th><th>Description Longue</th><th>Actions</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["IdCritere"] . "</td>";
            echo "<td>" . $row["descCourte"] . "</td>";
            echo "<td>" . $row["descLongue"] . "</td>";

            echo "<td>";
            echo "<a href='Critere/modifierCritere.php?id_critere=" . $row["IdCritere"] . "&id_section=$id_section&id_grille=$id_grille'>✏️ Modifier</a>";
            echo "<br><br><a href='Critere/supprimerCritere.php?id_critere=" . $row["IdCritere"] . 
            "&id_section=$id_section&id_grille=$id_grille' 
            onclick='return confirm(\"Supprimer ce critère ?\")'>🗑️ Supprimer</a>";
            echo "</td>";

            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "Aucun critère trouvé pour cette section.";
    }

    // Bouton pour ajouter un critère
    echo "<a href='Critere/ajouterCritere.php?id_section=$id_section&id_grille=$id_grille'>➕ Ajouter un critère</a><br><br>";
    echo "<a href='Section.php?id_grille=$id_grille'>📂 Retour aux sections</a>";

} else {
    echo "Erreur : aucune section sélectionnée.";
}

echo "<br><a href='Grille.php'>📂 Retour Grilles</a>";

$conn->close();
?>
