<?php

// Inclusion du CSS et de la navbar
echo '<!DOCTYPE html><html lang="fr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="/projet_sql/stylee.css">';
echo '<title>Ajouter Grille</title>';
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
echo '<div class="section-title">➕ Ajouter une grille</div>';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nature = $conn->real_escape_string($_POST['natureGrille']);
    $note   = $conn->real_escape_string($_POST['noteMaxGrille']);
    $nom    = $conn->real_escape_string($_POST['nomModuleGrilleEvaluation']);
    $annee  = $conn->real_escape_string($_POST['anneeDebut']);
    $sql = "INSERT INTO ModelesGrilleEval (natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut) VALUES ('$nature', '$note', '$nom', '$annee')";
    if ($conn->query($sql)) {
        echo '<div class="mb-3" style="color:green;font-weight:bold;">✅ Grille ajoutée avec succès.</div>';
        echo '<a href="../Grille.php" class="btn btn-primary">Retour à la liste des grilles</a>';
        echo '</div></body></html>';
        exit;
    } else {
        echo '<div class="mb-3" style="color:red;font-weight:bold;">❌ Erreur SQL : ' . $conn->error . '</div>';
    }
    // On continue d'afficher le formulaire en cas d'erreur
}

echo '<form method="POST" style="width:100%;max-width:500px;">';
echo '<label for="natureGrille">Nature Grille :</label>';
echo '<input type="text" id="natureGrille" name="natureGrille" required class="mb-3">';
echo '<label for="noteMaxGrille">Note Max de la Grille :</label>';
echo '<input type="number" id="noteMaxGrille" name="noteMaxGrille" required class="mb-3">';
echo '<label for="nomModuleGrilleEvaluation">Nom du Module de Grille d\'Evaluation :</label>';
echo '<input type="text" id="nomModuleGrilleEvaluation" name="nomModuleGrilleEvaluation" required class="mb-3">';
echo '<label for="anneeDebut">Année de Début :</label>';
echo '<select id="anneeDebut" name="anneeDebut" required class="mb-3">';
echo '<option value="">-- Sélectionner une année --</option>';
$res = $conn->query("SELECT anneeDebut FROM anneesuniversitaires ORDER BY anneeDebut DESC");
while ($row = $res->fetch_assoc()) {
    echo '<option value="' . $row['anneeDebut'] . '">' . $row['anneeDebut'] . '</option>';
}
echo '</select>';
echo '<div class="form-actions">';
echo '<button type="submit" class="btn btn-primary">Ajouter</button>';
echo '<a href="../Grille.php" class="btn">Retour aux grilles</a>';
echo '</div>';
echo '</form>';
echo '</div>';
echo '</body></html>';
