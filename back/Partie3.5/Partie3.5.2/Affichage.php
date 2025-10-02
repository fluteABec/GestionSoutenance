<?php
include("Bouton.php");

$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifier qu'une grille est sélectionnée
if (!isset($_GET['id_grille'])) {
    die("Erreur : aucune grille sélectionnée.");
}

$id_grille = intval($_GET['id_grille']);

// Récupérer la grille
$sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = $id_grille";
$res = $conn->query($sql);
if ($res->num_rows == 0) die("Grille non trouvée.");
$grille = $res->fetch_assoc();

// Récupérer les sections
$sql = "SELECT s.IdSection, sc.titre, sc.description
        FROM sectionseval s
        JOIN sectioncritereeval sc ON s.IdSection = sc.IdSection
        WHERE s.IdModeleEval = $id_grille";
$sections = $conn->query($sql);
?>

<h2>Grille : <?php echo htmlspecialchars($grille['nomModuleGrilleEvaluation']); ?></h2>
<p>Type : <?php echo htmlspecialchars($grille['natureGrille']); ?></p>
<p>Note Maximale : <?php echo htmlspecialchars($grille['noteMaxGrille']); ?></p>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../stylee.css">
</head>
<body>
    <form action="simulation.php" method="post">
    <input type="hidden" name="id_grille" value="<?php echo $id_grille; ?>">

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Critères</th>
            <th>Actions</th>
        </tr>

        <?php while ($sec = $sections->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($sec['titre']); ?></td>
                <td><?php echo htmlspecialchars($sec['description']); ?></td>
                <td>
                    <!-- Tableau interne pour les critères -->
                    <table border="1" cellpadding="3" cellspacing="0" width="100%">
                        <tr>
                            <th>Description Courte</th>
                            <th>Description Longue</th>
                            <th>Note Max</th>
                            <th>Votre Note</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        $id_section = $sec['IdSection'];
                        $sql_crit = "SELECT c.IdCritere, c.descCourte, c.descLongue, sc.valeurMaxCritereEval
                                     FROM critereseval c
                                     JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
                                     WHERE sc.IdSection = $id_section";
                        $crit_res = $conn->query($sql_crit);

                        if ($crit_res->num_rows > 0) {
                            while ($crit = $crit_res->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($crit['descCourte']) . "</td>";
                                echo "<td>" . htmlspecialchars($crit['descLongue']) . "</td>";
                                echo "<td>" . htmlspecialchars($crit['valeurMaxCritereEval']) . "</td>";

                                // Champ de saisie pour la simulation
                                echo "<td>
                                    <input type='number' 
                                    name='critere[" . $crit['IdCritere'] . "]' 
                                    min='0' 
                                    max='" . $crit['valeurMaxCritereEval'] . "' 
                                    step='0.01'>
                                </td>";

                                echo "<td>
                                        <a href='Critere/modifierCritere.php?id_critere=" . $crit['IdCritere'] . "&id_section=$id_section&id_grille=$id_grille'>✏️ Modifier</a> <br><br>
                                        <a href='Critere/supprimerCritere.php?id_critere=" . $crit['IdCritere'] . "&id_section=$id_section&id_grille=$id_grille' onclick='return confirm(\"Supprimer ce critère ?\")'>🗑️ Supprimer</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Aucun critère</td></tr>";
                        }
                        ?>
                        <tr>
                            <td colspan="5">
                                <a href="Critere/ajouterCritere.php?id_section=<?php echo $id_section; ?>&id_grille=<?php echo $id_grille; ?>">➕ Ajouter un critère</a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <a href="Section/modifierSection.php?id_section=<?php echo $sec['IdSection']; ?>&id_grille=<?php echo $id_grille; ?>">✏️ Modifier</a><br><br>
                    <a href="Section/supprimerSection.php?id_section=<?php echo $sec['IdSection']; ?>&id_grille=<?php echo $id_grille; ?>" onclick="return confirm('Supprimer cette section et ses critères ?')">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <button type="submit">▶️ Lancer une simulation de note</button>
</form>

<!-- Bouton ajouter une section -->
<br>
<a href="Section/ajouterSection.php?id_grille=<?php echo $id_grille; ?>">➕ Ajouter une section</a> <br>

<br>
<a href="Grille.php">📂 Retour aux grilles</a>
</body>
</html>


