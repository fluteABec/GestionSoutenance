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


echo "<h2>Grilles</h2>";


$sql = "SELECT * FROM modelesgrilleeval";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nature Grille</th><th>Note Maximale</th><th>Nom Module de Grille</th><th>Année Début</th><th>Actions</th><th>Sections</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["IdModeleEval"] . "</td>";
        echo "<td>" . $row["natureGrille"] . "</td>";
        echo "<td>" . $row["noteMaxGrille"] . "</td>";
        echo "<td>" . $row["nomModuleGrilleEvaluation"] . "</td>";
        echo "<td>" . $row["anneeDebut"] . "</td>";

        echo "<td>";
        echo btnModifier("Grille/modifierGrille.php?id_grille=" . $row['IdModeleEval']) . "<br> <br>"; 
        echo btnSupprimer("Grille/supprimerGrille.php?id_grille=" . $row['IdModeleEval']);
        echo "</td>";

        echo "<td>";
        echo "<a href='Section.php?id_grille=" . $row["IdModeleEval"] . "'>📂 Voir sections</a>";
        echo "</td>";

        echo "</tr>";
    }
    
    echo "</table>";

    echo btnAjouter("Grille/ajouterGrille.php", "Ajouter Grille");

} else {
    echo "Aucune donnée trouvée.";
}

$conn->close();


?>
