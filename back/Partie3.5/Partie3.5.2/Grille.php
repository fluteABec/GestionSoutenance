
<?php
include("Bouton.php");

$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Grilles</title>
    <link rel="stylesheet" href="../../../stylee.css">
</head>
<body>
    <?php include("../../navbarGrilles.php"); ?>

    <div class="admin-block">
        <h1 style="margin-bottom:24px;">Gestion des Grilles</h1>
        <h2 class='section-title'>Grilles disponibles</h2>
        <?php
        $sql = "SELECT * FROM modelesgrilleeval";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='styled-table'>";
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
            echo "<p class='no-data'>Aucune grille trouvée.</p>";
        }
        $conn->close();
        ?>
        <div style="margin-top:32px;">
            <a href="../../mainAdministration.php" class="btn">← Retour Administration</a>
        </div>
    </div>
</body>
</html>
