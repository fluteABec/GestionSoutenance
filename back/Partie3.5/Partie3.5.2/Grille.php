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

$sql = "SELECT * FROM modelesgrilleeval";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des grilles</title>
    <link rel="stylesheet" href="../../../stylee.css">
</head>
<body>
<?php include '../../navbarGrilles.php'; ?>
<div class="admin-block">
    <h2 class="section-title">Grilles</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Nature Grille</th>
                    <th>Note Maximale</th>
                    <th>Nom Module de Grille</th>
                    <th>Année Début</th>
                    <th>Actions</th>
                    <th>Affichage</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["natureGrille"]) ?></td>
                    <td><?= htmlspecialchars($row["noteMaxGrille"]) ?></td>
                    <td><?= htmlspecialchars($row["nomModuleGrilleEvaluation"]) ?></td>
                    <td><?= htmlspecialchars($row["anneeDebut"]) ?></td>
                    <td>
                        <?= btnModifier("Grille/modifierGrille.php?id_grille=" . $row['IdModeleEval']) ?><br>
                        <?= btnSupprimer("Grille/supprimerGrille.php?id_grille=" . $row['IdModeleEval']) ?><br>
                        <?php if ($row["anneeDebut"] < 2025): ?>
                            <?= btnCopier("Grille/copierGrille.php?id_grille=" . $row['IdModeleEval']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="Affichage.php?id_grille=<?= $row["IdModeleEval"] ?>" class="btn">📂 Afficher Grille</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <div style="margin-top:32px;text-align:center">
            <?= btnAjouter("Grille/ajouterGrille.php", "Créer une nouvelle grille vierge") ?>
        </div>
    <?php else: ?>
        <p class="section-title">Aucune donnée trouvée.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn->close();
?>