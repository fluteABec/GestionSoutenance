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
if (!isset($_GET['id_critere']) || !isset($_GET['id_section'])) {
    die("Erreur : critère ou section non spécifié.");
}

$id_critere = intval($_GET['id_critere']);
$id_section = intval($_GET['id_section'] ?? $_POST['id_section'] ?? 0);
$id_grille  = intval($_GET['id_grille'] ?? $_POST['id_grille'] ?? 0);


//////////////////////////////////////////////// MODIFICATION ////////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descLongue = $conn->real_escape_string($_POST['descLongue']);
    $descCourte = $conn->real_escape_string($_POST['descCourte']);

    // Mise à jour dans critereseval
    $sql = "UPDATE critereseval 
            SET descLongue = '$descLongue', descCourte = '$descCourte' 
            WHERE IdCritere = $id_critere";

    if ($conn->query($sql)) {
        echo "✅ Critère modifié avec succès.";
        header("Location: ../Affichage.php?id_grille=$id_grille");    } else {
        echo "Erreur SQL : " . $conn->error;
    }
} else {
    // Récupérer le critère existant
    $sql = "SELECT * FROM critereseval WHERE IdCritere = $id_critere";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $descLongue = $row['descLongue'];
        $descCourte = $row['descCourte'];
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

    <button type="submit">✅ Enregistrer</button>
</form>

<?php 
// bouton retour vers les critères de la section
echo "<br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour à l'affichage de grille</a>";
echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a>";


?>
