<?php
// Partie 3.4 - Outils de diffusion des résultats (Version simplifiée)
// Pour cours en groupe

require_once 'config.php';

// Fonction pour récupérer les étudiants candidats à la diffusion
function getEtudiantsCandidats($pdo) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT e.IdEtudiant, e.nom, e.prenom, e.mail, an.but3sinon2, an.alternanceBUT3, ent.nom as entreprise
        FROM EtudiantsBUT2ou3 e
        INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
        INNER JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
        LEFT JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
        LEFT JOIN EvalPortFolio ep ON e.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
        LEFT JOIN EvalAnglais ea ON e.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
        WHERE es.Statut = 'REMONTEE' 
        AND ep.Statut = 'REMONTEE'
        AND (an.but3sinon2 = FALSE OR ea.Statut = 'REMONTEE')
        ORDER BY e.nom, e.prenom
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fonction pour diffuser les résultats d'un étudiant
function diffuserResultats($pdo, $etudiantId) {
    try {
        $pdo->beginTransaction();
        
        // Mettre à jour les statuts vers "DIFFUSEE"
        $stmt = $pdo->prepare("UPDATE EvalStage SET Statut = 'DIFFUSEE' WHERE IdEtudiant = ? AND anneeDebut = YEAR(CURDATE())");
        $stmt->execute([$etudiantId]);
        
        $stmt = $pdo->prepare("UPDATE EvalPortFolio SET Statut = 'DIFFUSEE' WHERE IdEtudiant = ? AND anneeDebut = YEAR(CURDATE())");
        $stmt->execute([$etudiantId]);
        
        // Vérifier si c'est un BUT3 (anglais)
        $stmt = $pdo->prepare("SELECT but3sinon2 FROM AnneeStage WHERE IdEtudiant = ? AND anneeDebut = YEAR(CURDATE())");
        $stmt->execute([$etudiantId]);
        $isBut3 = $stmt->fetchColumn();
        
        if ($isBut3) {
            $stmt = $pdo->prepare("UPDATE EvalAnglais SET Statut = 'DIFFUSEE' WHERE IdEtudiant = ? AND anneeDebut = YEAR(CURDATE())");
            $stmt->execute([$etudiantId]);
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Fonction pour envoyer un email simple
// Fonction pour envoyer un email réel avec PHPMailer
function envoyerEmailSimple($email, $nom, $prenom, $etudiantId = null) {
    // Sujet + contenu
    $sujet = "Vos résultats d'évaluation - " . date('Y');
    // Génération d'un token signé (id étudiant + expiration)
    $expireSeconds = 60 * 60 * 24 * 7; // 7 jours
    $expiresAt = time() + $expireSeconds;

    // Si on n'a pas l'Id étudiant, tombe back sur email encodé (moins sûr)
    $payload = json_encode([
        'id' => $etudiantId ?? $email,
        'exp' => $expiresAt
    ]);

    $signature = hash_hmac('sha256', $payload, APP_SECRET);
    $token = base64_encode($payload) . '.' . $signature;

    // Construire une URL absolue robuste : privilégier l'hôte courant si disponible
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base = $scheme . '://' . $_SERVER['HTTP_HOST'];
        $scriptDir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        if ($scriptDir === '.' || $scriptDir === '/' || $scriptDir === '\\') {
            $scriptDir = '';
        }
        $lien = $base . $scriptDir . '/consultation_simple.php?token=' . urlencode($token);
    } else {
        // Fallback sur APP_URL si le script est exécuté en contexte sans _SERVER
        $lien = rtrim(APP_URL, '/') . '/consultation_simple.php?token=' . urlencode($token);
    }

    $message = "Bonjour $prenom $nom,\n\n";
    $message .= "Vos résultats d'évaluation sont disponibles.\n";
    $message .= "Cliquez sur ce lien pour les consulter :\n";
    $message .= "$lien\n";
    $message .= "Ce lien expirera dans 7 jours.\n\n";
    $message .= "Cordialement,\nL'équipe pédagogique";

    // Inclure PHPMailer
    require_once __DIR__ . '/../Partie3.3/vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Config serveur SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'u1840518965@gmail.com';   // ton adresse Gmail
        $mail->Password   = 'ooeo bavi hozw pndl';     // mot de passe d’application
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Expéditeur
        $mail->setFrom('u1840518965@gmail.com', 'IUT - Administration');

        // Destinataire
        $mail->addAddress($email, "$prenom $nom");

        // Contenu
        $mail->isHTML(false); // email en texte brut
        $mail->Subject = $sujet;
        $mail->Body    = $message;

        // Envoi
        $mail->send();

        // En parallèle, log local
        $logEntry = "[" . date('Y-m-d H:i:s') . "] Email envoyé à $email ($prenom $nom)\n";
        $logEntry .= "Sujet: $sujet\n$message\n---\n\n";
        if (!file_exists('logs')) mkdir('logs', 0755, true);
        file_put_contents('logs/emails.log', $logEntry, FILE_APPEND | LOCK_EX);

        return true;

    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
        return false;
    }
}


// Traitement des actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'diffuser_tous') {
            $etudiants = getEtudiantsCandidats($pdo);
            $success = 0;
            foreach ($etudiants as $etudiant) {
                if (diffuserResultats($pdo, $etudiant['IdEtudiant'])) {
                    // Passer l'IdEtudiant pour générer un token sécurisé
                    envoyerEmailSimple($etudiant['mail'], $etudiant['nom'], $etudiant['prenom'], $etudiant['IdEtudiant']);
                    $success++;
                }
            }
            $message = "Diffusion terminée : $success étudiants ont reçu leurs résultats.";
        }
    }
}

$etudiantsCandidats = getEtudiantsCandidats($pdo);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partie 3.4 - Diffusion des résultats</title>
    <link rel="stylesheet" href="../../stylee.css">
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="admin-block">
        <h1 class="section-title">Partie 3.4 - Outils de diffusion des résultats</h1>
        <?php if ($message): ?>
            <div class="alert alert-success" style="font-weight:600;color:var(--teal);margin-bottom:16px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <div class="info-section">
            <h3>📋 Règles de diffusion :</h3>
            <ul>
                <li><strong>BUT2 :</strong> Grilles de stage ET portfolio remontées</li>
                <li><strong>BUT3 :</strong> Grilles de stage ET portfolio ET anglais remontées</li>
                <li><strong>Action irréversible :</strong> Une fois diffusée, la grille ne peut plus être modifiée</li>
            </ul>
        </div>
        <h2 class="section-title">Étudiants candidats à la diffusion</h2>
        <?php if (empty($etudiantsCandidats)): ?>
            <p>Aucun étudiant candidat à la diffusion.</p>
        <?php else: ?>
            <p><strong><?= count($etudiantsCandidats) ?></strong> étudiants peuvent recevoir leurs résultats.</p>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Niveau</th>
                        <th>Entreprise</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiantsCandidats as $etudiant): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></strong></td>
                            <td>
                                <?php
                                $niveau = $etudiant['but3sinon2'] ? 'BUT3' : 'BUT2';
                                $class = $etudiant['but3sinon2'] ? ($etudiant['alternanceBUT3'] ? 'alternance' : 'but3') : 'but2';
                                if ($etudiant['but3sinon2'] && $etudiant['alternanceBUT3']) {
                                    $niveau .= ' (Alternance)';
                                }
                                ?>
                                <span class="niveau <?= $class ?>"><?= $niveau ?></span>
                            </td>
                            <td><?= htmlspecialchars($etudiant['entreprise']) ?></td>
                            <td><?= htmlspecialchars($etudiant['mail']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="actions" style="margin-top:24px;">
                <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir diffuser les résultats à TOUS les étudiants ? Cette action est irréversible !')">
                    <input type="hidden" name="action" value="diffuser_tous">
                    <button type="submit" class="btn btn-danger">📧 Diffuser à tous les candidats</button>
                </form>
            </div>
        <?php endif; ?>
        <h2 class="section-title">Étudiants ayant déjà reçu leurs résultats</h2>
        <?php
        $stmt = $pdo->prepare("
            SELECT e.nom, e.prenom, e.mail, an.but3sinon2, an.alternanceBUT3, ent.nom as entreprise
            FROM EtudiantsBUT2ou3 e
            INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
            INNER JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
            INNER JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
            WHERE es.Statut = 'DIFFUSEE'
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        $etudiantsDiffuses = $stmt->fetchAll();
        ?>
        <?php if (empty($etudiantsDiffuses)): ?>
            <p>Aucun étudiant n'a encore reçu ses résultats.</p>
        <?php else: ?>
            <p><strong><?= count($etudiantsDiffuses) ?></strong> étudiants ont déjà reçu leurs résultats.</p>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Niveau</th>
                        <th>Entreprise</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiantsDiffuses as $etudiant): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></strong></td>
                            <td>
                                <?php
                                $niveau = $etudiant['but3sinon2'] ? 'BUT3' : 'BUT2';
                                $class = $etudiant['but3sinon2'] ? ($etudiant['alternanceBUT3'] ? 'alternance' : 'but3') : 'but2';
                                if ($etudiant['but3sinon2'] && $etudiant['alternanceBUT3']) {
                                    $niveau .= ' (Alternance)';
                                }
                                ?>
                                <span class="niveau <?= $class ?>"><?= $niveau ?></span>
                            </td>
                            <td><?= htmlspecialchars($etudiant['entreprise']) ?></td>
                            <td><?= htmlspecialchars($etudiant['mail']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
                <p><a class="btn-retour" href="index.php">← Retour</a></p>
    </div>
</body>
</html>
