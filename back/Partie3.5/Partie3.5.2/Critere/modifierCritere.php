<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connexion échouée : " . $conn->connect_error);


//////////////////////////////////////////// MODIFICATION CRIT ////////////////////////////////////////////////////////


// Vérifier qu'on a les bons paramètres
if (!isset($_GET['id_critere']) || !isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
    die("Erreur : paramètres manquants.");
}

$id_critere = intval($_GET['id_critere']);
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
    $descCourte = $_POST['descCourte'];
    $descLongue = $_POST['descLongue'];
    $valeurMax  = floatval($_POST['valeurMaxCritereEval']);

    // Mise à jour critereseval
    $sql1 = "UPDATE critereseval 
             SET descCourte = '$descCourte', descLongue = '$descLongue' 
             WHERE IdCritere = $id_critere";
    $ok1 = $conn->query($sql1);

    // Mise à jour sectioncontenircriteres
    $sql2 = "UPDATE sectioncontenircriteres 
             SET valeurMaxCritereEval = $valeurMax 
             WHERE IdSection = $id_section AND IdCritere = $id_critere";
    $ok2 = $conn->query($sql2);

    if ($ok1 && $ok2) {
        echo "✅ Critère modifié avec succès.";
        header("Location: ../Affichage.php?id_grille=$id_grille");
        exit;
    } else {
        echo "Erreur SQL : " . $conn->error;
    }
} else {
    // Charger le critère existant
    $sql = "SELECT c.descCourte, c.descLongue, sc.valeurMaxCritereEval
            FROM critereseval c
            JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
            WHERE c.IdCritere = $id_critere AND sc.IdSection = $id_section";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $descCourte = $row['descCourte'];
        $descLongue = $row['descLongue'];
        $valeurMax  = $row['valeurMaxCritereEval'];
    } else {
        die("Erreur : critère non trouvé.");
    }
}
?>

<h2>Modifier le critère</h2>
<form method="POST">
    <label>Description Courte :</label>
    <input type="text" name="descCourte" value="<?php echo htmlspecialchars($descCourte); ?>" required>

    <label>Description Longue :</label>
    <input type="text" name="descLongue" value="<?php echo htmlspecialchars($descLongue); ?>" required>

    <label>Note maximale :</label>
    <input type="number" step="0.1" name="valeurMaxCritereEval" value="<?php echo htmlspecialchars($valeurMax); ?>" required>

    <button type="submit">✅ Enregistrer</button>
</form>

<?php
$id_grille = intval($_GET['id_grille']);
?>
<br><br><a href='../Affichage.php?id_grille=<?= $id_grille?>'>📂 Retour à l'affichage de grille</a>
<br><a href='../Grille.php'>📂 Retour aux Grilles</a> 