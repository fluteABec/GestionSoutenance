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

    <label>Nature Grille :</label>
    <input type="text" name="natureGrille" required>

    <label>Note Max de la Grille :</label>
    <input type="number" name="noteMaxGrille" required>

    <label>Nom du Module de Grille d'Evaluation :</label>
    <input type="text" name="nomModuleGrilleEvaluation" required>

    <label>Année de Début :</label>
    <select name="anneeDebut" required>
        <option value="">-- Sélectionner une année --</option>
        <?php
        // Charger les années depuis la table anneesuniversitaires
        $res = $conn->query("SELECT anneeDebut FROM anneesuniversitaires ORDER BY anneeDebut DESC");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='" . $row['anneeDebut'] . "'>" . $row['anneeDebut'] . "</option>";
        }
        ?>
    </select>

    <button type="submit">Ajouter</button>
</form>

<?php echo "<a href='../Grille.php'>📂 Retour aux Grilles</a>";?>
