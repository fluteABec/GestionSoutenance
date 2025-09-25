<?php

// Inclusion du CSS et de la navbar
echo '<!DOCTYPE html><html lang="fr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="/projet_sql/stylee.css">';
echo '<title>Modifier Section</title>';
echo '</head>';
echo '<body>';
include("../../../navbar.php");

$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("<div class=\"admin-block\"><div class=\"section-title\">Connexion échouée : " . $conn->connect_error . "</div></div></body></html>");
}
if (!isset($_GET['id_section']) || !isset($_GET['id_grille'])) {
    die("<div class=\"admin-block\"><div class=\"section-title\">Erreur : section ou grille non spécifiée.</div></div></body></html>");
}
$id_section = intval($_GET['id_section']);
$id_grille  = intval($_GET['id_grille']);

echo '<div class="admin-block">';
echo '<div class="section-title">Modifier la section</div>';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $conn->real_escape_string($_POST['titre']);
    $description = $conn->real_escape_string($_POST['description']);
    $sql = "UPDATE sectioncritereeval SET titre = '$titre', description = '$description' WHERE IdSection = $id_section";
    if ($conn->query($sql)) {
        echo '<div class="mb-3" style="color:green;font-weight:bold;">✅ Section modifiée avec succès.</div>';
        echo '<a href="../Section.php?id_grille=' . $id_grille . '" class="btn btn-primary">Retour à la liste des sections</a>';
        echo '</div></body></html>';
        exit;
    } else {
        echo '<div class="mb-3" style="color:red;font-weight:bold;">Erreur SQL : ' . $conn->error . '</div>';
    }
    // On continue d'afficher le formulaire en cas d'erreur
}
else {
    $sql = "SELECT * FROM sectioncritereeval WHERE IdSection = $id_section";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $titre = $row['titre'];
        $description = $row['description'];
    } else {
        die('<div class="section-title">Erreur : section non trouvée.</div></div></body></html>');
    }
}

echo '<form method="POST" style="width:100%;max-width:500px;">';
echo '<label for="titre">Titre :</label>';
echo '<input type="text" id="titre" name="titre" value="' . htmlspecialchars($titre) . '" required class="mb-3">';
echo '<label for="description">Description :</label>';
echo '<input type="text" id="description" name="description" value="' . htmlspecialchars($description) . '" required class="mb-3">';
echo '<div class="form-actions">';
echo '<button type="submit" class="btn btn-primary">✅ Enregistrer</button>';
echo '<a href="../Section.php?id_grille=' . $id_grille . '" class="btn">Retour aux sections</a>';
echo '</div>';
echo '</form>';
echo '</div>';
echo '</body></html>';
