<?php
// Page de consultation simple pour les étudiants
// Partie 3.4 - Version simplifiée

require_once 'config.php';

// Sécurisation : attendre un token signé plutôt qu'un id en clair
if (!isset($_GET['token'])) {
    die('Token manquant');
}

$token = $_GET['token'];
$parts = explode('.', $token);
if (count($parts) !== 2) {
    die('Token invalide');
}

$payloadB64 = $parts[0];
$signature = $parts[1];

$payloadJson = base64_decode($payloadB64, true);
if ($payloadJson === false) {
    die('Payload invalide');
}

$expectedSig = hash_hmac('sha256', $payloadJson, APP_SECRET);
if (!hash_equals($expectedSig, $signature)) {
    die('Signature invalide');
}

$payload = json_decode($payloadJson, true);
if (!$payload || !isset($payload['id']) || !isset($payload['exp'])) {
    die('Payload manquant');
}

if (time() > (int)$payload['exp']) {
    die('Le lien a expiré');
}

// Déterminer l'identifiant (email ou IdEtudiant)
$identifier = $payload['id'];

if (is_numeric($identifier)) {
    // chercher par IdEtudiant
    $stmt = $pdo->prepare("SELECT IdEtudiant, nom, prenom, mail FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
    $stmt->execute([(int)$identifier]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) die('Étudiant introuvable');
    $email = $etudiant['mail'];
} else {
    // chercher par email
    $email = $identifier;
    $stmt = $pdo->prepare("SELECT IdEtudiant, nom, prenom, mail FROM EtudiantsBUT2ou3 WHERE mail = ?");
    $stmt->execute([$email]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) die('Étudiant introuvable');
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
    
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Résultats d'évaluation</h1>
            <p>Année universitaire <?= date('Y') ?>-<?= date('Y') + 1 ?></p>
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
