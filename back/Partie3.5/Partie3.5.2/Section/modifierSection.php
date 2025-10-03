<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifier qu'on a bien reçu l'id
if (!isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
    die("Erreur : section ou grille non spécifiée.");
}


//////////////////////////////////////////////// MODIFICATION //////////////////////////////////////////////////////////////////////////////


$id_section = intval($_GET['id_section']);
$id_grille  = intval($_GET['id_grille']);

// Vérification si la grille est déjà utilisée
include("../Bouton.php");
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour à l'affichage de grille</a>";
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $conn->real_escape_string($_POST['titre']);
    $description = $conn->real_escape_string($_POST['description']);

    // Mise à jour dans sectioncritereeval (titre + description)
    $sql = "UPDATE sectioncritereeval 
            SET titre = '$titre', description = '$description' 
            WHERE IdSection = $id_section";

    if ($conn->query($sql)) {
        echo "✅ Section modifiée avec succès.";
        header("Location: ../Affichage.php?id_grille=$id_grille");
        exit;
    } else {
        echo "Erreur SQL : " . $conn->error;
    }
} else {
    // Récupérer la section existante
    $sql = "SELECT * FROM sectioncritereeval WHERE IdSection = $id_section";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $titre = $row['titre'];
        $description = $row['description'];
    } else {
        die("Erreur : section non trouvée.");
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier la section</title>
    <link rel="stylesheet" href="../../../../stylee.css">
</head>
<body>
    <?php include '../../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:500px;width:96%;margin:80px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Modifier la section</h2>
    <form method="POST" class="card" style="padding:32px 24px;">
        <div class="form-group" style="margin-bottom:18px;">
            <label for="titre">Titre :</label>
            <input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($titre); ?>" required class="input-text">
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($description); ?>" required class="input-text">
        </div>
        <button type="submit" class="btn btn-primary">✅ Enregistrer</button>
    </form>
        <a href="../Affichage.php?id_grille=<?php echo intval($_GET['id_grille']); ?>" class="btn-retour mb-3">📂 Retour à l'affichage de grille</a>
    <a href="../Grille.php" class="btn-retour mb-3">📂 Retour aux Grilles</a>
</div>
</body>
</html>
