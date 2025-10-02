<?php

require_once "../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Récupérer la liste des étudiants (BUT2 et BUT3 separé)
$sql = "SELECT e.IdEtudiant, e.nom, e.prenom
        FROM EtudiantsBUT2ou3 e
        JOIN AnneeStage a ON e.IdEtudiant = a.IdEtudiant
        WHERE a.but3sinon2 = FALSE AND a.anneeDebut = YEAR(CURDATE())";
$etudiantsBUT2 = $pdo->query($sql)->fetchAll();

$sql = "SELECT e.IdEtudiant, e.nom, e.prenom
        FROM EtudiantsBUT2ou3 e
        JOIN AnneeStage a ON e.IdEtudiant = a.IdEtudiant
        WHERE a.but3sinon2 = TRUE AND a.anneeDebut = YEAR(CURDATE())";
$etudiantsBUT3 = $pdo->query($sql)->fetchAll();

// Messages
$message = "";
if (isset($_GET['success'])) {
    $message = "✅ Action réussie.";
} elseif (isset($_GET['err'])) {
    switch ($_GET['err']) {
        case 'same':
            $message = "⚠️ Le tuteur et le second enseignant doivent être différents.";
            break;
        case 'invalid':
            $message = "⚠️ Requête invalide.";
            break;
        case 'badteacher':
            $message = "⚠️ Enseignant introuvable.";
            break;
        case 'nosalle':
            $message = "⚠️ Aucune salle disponible dans la table Salles (créez-en au moins une).";
            break;
        case 'dberror':
        default:
            $message = "⚠️ Erreur serveur (base de données).";
            break;
    }
}
if ($message) echo "<p style='color: #c33; font-weight: bold;'>".htmlspecialchars($message)."</p>";


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Gestion des Soutenances</title> 
    <link rel="stylesheet" href="../stylee.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php include 'navbarAdmin.php'; ?>

<div class="admin-block">

<!-- Tableau BUT2 -->
<h2>Étudiants deuxième année (BUT2)</h2>
<table class="tableEtudiants">
    <thead>
        <tr>
            <th >Étudiant</th>
            <th >Tuteur</th>
            <th >Enseignant Secondaire</th>
            <th >Soutenance</th>
            <th >Date</th>
            <th >Salle</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($etudiantsBUT2 as $etu): ?>
        <?php
        
        $sql = "
            SELECT 'stage' AS type, IdEvalStage AS id, date_h AS date, IdSalle, IdEnseignantTuteur, IdSecondEnseignant
            FROM EvalStage WHERE IdEtudiant = :id
            UNION
            SELECT 'anglais' AS type, IdEvalAnglais AS id, dateS AS date, IdSalle, IdEnseignant, NULL
            FROM EvalAnglais WHERE IdEtudiant = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        $tuteurNom = "-";
        $secondEnseignant ="-";

        // Enseignant Tuteur/Second (Stage & Portfolio)
        if ($soutenance && $soutenance['type'] === 'stage') {
            if ($soutenance['IdEnseignantTuteur'])
            {
                $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
                $stmt->execute(['id' => $soutenance['IdEnseignantTuteur']]);
                $tuteur = $stmt->fetch();
                if ($tuteur) {
                    $tuteurNom = htmlspecialchars($tuteur['nom'] . " " . $tuteur['prenom']);
                }

            }

            if ($soutenance["IdSecondEnseignant"])
            {
                $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
                $stmt->execute(['id' => $soutenance['IdSecondEnseignant']]);
                $second = $stmt->fetch();
                if ($second) {
                    $secondEnseignant = htmlspecialchars($second['nom'] . " " . $second['prenom']);
                }
            }
        }

        ?>
        <tr>
            <td><?= htmlspecialchars($etu['nom'] . " " . $etu['prenom']) ?></td>

            <td>
                <?php if ($soutenance): ?>
                    <form action="Partie3.2/SaveEnseignant.php?type=stage" method="post">
                        <input type="hidden" name="idEtudiant" value="<?= $etu['IdEtudiant'] ?>">
                        <select name="tuteur" onchange="this.form.submit()">
                            <option value="">-- Choisir --</option>
                            <?php
                                $enseignants = $pdo->query("SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom")->fetchAll();
                                foreach ($enseignants as $ens) {
                                    $selected = ($soutenance['IdEnseignantTuteur'] == $ens['IdEnseignant']) ? "selected" : "";
                                    echo "<option value='{$ens['IdEnseignant']}' $selected>" . htmlspecialchars($ens['nom']." ".$ens['prenom']) . "</option>";
                                }
                            ?>
                        </select>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <td>
                <?php if ($soutenance): ?>
                    <form action="Partie3.2/SaveEnseignant.php?type=stage" method="post">
                        <input type="hidden" name="idEtudiant" value="<?= $etu['IdEtudiant'] ?>">
                        <select name="second" onchange="this.form.submit()">
                            <option value="">-- Choisir --</option>
                            <?php
                                foreach ($enseignants as $ens) {
                                    $selected = ($soutenance['IdSecondEnseignant'] == $ens['IdEnseignant']) ? "selected" : "";
                                    echo "<option value='{$ens['IdEnseignant']}' $selected>" . htmlspecialchars($ens['nom']." ".$ens['prenom']) . "</option>";
                                }
                            ?>
                        </select>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <?php if ($soutenance): ?>
                <td><?= $soutenance['type'] === 'stage' ? "Portfolio & Stage" : "Anglais" ?></td>
                <td><?= htmlspecialchars($soutenance['date']) ?></td>
                <td><?= htmlspecialchars($soutenance['IdSalle']) ?></td>
                <td>
                    <a href="Partie3.2/EditSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>"><button>✏️ Modifier</button></a>
                    <a href="Partie3.2/DeleteSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>" onclick="return confirm('Supprimer cette soutenance ?')"><button>❌ Supprimer</button></a>
                </td>
            <?php else: ?>
                <td colspan="3">Aucune soutenance</td>
                <td>
                    <a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>&type=stage">
                        <button>➕ Ajouter</button>
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

    <!-- Tableau BUT3 -->
    <h2>Étudiants troisième année (BUT3)</h2>
    <h3> Portfolio & Stage </h3> <!-- Portfolio & Stage -->
    <table class="tableEtudiants">
    <thead>
        <tr>
            <th >Étudiant</th>
            <th >Tuteur</th>
            <th >Enseignant Secondaire</th>
            <th >Soutenance</th>
            <th >Date</th>
            <th >Salle</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($etudiantsBUT3 as $etu): ?>
        <?php

        $sql = "
            SELECT 'stage' AS type, IdEvalStage AS id, date_h AS date, IdSalle, IdEnseignantTuteur, IdSecondEnseignant
            FROM EvalStage WHERE IdEtudiant = :id
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        $tuteurNom = "-";
        $secondEnseignant = "-";

        // Enseignant tuteur/second
        if ($soutenance && $soutenance['type'] === 'stage') {
            if ($soutenance['IdEnseignantTuteur'])
            {
                $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
                $stmt->execute(['id' => $soutenance['IdEnseignantTuteur']]);
                $tuteur = $stmt->fetch();
                if ($tuteur) {
                    $tuteurNom = htmlspecialchars($tuteur['nom'] . " " . $tuteur['prenom']);
                }
            }

            if ($soutenance["IdSecondEnseignant"])
            {
                $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
                $stmt->execute(['id' => $soutenance['IdSecondEnseignant']]);
                $second = $stmt->fetch();
                if ($second) {
                    $secondEnseignant = htmlspecialchars($second['nom'] . " " . $second['prenom']);
                }
            }
            
        }

        ?>
        <tr>
            <td><?= htmlspecialchars($etu['nom'] . " " . $etu['prenom']) ?></td>
            
            <td>
                <?php if ($soutenance): ?>
                    <form action="Partie3.2/SaveEnseignant.php?type=stage" method="post">
                        <input type="hidden" name="idEtudiant" value="<?= $etu['IdEtudiant'] ?>">
                        <select name="tuteur" onchange="this.form.submit()">
                            <option value="">-- Choisir --</option>
                            <?php
                                $enseignants = $pdo->query("SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom")->fetchAll();
                                foreach ($enseignants as $ens) {
                                    $selected = ($soutenance['IdEnseignantTuteur'] == $ens['IdEnseignant']) ? "selected" : "";
                                    echo "<option value='{$ens['IdEnseignant']}' $selected>" . htmlspecialchars($ens['nom']." ".$ens['prenom']) . "</option>";
                                }
                            ?>
                        </select>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <td>
                <?php if ($soutenance): ?>
                    <form action="Partie3.2/SaveEnseignant.php?type=stage" method="post">
                        <input type="hidden" name="idEtudiant" value="<?= $etu['IdEtudiant'] ?>">
                        <select name="second" onchange="this.form.submit()">
                            <option value="">-- Choisir --</option>
                            <?php
                                foreach ($enseignants as $ens) {
                                    $selected = ($soutenance['IdSecondEnseignant'] == $ens['IdEnseignant']) ? "selected" : "";
                                    echo "<option value='{$ens['IdEnseignant']}' $selected>" . htmlspecialchars($ens['nom']." ".$ens['prenom']) . "</option>";
                                }
                            ?>
                        </select>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <?php if ($soutenance): ?>
                <td><?= $soutenance['type'] === 'stage' ? "Portfolio & Stage" : "Anglais" ?></td>
                <td><?= htmlspecialchars($soutenance['date']) ?></td>
                <td><?= htmlspecialchars($soutenance['IdSalle']) ?></td>
                <td>
                    <a href="Partie3.2/EditSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>"><button>✏️ Modifier</button></a>
                    <a href="Partie3.2/DeleteSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>" onclick="return confirm('Supprimer cette soutenance ?')"><button>❌ Supprimer</button></a>
                </td>
            <?php else: ?>
                <td colspan="3">Aucune soutenance</td>
                <td>
                    <a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>&type=stage">
                        <button>➕ Ajouter</button>
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

    <h3> Anglais </h3> <!-- Anglais -->
    <table class="tableEtudiants">
    <thead>
        <tr>
            <th >Étudiant</th>
            <th >Tuteur</th>
            <th >Soutenance</th>
            <th >Date</th>
            <th >Salle</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($etudiantsBUT3 as $etu): ?>
        <?php
        $sql = "
            SELECT 'anglais' AS type, IdEvalAnglais AS id, dateS AS date, IdSalle, IdEnseignant AS IdEnseignantTuteur, NULL
            FROM EvalAnglais WHERE IdEtudiant = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        $tuteurNom = "-";
        $secondEnseignant = "-";

        // Enseignant Tuteur (Anglais)
        if ($soutenance && $soutenance['type'] === 'anglais') {
            if ($soutenance['IdEnseignantTuteur'])
            {
                $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
                $stmt->execute(['id' => $soutenance['IdEnseignantTuteur']]);
                $tuteur = $stmt->fetch();
                if ($tuteur) {
                    $tuteurNom = htmlspecialchars($tuteur['nom'] . " " . $tuteur['prenom']);
                }
            }
        }
        ?>
        <tr>
            <td><?= htmlspecialchars($etu['nom'] . " " . $etu['prenom']) ?></td>

            <td>
                <?php if ($soutenance): ?>
                    <form action="Partie3.2/SaveEnseignant.php?type=anglais" method="post">
                        <input type="hidden" name="idEtudiant" value="<?= $etu['IdEtudiant'] ?>">
                        <select name="tuteur" onchange="this.form.submit()">
                            <option value="">-- Choisir --</option>
                            <?php
                                $enseignants = $pdo->query("SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom")->fetchAll();
                                foreach ($enseignants as $ens) {
                                    $selected = ($soutenance['IdEnseignantTuteur'] == $ens['IdEnseignant']) ? "selected" : "";
                                    echo "<option value='{$ens['IdEnseignant']}' $selected>" . htmlspecialchars($ens['nom']." ".$ens['prenom']) . "</option>";
                                }
                            ?>
                        </select>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

             
            <?php if ($soutenance): ?>
                <td><?= $soutenance['type'] === 'stage' ? "Portfolio & Stage" : "Anglais" ?></td>
                <td><?= htmlspecialchars($soutenance['date']) ?></td>
                <td><?= htmlspecialchars($soutenance['IdSalle']) ?></td>
                <td>
                    <a href="Partie3.2/EditSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>"><button>✏️ Modifier</button></a>
                    <a href="Partie3.2/DeleteSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>" onclick="return confirm('Supprimer cette soutenance ?')"><button>❌ Supprimer</button></a>
                </td>
            <?php else: ?>
                <td colspan="3">Aucune soutenance</td>
                <td>
                <a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>&type=anglais">
                    <button>➕ Ajouter</button>
                </a>
                </td>

            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>

    </table>

    <script>
    // Recherche dynamique sur les deux tableaux
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll(".tableEtudiants tbody tr");

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
    </script>

</div>

</body>
</html>
