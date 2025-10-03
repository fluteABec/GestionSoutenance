<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

///////////////////////////////////////////////// AJOUTER CRITERE ////////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
        die("Erreur : section ou grille non spécifiée.");
    }

    $id_section = intval($_GET['id_section']);
    $id_grille  = intval($_GET['id_grille']);
    $descLongue = $_POST['descLongue'];
    $descCourte = $_POST['descCourte'];
    $valeurMax  = floatval($_POST['valeurMaxCritereEval']);

// Vérification si la grille est déjà utilisée
include("../Bouton.php");
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour à l'affichage de grille</a>";
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}


    // Étape 1 : insertion dans critereseval
    $sql1 = "INSERT INTO critereseval (descLongue, descCourte) 
             VALUES ('$descLongue', '$descCourte')";
    if ($conn->query($sql1)) {
        $id_critere = $conn->insert_id;

        // Étape 2 : liaison dans sectioncontenircriteres avec la valeur max
        $sql2 = "INSERT INTO sectioncontenircriteres (IdSection, IdCritere, valeurMaxCritereEval) 
                 VALUES ($id_section, $id_critere, $valeurMax)";

        if ($conn->query($sql2)) {
            echo "✅ Critère ajouté avec succès.";
            header("Location: ../Affichage.php?id_grille=$id_grille");
            exit;
        } else {
            echo "Erreur (insertion sectioncontenircriteres) : " . $conn->error;
        }
    } else {
        echo "Erreur (insertion critereseval) : " . $conn->error;
    }
}
?>

<h2>Ajouter un critère</h2>
<form method="POST">
    <label>Description Courte :</label>
    <input type="text" name="descCourte" required>

    <label>Description Longue :</label>
    <input type="text" name="descLongue" required>

    <label>Note maximale :</label>
    <input type="number" step="0.1" name="valeurMaxCritereEval" required>

    <button type="submit">✅ Ajouter</button>
</form>

<?php
$id_grille = intval($_GET['id_grille']);
?>
<br><br><a href='../Affichage.php?id_grille=<?= $id_grille?>'>📂 Retour à l'affichage de grille</a>
<br><a href='../Grille.php'>📂 Retour aux Grilles</a> 
