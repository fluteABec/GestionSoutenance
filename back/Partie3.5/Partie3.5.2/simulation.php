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

// Récupérer la grille
$sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = $id_grille";
$res = $conn->query($sql);
if ($res->num_rows == 0) die("Grille non trouvée.");
$grille = $res->fetch_assoc();

$total_obtenu = 0;
$total_max_criteres = 0;
$details = [];

foreach ($notes as $id_critere => $note) {
    $id_critere = intval($id_critere);
    $note = floatval($note);

    // Vérifier la note max de ce critère
    $sql = "SELECT sc.valeurMaxCritereEval, c.descCourte 
            FROM sectioncontenircriteres sc 
            JOIN critereseval c ON c.IdCritere = sc.IdCritere
            WHERE sc.IdCritere = $id_critere";
    $crit_res = $conn->query($sql);
    if ($crit_res->num_rows == 0) continue;

    $crit = $crit_res->fetch_assoc();
    $max = floatval($crit['valeurMaxCritereEval']);

    if ($note < 0) $note = 0;
    if ($note > $max) $note = $max; // sécurité

    // on cumule la note et la valeur max totale des critères
    $total_obtenu += $note;
    $total_max_criteres += $max;

    $details[] = [
        "descCourte" => $crit['descCourte'],
        "note" => $note,
        "max" => $max
    ];
}

// Calcul pondéré de la note finale
$note_finale = 0;
if ($total_max_criteres > 0) {
    $note_finale = ($total_obtenu / $total_max_criteres) * $grille['noteMaxGrille'];
}
?>


<link rel="stylesheet" href="../../../stylee.css">

<?php include '../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:600px;margin:120px auto 0 auto;">
    <h2 class="square-title">Simulation de la grille : <?php echo htmlspecialchars($grille['nomModuleGrilleEvaluation']); ?></h2>
    <div class="card" style="margin-bottom:18px;">
        <p><strong>Note maximale de la grille :</strong> <?php echo htmlspecialchars($grille['noteMaxGrille']); ?></p>
    </div>
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Critère</th>
                    <th>Votre note</th>
                    <th>Max Critère</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $d): ?>
                <tr>
                    <td><?php echo htmlspecialchars($d['descCourte']); ?></td>
                    <td><?php echo htmlspecialchars($d['note']); ?></td>
                    <td><?php echo htmlspecialchars($d['max']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <h3 style="margin-top:24px;" class="square-title">Note obtenue : <?php echo round($note_finale, 2); ?> / <?php echo htmlspecialchars($grille['noteMaxGrille']); ?></h3>
    <div style="display:flex;gap:16px;justify-content:center;margin-top:18px;">
        <a href="Affichage.php?id_grille=<?php echo $id_grille; ?>" class="btn btn-retour">📂 Retour à l'affichage de grille</a>
        <a href='Grille.php' class="btn btn-retour">📂 Retour aux grilles</a>
    </div>
</div>

