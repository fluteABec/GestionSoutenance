<?php
// Page de consultation simple pour les étudiants
// Partie 3.4 - Version simplifiée

require_once 'config.php';

// Fonction d'erreur agréable
function render_error($title, $msg) {
    http_response_code(403);
    echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>';
    echo '<style>body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fb;color:#333;padding:30px} .card{max-width:820px;margin:30px auto;background:#fff;border-radius:8px;padding:24px;box-shadow:0 6px 18px rgba(0,0,0,0.06)} h1{margin:0 0 8px} p{margin:0}</style></head><body>';
    echo '<div class="card"><h1>' . htmlspecialchars($title) . '</h1><p>' . nl2br(htmlspecialchars($msg)) . '</p></div></body></html>';
    exit;
}

// Accept both token (preferred) and legacy ?id=email fallback
$identifier = null;
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        render_error('Token invalide', 'Le token fourni est invalide.');
    }

    $payloadB64 = $parts[0];
    $signature = $parts[1];

    $payloadJson = base64_decode($payloadB64, true);
    if ($payloadJson === false) {
        render_error('Payload invalide', 'Le token est corrompu.');
    }

    $expectedSig = hash_hmac('sha256', $payloadJson, APP_SECRET);
    if (!hash_equals($expectedSig, $signature)) {
        render_error('Signature invalide', 'La signature du token ne correspond pas.');
    }

    $payload = json_decode($payloadJson, true);
    if (!$payload || !isset($payload['id']) || !isset($payload['exp'])) {
        render_error('Payload manquant', 'Le token ne contient pas les informations attendues.');
    }

    if (time() > (int)$payload['exp']) {
        render_error('Lien expiré', 'Ce lien a expiré. Demandez un nouveau lien à votre administration.');
    }

    $identifier = $payload['id'];
} elseif (isset($_GET['id'])) {
    // Legacy fallback — moins sûr, mais pratique pour les liens déjà envoyés
    $identifier = $_GET['id'];
} else {
    render_error('Accès refusé', 'Aucun identifiant ni token fourni.');
}

// Déterminer le type d'identifiant et récupérer l'étudiant
if (is_numeric($identifier)) {
    $stmt = $pdo->prepare("SELECT IdEtudiant, nom, prenom, mail FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
    $stmt->execute([(int)$identifier]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) render_error('Étudiant introuvable', 'Aucun étudiant trouvé pour cet identifiant.');
    $email = $etudiant['mail'];
} else {
    $email = $identifier;
    $stmt = $pdo->prepare("SELECT IdEtudiant, nom, prenom, mail FROM EtudiantsBUT2ou3 WHERE mail = ?");
    $stmt->execute([$email]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) render_error('Étudiant introuvable', 'Aucun étudiant trouvé pour cette adresse mail.');
}

// Récupération des informations de l'étudiant
$stmt = $pdo->prepare("
    SELECT 
        e.IdEtudiant,
        e.nom,
        e.prenom,
        e.mail,
        an.but3sinon2,
        an.alternanceBUT3,
        ent.nom as nomEntreprise,
        ent.villeE,
        ent.codePostal,
        an.sujet,
        an.nomMaitreStageApp,
        an.noteEntreprise
    FROM EtudiantsBUT2ou3 e
    LEFT JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
    LEFT JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
    WHERE e.mail = ?
");
$stmt->execute([$email]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    die('Étudiant non trouvé');
}

// Vérifier que les résultats sont diffusés
$stmt = $pdo->prepare("
    SELECT 
        es.Statut as statutStage,
        ep.Statut as statutPortfolio,
        ea.Statut as statutAnglais
    FROM EvalStage es
    LEFT JOIN EvalPortFolio ep ON es.IdEtudiant = ep.IdEtudiant AND es.anneeDebut = ep.anneeDebut
    LEFT JOIN EvalAnglais ea ON es.IdEtudiant = ea.IdEtudiant AND es.anneeDebut = ea.anneeDebut
    WHERE es.IdEtudiant = ? AND es.anneeDebut = YEAR(CURDATE())
");
$stmt->execute([$etudiant['IdEtudiant']]);
$statuts = $stmt->fetch();

if (!$statuts || $statuts['statutStage'] !== 'DIFFUSEE') {
    die('Résultats non disponibles ou non diffusés');
}

// Récupération des évaluations
$evaluations = [];

// Évaluation de stage
$stmt = $pdo->prepare("
    SELECT 
        es.note,
        es.commentaireJury,
        es.date_h,
        et.nom as nomTuteur,
        et.prenom as prenomTuteur,
        mg.noteMaxGrille
    FROM EvalStage es
    INNER JOIN Enseignants et ON es.IdEnseignantTuteur = et.IdEnseignant
    INNER JOIN ModelesGrilleEval mg ON es.IdModeleEval = mg.IdModeleEval
    WHERE es.IdEtudiant = ? AND es.anneeDebut = YEAR(CURDATE())
");
$stmt->execute([$etudiant['IdEtudiant']]);
$evaluations['stage'] = $stmt->fetch();

// Évaluation de portfolio
$stmt = $pdo->prepare("
    SELECT 
        ep.note,
        ep.commentaireJury,
        mg.noteMaxGrille
    FROM EvalPortFolio ep
    INNER JOIN ModelesGrilleEval mg ON ep.IdModeleEval = mg.IdModeleEval
    WHERE ep.IdEtudiant = ? AND ep.anneeDebut = YEAR(CURDATE())
");
$stmt->execute([$etudiant['IdEtudiant']]);
$evaluations['portfolio'] = $stmt->fetch();

// Évaluation d'anglais (si BUT3)
if ($etudiant['but3sinon2']) {
    $stmt = $pdo->prepare("
        SELECT 
            ea.note,
            ea.commentaireJury,
            ea.dateS,
            e.nom as nomEnseignant,
            e.prenom as prenomEnseignant,
            mg.noteMaxGrille
        FROM EvalAnglais ea
        INNER JOIN Enseignants e ON ea.IdEnseignant = e.IdEnseignant
        INNER JOIN ModelesGrilleEval mg ON ea.IdModeleEval = mg.IdModeleEval
        WHERE ea.IdEtudiant = ? AND ea.anneeDebut = YEAR(CURDATE())
    ");
    $stmt->execute([$etudiant['IdEtudiant']]);
    $evaluations['anglais'] = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats d'évaluation - <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></title>
    <style>
        body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f4f6fb; color:#222; padding:24px}
        .card{max-width:980px;margin:18px auto;background:#fff;border-radius:10px;padding:24px;box-shadow:0 10px 30px rgba(20,30,50,0.06)}
        h1{margin:0 0 6px;font-size:20px}
        .muted{color:#6b7280}
        .student-info{display:flex;gap:18px;flex-wrap:wrap}
        .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
        .niveau-badge{display:inline-block;padding:6px 10px;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:600}
        .evaluation-card{margin-top:18px;padding:14px;border-radius:8px;background:#fbfbff}
        .note-display{display:flex;align-items:baseline;gap:12px}
        .note-number{font-size:28px;font-weight:700}
        .note-max{color:#6b7280}
        table{width:100%;border-collapse:collapse;margin-top:8px}
        table th, table td{border:1px solid #e6e9ef;padding:6px;text-align:left}
        .summary-grid{display:flex;gap:12px;flex-wrap:wrap;margin-top:8px}
        .summary-item{background:#fff;padding:10px;border-radius:8px;box-shadow:0 4px 10px rgba(20,30,50,0.04)}
        .footer{margin-top:18px;color:#6b7280;font-size:13px}
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>📊 Résultats d'évaluation</h1>
            <p class="muted">Année universitaire <?= date('Y') ?>-<?= date('Y') + 1 ?></p>
        </div>
        
        <div class="student-info">
            <h3>👤 Informations étudiant</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nom :</span>
                    <span class="info-value"><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Niveau :</span>
                    <span class="info-value">
                        <?php
                        $niveau = $etudiant['but3sinon2'] ? 'BUT3' : 'BUT2';
                        $class = $etudiant['but3sinon2'] ? ($etudiant['alternanceBUT3'] ? 'alternance' : 'but3') : 'but2';
                        if ($etudiant['but3sinon2'] && $etudiant['alternanceBUT3']) {
                            $niveau .= ' (Alternance)';
                        }
                        ?>
                        <span class="niveau-badge <?= $class ?>"><?= $niveau ?></span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email :</span>
                    <span class="info-value"><?= htmlspecialchars($etudiant['mail']) ?></span>
                </div>
                <?php if ($etudiant['nomEntreprise']): ?>
                    <div class="info-item">
                        <span class="info-label">Entreprise :</span>
                        <span class="info-value"><?= htmlspecialchars($etudiant['nomEntreprise']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ville :</span>
                        <span class="info-value"><?= htmlspecialchars($etudiant['villeE'] . ' ' . $etudiant['codePostal']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Maître de stage :</span>
                        <span class="info-value"><?= htmlspecialchars($etudiant['nomMaitreStageApp']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sujet :</span>
                        <span class="info-value"><?= htmlspecialchars($etudiant['sujet']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Évaluation de stage -->
        <?php if ($evaluations['stage']): ?>
            <div class="evaluation-card">
                <h3>🎯 Évaluation du stage</h3>
                <div class="note-display">
                    <div class="note-number"><?= number_format($evaluations['stage']['note'], 2) ?></div>
                    <div class="note-max">sur <?= $evaluations['stage']['noteMaxGrille'] ?></div>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de soutenance :</span>
                    <span class="info-value"><?= formatDate($evaluations['stage']['date_h']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Enseignant tuteur :</span>
                    <span class="info-value"><?= htmlspecialchars($evaluations['stage']['prenomTuteur'] . ' ' . $evaluations['stage']['nomTuteur']) ?></span>
                </div>
                <?php if ($evaluations['stage']['commentaireJury']): ?>
                    <div class="comment">
                        <h4>💬 Commentaire du jury</h4>
                        <p><?= nl2br(htmlspecialchars($evaluations['stage']['commentaireJury'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Évaluation de portfolio -->
        <?php if ($evaluations['portfolio']): ?>
            <div class="evaluation-card">
                <h3>📁 Évaluation du portfolio</h3>
                <div class="note-display">
                    <div class="note-number"><?= number_format($evaluations['portfolio']['note'], 2) ?></div>
                    <div class="note-max">sur <?= $evaluations['portfolio']['noteMaxGrille'] ?></div>
                </div>
                <?php if ($evaluations['portfolio']['commentaireJury']): ?>
                    <div class="comment">
                        <h4>💬 Commentaire du jury</h4>
                        <p><?= nl2br(htmlspecialchars($evaluations['portfolio']['commentaireJury'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Évaluation d'anglais (BUT3 seulement) -->
        <?php if ($etudiant['but3sinon2'] && $evaluations['anglais']): ?>
            <div class="evaluation-card">
                <h3>🇬🇧 Évaluation de la soutenance en anglais</h3>
                <div class="note-display">
                    <div class="note-number"><?= number_format($evaluations['anglais']['note'], 2) ?></div>
                    <div class="note-max">sur <?= $evaluations['anglais']['noteMaxGrille'] ?></div>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de soutenance :</span>
                    <span class="info-value"><?= formatDate($evaluations['anglais']['dateS']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Enseignant :</span>
                    <span class="info-value"><?= htmlspecialchars($evaluations['anglais']['prenomEnseignant'] . ' ' . $evaluations['anglais']['nomEnseignant']) ?></span>
                </div>
                <?php if ($evaluations['anglais']['commentaireJury']): ?>
                    <div class="comment">
                        <h4>💬 Commentaire du jury</h4>
                        <p><?= nl2br(htmlspecialchars($evaluations['anglais']['commentaireJury'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Note entreprise -->
        <?php if ($etudiant['noteEntreprise']): ?>
            <div class="evaluation-card">
                <h3>🏢 Note de l'entreprise</h3>
                <div class="note-display">
                    <div class="note-number"><?= number_format($etudiant['noteEntreprise'], 2) ?></div>
                    <div class="note-max">sur 20</div>
                </div>
                <p style="text-align: center; color: #6c757d; font-style: italic;">Cette note a été fournie par l'entreprise d'accueil.</p>
            </div>
        <?php endif; ?>
        
        <!-- Résumé global -->
        <div class="summary">
            <h3>📈 Résumé des évaluations</h3>
            <div class="summary-grid">
                <?php if ($evaluations['stage']): ?>
                    <div class="summary-item">
                        <strong>Stage</strong>
                        <div class="note"><?= number_format($evaluations['stage']['note'], 2) ?>/<?= $evaluations['stage']['noteMaxGrille'] ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($evaluations['portfolio']): ?>
                    <div class="summary-item">
                        <strong>Portfolio</strong>
                        <div class="note"><?= number_format($evaluations['portfolio']['note'], 2) ?>/<?= $evaluations['portfolio']['noteMaxGrille'] ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($etudiant['but3sinon2'] && $evaluations['anglais']): ?>
                    <div class="summary-item">
                        <strong>Anglais</strong>
                        <div class="note"><?= number_format($evaluations['anglais']['note'], 2) ?>/<?= $evaluations['anglais']['noteMaxGrille'] ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($etudiant['noteEntreprise']): ?>
                    <div class="summary-item">
                        <strong>Entreprise</strong>
                        <div class="note"><?= number_format($etudiant['noteEntreprise'], 2) ?>/20</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>🔒 Ces résultats sont confidentiels et personnels</strong></p>
            <p>Date de consultation : <?= date('d/m/Y H:i') ?></p>
            <p>IUT - Département Informatique</p>
        </div>
    </div>
</body>
</html>
