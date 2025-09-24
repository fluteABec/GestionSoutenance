<?php

require_once "/opt/lampp/htdocs/projet_sql/db.php";

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
    $message = "✅ Soutenance mise à jour avec succès.";
} elseif (isset($_GET['deleted'])) {
    $message = "🗑️ Soutenance supprimée avec succès.";
} elseif (isset($_GET['added'])) {
    $message = "✅ Soutenance ajoutée avec succès.";
}
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

<div class="navbar">
    <div class="brand"><span class="logo"></span><span>Administration</span></div>
    <a class="nav-item" href="Partie3.1/3_1_natan.php">Tâches enseignants</a>
    <a class="nav-item" href="Partie3.3/index.php">Évaluations IUT</a>
    <a class="nav-item" href="Partie3.4/index.php">Diffusion résultats</a>
</div>

<!-- Barre de recherche -->
<input type="text" id="searchInput" placeholder="🔍 Rechercher un étudiant...">

<!-- Tableau BUT2 -->
<h2>Étudiants deuxième année (BUT2)</h2>
<table class="tableEtudiants">
    <thead>
        <tr>
            <th>Étudiant</th>
            <th>Tuteur</th>
            <th>Enseignant Secondaire</th>
            <th>Soutenance</th>
            <th>Date</th>
            <th>Salle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($etudiantsBUT2 as $etu): ?>
        <?php
        $sql = "
            SELECT 'stage' AS type, IdEvalStage AS id, date_h AS date, IdSalle, IdEnseignantTuteur, IdSecondEnseignant
            FROM EvalStage WHERE IdEtudiant = :id
            UNION
            SELECT 'anglais' AS type, IdEvalAnglais AS id, dateS AS date, IdSalle, NULL, NULL
            FROM EvalAnglais WHERE IdEtudiant = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        $tuteurNom = "-";
        $secondEnseignant ="-";

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
            <td><?= $tuteurNom ?></td>
            <td><?= $secondEnseignant ?></td>
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
                <td><a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>"><button>➕ Ajouter</button></a></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Tableau BUT3 -->
<h2>Étudiants troisième année (BUT3)</h2>
<table class="tableEtudiants">
    <thead>
        <tr>
            <th>Étudiant</th>
            <th>Tuteur</th>
            <th>Soutenance</th>
            <th>Date</th>
            <th>Salle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($etudiantsBUT3 as $etu): ?>
        <?php
        $sql = "
            SELECT 'stage' AS type, IdEvalStage AS id, date_h AS date, IdSalle, IdEnseignantTuteur
            FROM EvalStage WHERE IdEtudiant = :id
            UNION
            SELECT 'anglais' AS type, IdEvalAnglais AS id, dateS AS date, IdSalle, NULL
            FROM EvalAnglais WHERE IdEtudiant = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        $tuteurNom = "-";
        if ($soutenance && $soutenance['type'] === 'stage' && $soutenance['IdEnseignantTuteur']) {
            $stmt = $pdo->prepare("SELECT nom, prenom FROM Enseignants WHERE IdEnseignant = :id");
            $stmt->execute(['id' => $soutenance['IdEnseignantTuteur']]);
            $tuteur = $stmt->fetch();
            if ($tuteur) {
                $tuteurNom = htmlspecialchars($tuteur['nom'] . " " . $tuteur['prenom']);
            }
        }
        ?>
        <tr>
            <td><?= htmlspecialchars($etu['nom'] . " " . $etu['prenom']) ?></td>
            <td><?= $tuteurNom ?></td>
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
                <td><a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>"><button>➕ Ajouter</button></a></td>
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

</body>
</html>
