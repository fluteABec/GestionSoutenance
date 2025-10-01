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

$total = 0;
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

    $total += $note;
    $details[] = [
        "descCourte" => $crit['descCourte'],
        "note" => $note,
        "max" => $max
    ];
}
?>

<h2>Résultat de la simulation</h2>
<p>Grille : <?php echo htmlspecialchars($grille['nomModuleGrilleEvaluation']); ?></p>
<p>Type : <?php echo htmlspecialchars($grille['natureGrille']); ?></p>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Critère</th>
        <th>Votre Note</th>
        <th>Note Max</th>
    </tr>
    <?php foreach ($details as $d): ?>
        <tr>
            <td><?php echo htmlspecialchars($d['descCourte']); ?></td>
            <td><?php echo $d['note']; ?></td>
            <td><?php echo $d['max']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Note totale obtenue : <strong><?php echo $total; ?></strong> / <?php echo $grille['noteMaxGrille']; ?></h3>

<br>
<a href="Affichage.php?id_grille=<?php echo $id_grille; ?>">📂 Retour à l'affichage de grille</a>
