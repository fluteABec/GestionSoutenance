<?php


// Inclusion du CSS et de la navbar
echo '<!DOCTYPE html><html lang="fr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="/projet_sql/stylee.css">';
echo '<title>Ajouter Section</title>';
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

echo '<div class="admin-block">';
echo '<div class="section-title">Ajouter une section</div>';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_GET['id_grille'])) {
        die('<div class=\"section-title\">Erreur : aucune grille sélectionnée.</div></div></body></html>');
    }
    $id_grille = intval($_GET['id_grille']);
    $titre = $conn->real_escape_string($_POST['titre']);
    $description = $conn->real_escape_string($_POST['description']);
    $sql1 = "INSERT INTO sectioncritereeval (titre, description) VALUES ('$titre', '$description')";
    if ($conn->query($sql1)) {
        $id_section = $conn->insert_id;
        $sql2 = "INSERT INTO sectionseval (IdSection, IdModeleEval) VALUES ($id_section, $id_grille)";
        if ($conn->query($sql2)) {
            echo '<div class="mb-3" style="color:green;font-weight:bold;">✅ Section ajoutée avec succès.</div>';
            echo '<a href="../Section.php?id_grille=' . $id_grille . '" class="btn btn-primary">Retour à la liste des sections</a>';
            echo '</div></body></html>';
            exit;
        } else {
            echo '<div class="mb-3" style="color:red;font-weight:bold;">Erreur (insertion sectionseval) : ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="mb-3" style="color:red;font-weight:bold;">Erreur (insertion sectioncritereeval) : ' . $conn->error . '</div>';
    }
    // On continue d'afficher le formulaire en cas d'erreur
}

$id_grille = isset($_GET['id_grille']) ? intval($_GET['id_grille']) : 0;
echo '<form method="POST" style="width:100%;max-width:500px;">';
echo '<label for="titre">Titre :</label>';
echo '<input type="text" id="titre" name="titre" required class="mb-3">';
echo '<label for="description">Description :</label>';
echo '<input type="text" id="description" name="description" required class="mb-3">';
echo '<div class="form-actions">';
echo '<button type="submit" class="btn btn-primary">Ajouter</button>';
echo '<a href="../Section.php?id_grille=' . $id_grille . '" class="btn">Retour aux sections</a>';
echo '</div>';
echo '</form>';
echo '</div>';
echo '</body></html>';