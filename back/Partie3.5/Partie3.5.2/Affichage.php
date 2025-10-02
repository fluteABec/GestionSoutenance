<?php
include("Bouton.php");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "evaluationstages";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
if (!isset($_GET['id_grille'])) {
    die("Erreur : aucune grille sélectionnée.");
}
$id_grille = intval($_GET['id_grille']);
$sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = $id_grille";
$res = $conn->query($sql);
if ($res->num_rows == 0) die("Grille non trouvée.");
$grille = $res->fetch_assoc();
$sql = "SELECT s.IdSection, sc.titre, sc.description
        FROM sectionseval s
        JOIN sectioncritereeval sc ON s.IdSection = sc.IdSection
        WHERE s.IdModeleEval = $id_grille";
$sections = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage de la grille</title>
    <link rel="stylesheet" href="../../../stylee.css">
</head>
<body>
    <?php include '../../navbarGrilles.php'; ?>
<div class="admin-block" style="max-width:1250px;width:98%;margin:40px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Grille : <?php echo htmlspecialchars($grille['nomModuleGrilleEvaluation']); ?></h2>
    <p><strong>Type :</strong> <?php echo htmlspecialchars($grille['natureGrille']); ?></p>
    <p><strong>Note Maximale :</strong> <?php echo htmlspecialchars($grille['noteMaxGrille']); ?></p>
    <form action="simulation.php" method="post">
        <input type="hidden" name="id_grille" value="<?php echo $id_grille; ?>">
        <div class="table-container" style="max-width:100%;overflow-x:auto;">
        <table class="styled-table" style="min-width:900px;">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Critères</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($sec = $sections->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sec['titre']); ?></td>
                    <td><?php echo htmlspecialchars($sec['description']); ?></td>
                    <td>
                        <div class="table-container" style="max-width:100%;overflow-x:auto;">
                        <table class="styled-table" style="min-width:700px;margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th>Description Courte</th>
                                    <th>Description Longue</th>
                                    <th>Note Max</th>
                                    <th>Votre Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $id_section = $sec['IdSection'];
                            $sql_crit = "SELECT c.IdCritere, c.descCourte, c.descLongue, sc.valeurMaxCritereEval
                                         FROM critereseval c
                                         JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
                                         WHERE sc.IdSection = $id_section";
                            $crit_res = $conn->query($sql_crit);
                            if ($crit_res->num_rows > 0) {
                                while ($crit = $crit_res->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($crit['descCourte']) . '</td>';
                                    echo '<td>' . htmlspecialchars($crit['descLongue']) . '</td>';
                                    echo '<td>' . htmlspecialchars($crit['valeurMaxCritereEval']) . '</td>';
                                    echo '<td><input type="number" name="critere[' . $crit['IdCritere'] . ']" min="0" max="' . $crit['valeurMaxCritereEval'] . '" step="0.01" class="input-note" style="min-width:80px;padding:6px 10px;"></td>';
                                    echo '<td>';
                                    echo '<a href="Critere/modifierCritere.php?id_critere=' . $crit['IdCritere'] . '&id_section=' . $id_section . '&id_grille=' . $id_grille . '" class="btn">✏️ Modifier</a> ';
                                    echo '<a href="Critere/supprimerCritere.php?id_critere=' . $crit['IdCritere'] . '&id_section=' . $id_section . '&id_grille=' . $id_grille . '" class="btn btn-supprimer" onclick="return confirm(\'Supprimer ce critère ?\')">🗑️ Supprimer</a>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Aucun critère</td></tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="5">
                                    <a href="Critere/ajouterCritere.php?id_section=<?php echo $id_section; ?>&id_grille=<?php echo $id_grille; ?>" class="btn btn-ajouter">➕ Ajouter un critère</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </td>
                    <td>
                        <a href="Section/modifierSection.php?id_section=<?php echo $sec['IdSection']; ?>&id_grille=<?php echo $id_grille; ?>" class="btn">✏️ Modifier</a>
                        <a href="Section/supprimerSection.php?id_section=<?php echo $sec['IdSection']; ?>&id_grille=<?php echo $id_grille; ?>" class="btn btn-supprimer" onclick="return confirm('Supprimer cette section et ses critères ?')">🗑️ Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <button type="submit" class="btn btn-primary mt-3">▶️ Lancer une simulation de note</button>
    </form>
    <div style="margin-top:24px;">
        <a href="Section/ajouterSection.php?id_grille=<?php echo $id_grille; ?>" class="btn btn-ajouter">➕ Ajouter une section</a>
    </div>
        <a href="Grille.php" class="btn-retour mb-3">📂 Retour aux grilles</a>

</div>
</body>
</html>


