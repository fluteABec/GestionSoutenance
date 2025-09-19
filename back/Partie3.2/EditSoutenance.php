<?php
$host = 'localhost';
$db   = 'evaluationstages';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

$id   = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if (!$id || !$type) {
    die("⚠️ Paramètres manquants.");
}

// Charger la soutenance
if ($type === 'stage') {
    $sql = "SELECT * FROM evalstage WHERE IdEvalStage = :id LIMIT 1";
} elseif ($type === 'anglais') {
    $sql = "SELECT * FROM evalanglais WHERE IdEvalAnglais = :id LIMIT 1";
} else {
    die("⚠️ Type de soutenance inconnu.");
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$soutenance = $stmt->fetch();

if (!$soutenance) {
    die("⚠️ Soutenance introuvable.");
}

// Récupérer l’étudiant lié
$idEtudiant = $soutenance['IdEtudiant'];

// Récupérer liste enseignants disponibles
$sql = "
    SELECT e.IdEnseignant, e.nom, e.prenom
    FROM Enseignants e
    WHERE e.IdEnseignant NOT IN (
        SELECT es.IdEnseignantTuteur
        FROM EvalStage es
        WHERE es.IdEtudiant = :idEtudiant
    )
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['idEtudiant' => $idEtudiant]);
$listeEnseignant = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les salles disponibles (sans filtrer sur la date)
$sql = "SELECT IdSalle, description FROM salles ORDER BY IdSalle";
$stmt = $pdo->query($sql);
$listeSalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si formulaire soumis → UPDATE + redirection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date   = $_POST['DateSoutenance'];
    $salle  = $_POST['Salle'];
    $second = $_POST['SecondEnseignant'] ?? null;

    if ($type === 'stage') {
        $sql = "UPDATE evalstage 
                SET date_h = :date, IdSalle = :salle, IdSecondEnseignant = :second 
                WHERE IdEvalStage = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'salle' => $salle,
            'second' => $second,
            'id' => $id
        ]);
    } elseif ($type === 'anglais') {
        $sql = "UPDATE evalanglais 
                SET dateS = :date, IdSalle = :salle, IdEnseignant = :second 
                WHERE IdEvalAnglais = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'salle' => $salle,
            'second' => $second,
            'id' => $id
        ]);
    }

    // Redirection avec message de succès
    header("Location: ../mainAdministration.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer la Soutenance</title>
    <link rel="stylesheet" href="/projet_sql/stylee.css">
</head>
<body>
<div class="navbar">
    <div class="brand"><span class="logo"></span><span>Soutenances</span></div>
    <a class="nav-item" href="/projet_sql/back/Partie3.1/3_1_natan.php">Tâches enseignants</a>
    <a class="nav-item" href="/projet_sql/back/Partie3.3/index.php">Évaluations IUT</a>
    <a class="nav-item" href="/projet_sql/back/Partie3.4/index.php">Diffusion résultats</a>
    <a class="nav-item" href="/projet_sql/back/mainAdministration.php">Administration</a>
</div>

<h2>Modifier la soutenance (<?= htmlspecialchars($type) ?>)</h2>

<form method="post">
    <label>Heure de début :</label>
    <input type="datetime-local" id="start" name="DateSoutenance"
           value="<?= $type === 'stage' ? date('Y-m-d\TH:i', strtotime($soutenance['date_h'])) : date('Y-m-d\TH:i', strtotime($soutenance['dateS'])) ?>" />
    <br>

    <label>Salle</label>
    <select id="salleSelect" name="Salle">
        <?php foreach ($listeSalles as $salle): ?>
            <option value="<?= htmlspecialchars($salle['IdSalle']) ?>"
                <?= $salle['IdSalle'] == $soutenance['IdSalle'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($salle['IdSalle'] . " - " . $salle['description']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label><?= $type === 'stage' ? "Second Enseignant" : "Enseignant" ?></label>
    <select name="SecondEnseignant">
        <option value="">-- Choisir --</option>
        <?php foreach ($listeEnseignant as $enseignant): ?>
            <option value="<?= htmlspecialchars($enseignant['IdEnseignant']) ?>"
                <?= ($type === 'stage' && $enseignant['IdEnseignant'] == $soutenance['IdSecondEnseignant']) ||
                   ($type === 'anglais' && $enseignant['IdEnseignant'] == $soutenance['IdEnseignant']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <button type="submit">Enregistrer les modifications</button>
</form>

    <p><a href="../mainAdministration.php">← Retour au menu</a></p>

</body>
</html>
