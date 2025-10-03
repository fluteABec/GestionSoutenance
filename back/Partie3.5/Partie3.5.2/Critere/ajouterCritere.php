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


<link rel="stylesheet" href="../../../../stylee.css">

<?php include '../../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:500px;margin:120px auto 0 auto;">
    <h2 class="square-title">Ajouter un critère</h2>
    <form method="POST" class="card" style="margin-bottom:18px;">
        <label for="descCourte">Description Courte :</label>
        <input type="text" name="descCourte" id="descCourte" required>

        <label for="descLongue">Description Longue :</label>
        <input type="text" name="descLongue" id="descLongue" required>

        <label for="valeurMaxCritereEval">Note maximale :</label>
        <input type="number" step="0.1" name="valeurMaxCritereEval" id="valeurMaxCritereEval" required>

        <div class="form-actions" style="margin-top:18px;">
            <button type="submit" class="btn btn-primary">✅ Ajouter</button>
        </div>
    </form>
    <?php $id_grille = intval($_GET['id_grille']); ?>
    <div style="display:flex;gap:16px;justify-content:center;margin-top:18px;">
        <a href='../Affichage.php?id_grille=<?= $id_grille?>' class="btn btn-retour">📂 Retour à l'affichage de grille</a>
        <a href='../Grille.php' class="btn btn-retour">📂 Retour aux Grilles</a>
    </div>
</div>
