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

<h2>Modifier la section</h2>
<form method="POST">
    <label>Titre :</label>
    <input type="text" name="titre" value="<?php echo htmlspecialchars($titre); ?>" required>

    <label>Description :</label>
    <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>

    <button type="submit">✅ Enregistrer</button>
</form>

<?php
$id_grille = intval($_GET['id_grille']);
echo "<br><br><a href='../Affichage.php?id_grille=$id_grille'>📂 Retour aux sections</a>"; 
echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a>"; 
