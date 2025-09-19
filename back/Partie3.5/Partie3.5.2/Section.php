<?php
include("Bouton.php");


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


/////////////////////////////////// AFFICHAGE /////////////////////////////////////////////


// Vérifier qu'on a bien reçu l'id de la grille
if (isset($_GET['id_grille'])) {
    $id_grille = intval($_GET['id_grille']); // sécurisation (intval)

    // Requête pour récupérer les sections liées à cette grille
    $sql = "SELECT * FROM `sectioncritereEval`
            JOIN sectionseval ON sectioncritereeval.IdSection = sectionseval.IdSection
            WHERE IdModeleEval = $id_grille";
    $result = $conn->query($sql);

    echo "<h2>Sections de la grille n°$id_grille</h2>";

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th> ID Modele Grille</th><th>ID Section</th><th>Titre</th><th>Description</th><th>Actions</th><th>Critères</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";

            echo "<td>" . $row["IdModeleEval"] . "</td>";
            echo "<td>" . $row["IdSection"] . "</td>";
            echo "<td>" . $row["titre"] . "</td>";
            echo "<td>" . $row["description"] .  "</td>";

            echo "<td>";
            echo btnModifier("Section/modifierSection.php?id_section=" . $row["IdSection"] . "&id_grille=$id_grille", "Modifier"). "<br> <br>";
            echo "<a href='Section/supprimerSection.php?id_section=" . $row["IdSection"] . "&id_grille=$id_grille' 
            onclick='return confirm(\"Supprimer cette section avec tous ses critères ?\")'>🗑️ Supprimer</a>";

            echo "</td>";

            echo "<td>";
            echo "<a href='Critere.php?id_section=" . $row["IdSection"] . "&id_grille=$id_grille'>📂 Voir Critères</a>";
            echo "</td>";

            echo "</tr>";
        }
        
        echo "</table>";


    } else {
        echo "Aucune section trouvée pour cette grille.";
    } 
} else {
    echo "Erreur : aucune grille sélectionnée.";
}

echo btnAjouter("Section/ajouterSection.php?id_grille=$id_grille", "Ajouter Section");
echo "<br><br><a href='Grille.php'>📂 Retour Grilles</a>";

$conn->close();