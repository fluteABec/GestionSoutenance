<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

///////////////////////////////////////////////// AJOUTER ////////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nature = $_POST['natureGrille'];
    $note   = $_POST['noteMaxGrille'];
    $nom    = $_POST['nomModuleGrilleEvaluation'];
    $annee  = $_POST['anneeDebut'];  // récupère l'année choisie dans la liste

    // Insertion dans la table
    $sql = "INSERT INTO ModelesGrilleEval (natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut) 
            VALUES ('$nature', '$note', '$nom', '$annee')";

    if ($conn->query($sql)) {
        echo "✅ Grille ajoutée avec succès.";
        header("Location: ../Grille.php");
        exit;
    } else {
        echo "❌ Erreur SQL : " . $conn->error;
    }
}
?>

<h2>➕ Ajouter une grille</h2>
<form method="POST">

    <!-- garder l'id en hidden pour le POST -->
        <input type="hidden" name="id_grille" value="<?php echo $id_grille; ?>">

        <label for="natureGrille">Nature Grille :</label>
        <select name="natureGrille" id="natureGrille" required>
        <option value="soutenance" <?php if(isset($nature) && $nature=="soutenance") echo "selected"; ?>>SOUTENANCE</option>
        <option value="stage" <?php if(isset($nature) && $nature=="stage") echo "selected"; ?>>STAGE</option>
        <option value="portfolio" <?php if(isset($nature) && $nature=="portfolio") echo "selected"; ?>>PORTFOLIO</option>
        <option value="anglais" <?php if(isset($nature) && $nature=="anglais") echo "selected"; ?>>ANGLAIS</option>
        <option value="rapport" <?php if(isset($nature) && $nature=="rapport") echo "selected"; ?>>RAPPORT</option>
        </select>

    <label>Note Max de la Grille :</label>
    <input type="number" name="noteMaxGrille" required>

    <label>Nom du Module de Grille d'Evaluation :</label>
    <input type="text" name="nomModuleGrilleEvaluation" required>

    <label>Année de début :</label>
        <input type="number" name="anneeDebut" value="<?php echo htmlspecialchars($annee); ?>" required>


    <button type="submit">✅ Ajouter</button>
</form>

<?php echo "<a href='../Grille.php'>📂 Retour aux Grilles</a>";?>
