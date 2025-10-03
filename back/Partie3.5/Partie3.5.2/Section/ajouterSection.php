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


///////////////////////////////////////////////// AJOUTER ////////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_GET['id_grille'])) {
        die("Erreur : aucune grille sélectionnée.");
    }

    $id_grille = intval($_GET['id_grille']);
    $titre = $_POST['titre'];
    $description = $_POST['description'];

// Vérification si la grille est déjà utilisée
include("../Bouton.php");
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour à l'affichage de grille</a>";
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}

    // Étape 1 : insérer dans sectioncritereeval
    $sql1 = "INSERT INTO sectioncritereeval (titre, description) 
             VALUES ('$titre', '$description')";
    if ($conn->query($sql1)) {
        $id_section = $conn->insert_id;

        // Étape 2 : insérer dans sectionseval pour lier section + grille
        $sql2 = "INSERT INTO sectionseval (IdSection, IdModeleEval) 
                 VALUES ($id_section, $id_grille)";

        if ($conn->query($sql2)) {
            echo "✅ Section ajoutée avec succès.";
            header("Location: ../Affichage.php?id_grille=$id_grille");
            exit;
        } else {
            echo "Erreur (insertion sectionseval) : " . $conn->error;
        }
    } else {
        echo "Erreur (insertion sectioncritereeval) : " . $conn->error;
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ajouter une section</title>
    <link rel="stylesheet" href="../../../../stylee.css">
</head>
<body>
    <?php include '../../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:500px;width:96%;margin:40px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Ajouter une section</h2>
    <form method="POST" class="card" style="padding:32px 24px;">
        <div class="form-group" style="margin-bottom:18px;">
            <label for="titre">Titre :</label>
            <input type="text" name="titre" id="titre" required class="input-text">
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" required class="input-text">
        </div>
        <button type="submit" class="btn btn-primary">✅ Ajouter</button>
    </form>
        <a href="../Affichage.php?id_grille=<?php echo intval($_GET['id_grille']); ?>" class="btn-retour mb-3">📂 Retour à l'affichage de grille</a>
    <a href="../Grille.php" class="btn-retour mb-3">📂 Retour aux Grilles</a>
</div>
</body>
</html>