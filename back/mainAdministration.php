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

// Récupérer la liste des étudiants
$sql = "SELECT IdEtudiant, nom, prenom FROM etudiantsbut2ou3 ORDER BY nom, prenom";
$etudiants = $pdo->query($sql)->fetchAll();

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
    <link rel="stylesheet" href="style.css">
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        .actions button { margin: 2px; }
        #searchInput { width: 300px; padding: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>📋 Gestion des Soutenances</h2>

<?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<a href="Partie3.1/3_1_natan.php">
    <button>Visualisation des tâches enseignants</button>
</a>

<a href="Partie3.3/index.php">
    <button>Gestion des Évaluations - IUT</button>
</a>

<a href="Partie3.4/index.php">
    <button>Outils de diffusion des résultats</button>
</a>

<!-- Barre de recherche -->
<input type="text" id="searchInput" placeholder="🔍 Rechercher un étudiant...">

<table id="tableEtudiants">
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
    <?php foreach ($etudiants as $etu): ?>
        <?php
        // Soutenance existante ?
        $sql = "
            SELECT 'stage' AS type, IdEvalStage AS id, date_h AS date, IdSalle, IdEnseignantTuteur
            FROM evalstage WHERE IdEtudiant = :id
            UNION
            SELECT 'anglais' AS type, IdEvalAnglais AS id, dateS AS date, IdSalle, NULL
            FROM evalanglais WHERE IdEtudiant = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $etu['IdEtudiant']]);
        $soutenance = $stmt->fetch();

        // Chercher le tuteur si stage
        $tuteurNom = "-";
        if ($soutenance && $soutenance['type'] === 'stage' && $soutenance['IdEnseignantTuteur']) {
            $stmt = $pdo->prepare("SELECT nom, prenom FROM enseignants WHERE IdEnseignant = :id");
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
                <td class="actions">
                    <a href="Partie3.2/EditSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>">
                        <button>✏️ Modifier</button>
                    </a>
                    <a href="Partie3.2/DeleteSoutenance.php?id=<?= $soutenance['id'] ?>&type=<?= $soutenance['type'] ?>" onclick="return confirm('Supprimer cette soutenance ?')">
                        <button style="color:red;">❌ Supprimer</button>
                    </a>
                </td>
            <?php else: ?>
                <td colspan="3">Aucune soutenance</td>
                <td>
                    <a href="Partie3.2/AddSoutenance.php?idEtudiant=<?= $etu['IdEtudiant'] ?>">
                        <button>➕ Ajouter</button>
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
// Recherche dynamique
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#tableEtudiants tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>
