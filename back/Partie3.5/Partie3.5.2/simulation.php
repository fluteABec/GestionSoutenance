
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "evaluationstages";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id_grille'])) {
    die("Erreur : accès invalide.");
}
$id_grille = intval($_POST['id_grille']);
$notes = $_POST['critere'] ?? [];
$sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = $id_grille";
$res = $conn->query($sql);
if ($res->num_rows == 0) die("Grille non trouvée.");
$grille = $res->fetch_assoc();
$total = 0;
$details = [];
foreach ($notes as $id_critere => $note) {
    $id_critere = intval($id_critere);
    $note = floatval($note);
    $sql = "SELECT sc.valeurMaxCritereEval, c.descCourte 
            FROM sectioncontenircriteres sc 
            JOIN critereseval c ON c.IdCritere = sc.IdCritere
            WHERE sc.IdCritere = $id_critere";
    $crit_res = $conn->query($sql);
    if ($crit_res->num_rows == 0) continue;
    $crit = $crit_res->fetch_assoc();
    $max = floatval($crit['valeurMaxCritereEval']);
    if ($note < 0) $note = 0;
    if ($note > $max) $note = $max;
    $total += $note;
    $details[] = [
        "descCourte" => $crit['descCourte'],
        "note" => $note,
        "max" => $max
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat simulation</title>
    <link rel="stylesheet" href="../../../stylee.css">
</head>
<body>
<div class="admin-block" style="max-width:700px;width:95%;margin:40px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Résultat de la simulation</h2>
    <p><strong>Grille :</strong> <?php echo htmlspecialchars($grille['nomModuleGrilleEvaluation']); ?></p>
    <p><strong>Type :</strong> <?php echo htmlspecialchars($grille['natureGrille']); ?></p>
    <div class="table-container" style="max-width:100%;overflow-x:auto;">
    <table class="styled-table" style="min-width:400px;">
        <thead>
            <tr>
                <th>Critère</th>
                <th>Votre Note</th>
                <th>Note Max</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($details as $d): ?>
            <tr>
                <td><?php echo htmlspecialchars($d['descCourte']); ?></td>
                <td><?php echo $d['note']; ?></td>
                <td><?php echo $d['max']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <h3 style="margin-top:32px;">Note totale obtenue : <strong><?php echo $total; ?></strong> / <?php echo $grille['noteMaxGrille']; ?></h3>
    <a href="Affichage.php?id_grille=<?php echo $id_grille; ?>" class="btn-retour mb-3">📂 Retour à l'affichage de grille</a>
</div>
</body>
</html>
