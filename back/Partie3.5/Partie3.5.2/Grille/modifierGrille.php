<?php
include("../Bouton.php");
// Grille/modifierGrille.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "evaluationstages";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}


//////////////////////////////////////////////// MODIFICATION //////////////////////////////////////////////////////////////////////////////


// 1) Déterminer l'id de la grille : POST (après submit) OU GET (à l'ouverture du formulaire)
$id_grille = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_grille'])) {
    $id_grille = intval($_POST['id_grille']);
} elseif (isset($_GET['id_grille'])) {
    $id_grille = intval($_GET['id_grille']);
}

// Si toujours absent -> afficher message utile et quitter
if (!$id_grille) {
    echo "<h3>Erreur : grille non spécifiée.</h3>";
    echo "<p>Vérifie que le lien contient <code>?id_grille=...</code>.</p>";
    echo "<p><a href='/SQL/Grille.php'>&larr; Retour aux grilles</a></p>";
    // debug court (décommente si besoin) :
    // echo "<pre>GET=" . htmlspecialchars(print_r($_GET, true)) . "\nPOST=" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
    exit;
}

// Vérification si la grille est déjà utilisée
if (grilleDejaUtilisee($conn, $id_grille)) {
    echo "<br><a href='../Grille.php'>📂 Retour aux Grilles</a> <br> <br>";
    die("⛔ Cette grille est déjà utilisée pour une évaluation et ne peut plus être modifiée.");

}

// Si formulaire soumis => faire l'UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récupération et nettoyage des champs
    $nature = isset($_POST['natureGrille']) ? trim($_POST['natureGrille']) : '';
    $note   = isset($_POST['noteMaxGrille']) ? trim($_POST['noteMaxGrille']) : '';
    $nom    = isset($_POST['nomModuleGrilleEvaluation']) ? trim($_POST['nomModuleGrilleEvaluation']) : '';
    $annee  = isset($_POST['anneeDebut']) ? trim($_POST['anneeDebut']) : '';

    // Requête préparée pour éviter injection
    $stmt = $conn->prepare("UPDATE modelesgrilleeval 
                            SET natureGrille = ?, noteMaxGrille = ?, nomModuleGrilleEvaluation = ?, anneeDebut = ?
                            WHERE IdModeleEval = ?");
    if (!$stmt) {
        echo "Erreur (prepare) : " . $conn->error;
        exit;
    }
    $stmt->bind_param("ssssi", $nature, $note, $nom, $annee, $id_grille);
    if ($stmt->execute()) {
        // succès -> retour vers la liste (ou vers la page que tu veux)
        header("Location: /SQL/Grille.php?updated=1");
        exit;
    } else {
        echo "Erreur SQL (execute) : " . htmlspecialchars($stmt->error);
        exit;
    }
}

// Sinon (méthode GET) : charger les valeurs actuelles pour pré-remplir le formulaire
$stmt = $conn->prepare("SELECT natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut
                        FROM modelesgrilleeval WHERE IdModeleEval = ?");
$stmt->bind_param("i", $id_grille);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "Erreur : grille introuvable.";
    echo "<p><a href='/SQL/Grille.php'>&larr; Retour</a></p>";
    exit;
}
$row = $res->fetch_assoc();
$nature = $row['natureGrille'];
$note   = $row['noteMaxGrille'];
$nom    = $row['nomModuleGrilleEvaluation'];
$annee  = $row['anneeDebut'];

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier la grille</title>
    <link rel="stylesheet" href="../../../../stylee.css">
</head>
<body>
    <?php include '../../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:600px;width:96%;margin:40px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">✏️ Modifier la grille "<?php echo htmlspecialchars($nom); ?>"</h2>
    <form method="POST" class="card" style="padding:32px 24px;">
        <input type="hidden" name="id_grille" value="<?php echo $id_grille; ?>">
        <div class="form-group" style="margin-bottom:18px;">
            <label for="natureGrille">Nature Grille :</label>
            <select name="natureGrille" id="natureGrille" required class="input-text">
                <option value="soutenance" <?php if(isset($nature) && $nature=="soutenance") echo "selected"; ?>>SOUTENANCE</option>
                <option value="stage" <?php if(isset($nature) && $nature=="stage") echo "selected"; ?>>STAGE</option>
                <option value="portfolio" <?php if(isset($nature) && $nature=="portfolio") echo "selected"; ?>>PORTFOLIO</option>
                <option value="anglais" <?php if(isset($nature) && $nature=="anglais") echo "selected"; ?>>ANGLAIS</option>
                <option value="rapport" <?php if(isset($nature) && $nature=="rapport") echo "selected"; ?>>RAPPORT</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="noteMaxGrille">Note Max :</label>
            <input type="number" name="noteMaxGrille" id="noteMaxGrille" value="<?php echo htmlspecialchars($note); ?>" required class="input-text">
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="nomModuleGrilleEvaluation">Nom du module :</label>
            <input type="text" name="nomModuleGrilleEvaluation" id="nomModuleGrilleEvaluation" value="<?php echo htmlspecialchars($nom); ?>" required class="input-text">
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="anneeDebut">Année de début :</label>
            <input type="number" name="anneeDebut" id="anneeDebut" value="<?php echo htmlspecialchars($annee); ?>" required class="input-text">
        </div>
        <button type="submit" class="btn btn-primary">✅ Enregistrer</button>
    </form>
    <a href="../Grille.php" class="btn-retour mb-3">📂 Retour aux Grilles</a>
</div>
</body>
</html>
