<?php

require_once "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);


$id   = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if (!$id || !$type) {
    die("⚠️ Paramètres manquants.");
}

// Charger la soutenance
if ($type === 'stage') {
    $sql = "SELECT * FROM EvalStage WHERE IdEvalStage = :id LIMIT 1";
} elseif ($type === 'anglais') {
    $sql = "SELECT * FROM EvalAnglais WHERE IdEvalAnglais = :id LIMIT 1";
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
$sql = "SELECT IdSalle, description FROM Salles ORDER BY IdSalle";
$stmt = $pdo->query($sql);
$listeSalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si formulaire soumis → UPDATE + redirection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date   = $_POST['DateSoutenance'];
    $salle  = $_POST['Salle'];
    $second = $_POST['SecondEnseignant'] ?? null;

    if ($type === 'stage') {
        $sql = "UPDATE EvalStage 
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
        $sql = "UPDATE EvalAnglais 
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


<div class="admin-block" style="max-width:600px;width:96%;margin:80px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Modifier la soutenance (<?= htmlspecialchars($type) ?>)</h2>
    <form method="post" class="card" style="padding:32px 24px;">
        <div class="form-group" style="margin-bottom:18px;">
            <label for="start">Heure de début :</label>
            <input type="datetime-local" id="start" name="DateSoutenance"
                   value="<?= $type === 'stage' ? date('Y-m-d\TH:i', strtotime($soutenance['date_h'])) : date('Y-m-d\TH:i', strtotime($soutenance['dateS'])) ?>" class="input-text" />
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="salleSelect">Salle</label>
            <select id="salleSelect" name="Salle" class="input-text">
                <?php foreach ($listeSalles as $salle): ?>
                    <option value="<?= htmlspecialchars($salle['IdSalle']) ?>"
                        <?= $salle['IdSalle'] == $soutenance['IdSalle'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle['IdSalle'] . " - " . $salle['description']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="SecondEnseignant"><?= $type === 'stage' ? "Second Enseignant" : "Enseignant" ?></label>
            <select name="SecondEnseignant" id="SecondEnseignant" class="input-text">
                <option value="">-- Choisir --</option>
                <?php foreach ($listeEnseignant as $enseignant): ?>
                    <option value="<?= htmlspecialchars($enseignant['IdEnseignant']) ?>"
                        <?= ($type === 'stage' && $enseignant['IdEnseignant'] == $soutenance['IdSecondEnseignant']) ||
                           ($type === 'anglais' && $enseignant['IdEnseignant'] == $soutenance['IdEnseignant']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
        <a href="../mainAdministration.php" class="btn-retour mb-3">← Retour</a>

</div>

</body>
</html>
