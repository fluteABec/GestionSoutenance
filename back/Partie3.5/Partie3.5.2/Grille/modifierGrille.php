<?php
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
    echo "<p><a href='../Grille.php'>&larr; Retour aux grilles</a></p>";
    // debug court (décommente si besoin) :
    // echo "<pre>GET=" . htmlspecialchars(print_r($_GET, true)) . "\nPOST=" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
    exit;
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
        header("Location: ../Grille.php?updated=1");
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
    echo "<p><a href='../Grille.php'>&larr; Retour</a></p>";
    exit;
}
$row = $res->fetch_assoc();
$nature = $row['natureGrille'];
$note   = $row['noteMaxGrille'];
$nom    = $row['nomModuleGrilleEvaluation'];
$annee  = $row['anneeDebut'];

// Récupérer les années pour le <select>
$years = $conn->query("SELECT anneeDebut FROM anneesuniversitaires ORDER BY anneeDebut DESC");
?>

<?php include '../../../navbar.php'; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Modifier la grille</title>
<link rel="stylesheet" href="../../../../stylee.css">
</head>
<body>
    <div class="admin-block">
        <h2 class="section-title">✏️ Modifier la grille #<?php echo $id_grille; ?></h2>
        <form method="POST" style="max-width:500px;margin:0 auto;">
            <input type="hidden" name="id_grille" value="<?php echo $id_grille; ?>">
            <label for="natureGrille">Nature :</label>
            <input type="text" name="natureGrille" id="natureGrille" value="<?php echo htmlspecialchars($nature); ?>" required>
            <label for="noteMaxGrille">Note Max :</label>
            <input type="number" name="noteMaxGrille" id="noteMaxGrille" value="<?php echo htmlspecialchars($note); ?>" required>
            <label for="nomModuleGrilleEvaluation">Nom du module :</label>
            <input type="text" name="nomModuleGrilleEvaluation" id="nomModuleGrilleEvaluation" value="<?php echo htmlspecialchars($nom); ?>" required>
            <label for="anneeDebut">Année de début :</label>
            <select name="anneeDebut" id="anneeDebut" class="btn" style="min-width:180px;" required>
                <?php
                if ($years) {
                    while ($y = $years->fetch_assoc()) {
                        $val = $y['anneeDebut'];
                        $sel = ($val == $annee) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($val) . "' $sel>" . htmlspecialchars($val) . "</option>";
                    }
                }
                ?>
            </select>
            <div class="form-actions" style="margin-top:24px;display:flex;gap:16px;">
                <button type="submit" class="btn btn-primary" style="min-width:160px;height:44px;font-size:1rem;">✅ Enregistrer</button>
                <a href="../Grille.php" class="btn" style="background:var(--navy);color:#fff;min-width:160px;height:44px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">📂 Retour aux Grilles</a>
            </div>
        </form>
    </div>
</body>
</html>
