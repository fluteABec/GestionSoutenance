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
            header("Location: ../Section.php?id_grille=$id_grille");
            exit;
        } else {
            echo "Erreur (insertion sectionseval) : " . $conn->error;
        }
    } else {
        echo "Erreur (insertion sectioncritereeval) : " . $conn->error;
    }
}
?>


<h2>Ajouter une section</h2>
<form method="POST">
    <label>Titre :</label>
    <input type="text" name="titre" required>
    <label>Description :</label>
    <input type="text" name="description" required>
    <button type="submit">Ajouter</button>
</form>

<?php 