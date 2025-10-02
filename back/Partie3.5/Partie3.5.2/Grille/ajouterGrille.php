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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ajouter une grille</title>
    <link rel="stylesheet" href="../../../../stylee.css">
</head>
<body>
    <div class="admin-block">
        <h2 class="section-title">➕ Ajouter une grille</h2>
        <form method="POST" class="card" style="max-width:480px;width:100%;margin:auto;">
            <input type="hidden" name="id_grille" value="<?php echo isset($id_grille) ? $id_grille : ''; ?>">
            <label for="natureGrille">Nature Grille :</label>
            <select name="natureGrille" id="natureGrille" required>
                <option value="soutenance" <?php if(isset($nature) && $nature=="soutenance") echo "selected"; ?>>SOUTENANCE</option>
                <option value="stage" <?php if(isset($nature) && $nature=="stage") echo "selected"; ?>>STAGE</option>
                <option value="portfolio" <?php if(isset($nature) && $nature=="portfolio") echo "selected"; ?>>PORTFOLIO</option>
                <option value="anglais" <?php if(isset($nature) && $nature=="anglais") echo "selected"; ?>>ANGLAIS</option>
                <option value="rapport" <?php if(isset($nature) && $nature=="rapport") echo "selected"; ?>>RAPPORT</option>
            </select>
            <label for="noteMaxGrille">Note Max de la Grille :</label>
            <input type="number" name="noteMaxGrille" id="noteMaxGrille" required>
            <label for="nomModuleGrilleEvaluation">Nom du Module de Grille d'Évaluation :</label>
            <input type="text" name="nomModuleGrilleEvaluation" id="nomModuleGrilleEvaluation" required>
            <label for="anneeDebut">Année de début :</label>
            <input type="number" name="anneeDebut" id="anneeDebut" value="<?php echo isset($annee) ? htmlspecialchars($annee) : ''; ?>" required>
            <button type="submit" class="btn btn-primary mt-3">✅ Ajouter</button>
        </form>
            <a href="../Grille.php" class="btn-retour mb-3">📂 Retour aux Grilles</a>
    </div>
</body>
</html>
