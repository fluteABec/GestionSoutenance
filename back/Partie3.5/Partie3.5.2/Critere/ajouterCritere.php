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

///////////////////////////////////////////////// AJOUTER CRITERE ////////////////////////////////////////////////////////////


$id_section = intval($_GET['id_section'] ?? $_POST['id_section'] ?? 0);
$id_grille  = intval($_GET['id_grille'] ?? $_POST['id_grille'] ?? 0);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_GET['id_section'])) {
        die("Erreur : aucune section sélectionnée.");
    }

    $id_section = intval($_GET['id_section']);
    $descLongue = $_POST['descLongue'];
    $descCourte = $_POST['descCourte'];

    $id_grille  = intval($_GET['id_grille']);

    // Étape 1 : insertion dans critereseval (idCritere est AUTO_INCREMENT → on ne l'insère pas)
    $sql1 = "INSERT INTO critereseval (descLongue, descCourte) 
             VALUES ('$descLongue', '$descCourte')";

    if ($conn->query($sql1)) {
        $id_critere = $conn->insert_id;

        // Étape 2 : liaison dans sectioncontenircriteres
        $sql2 = "INSERT INTO sectioncontenircriteres (IdSection, IdCritere) 
                 VALUES ($id_section, $id_critere)";

        if ($conn->query($sql2)) {
            echo "✅ Critère ajouté avec succès.";
            header("Location: ../Critere.php?id_section=$id_section&id_grille=$id_grille");        } else {
            echo "Erreur (insertion sectioncontenircriteres) : " . $conn->error;
        }
    } else {
        echo "Erreur (insertion critereseval) : " . $conn->error;
    }
}
?>

<h2>Ajouter un critère</h2>
<form method="POST">
    <label>Description Longue :</label>
    <input type="text" name="descLongue" required>

    <label>Description Courte :</label>
    <input type="text" name="descCourte" required>

    <button type="submit">Ajouter</button>
</form>

<?php echo "<br><br><a href='../Critere.php?id_section=" . ($_GET['id_section'] ?? '') . "'>📂 Retour aux critères</a>"; 
echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a>";
?>
