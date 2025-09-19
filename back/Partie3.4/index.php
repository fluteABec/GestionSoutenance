<?php
// Page d'accueil simple pour tester le système
// Partie 3.4 - Outils de diffusion des résultats

require_once 'config.php';

// Statistiques simples
$stats = [];

// Nombre d'étudiants candidats à la diffusion
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.IdEtudiant) as candidats
    FROM EtudiantsBUT2ou3 e
    INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalPortFolio ep ON e.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalAnglais ea ON e.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
    WHERE es.Statut = 'REMONTEE' 
    AND ep.Statut = 'REMONTEE'
    AND (an.but3sinon2 = FALSE OR ea.Statut = 'REMONTEE')
");
$stmt->execute();
$stats['candidats'] = $stmt->fetchColumn();

// Nombre d'étudiants déjà diffusés
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.IdEtudiant) as diffuses
    FROM EtudiantsBUT2ou3 e
    INNER JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
    WHERE es.Statut = 'DIFFUSEE'
");
$stmt->execute();
$stats['diffuses'] = $stmt->fetchColumn();

// Nombre total d'étudiants en stage
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM AnneeStage 
    WHERE anneeDebut = YEAR(CURDATE())
");
$stmt->execute();
$stats['total'] = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partie 3.4 - Test du système</title>
    <link rel="stylesheet" href="../../3.4.css">
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container">
        <div class="header">
            <h1>Partie 3.4 - Outils de diffusion des résultats</h1>
            <p>Test du système de diffusion des résultats aux étudiants</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Étudiants en stage</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['candidats'] ?></div>
                <div class="stat-label">Candidats à la diffusion</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['diffuses'] ?></div>
                <div class="stat-label">Déjà diffusés</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="partie_3_4_simple.php" class="btn btn-success">📧 Gérer la diffusion</a>
        </div>
        
        <div class="info-section">
            <h3>📋 Règles de diffusion</h3>
            <ul>
                <li><strong>BUT2 :</strong> Grilles de stage ET portfolio remontées</li>
                <li><strong>BUT3 :</strong> Grilles de stage ET portfolio ET anglais remontées</li>
                <li><strong>Action irréversible :</strong> Une fois diffusée, la grille ne peut plus être modifiée</li>
                <li><strong>Email automatique :</strong> Chaque étudiant reçoit un lien de consultation</li>
            </ul>
        </div>
    </div>
</body>
</html>