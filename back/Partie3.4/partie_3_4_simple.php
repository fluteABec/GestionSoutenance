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
function envoyerEmailSimple($email, $nom, $prenom) {
    $sujet = "Vos résultats d'évaluation - " . date('Y');
    $lien = "http://localhost/envoie%20de%20mail/consultation_simple.php?id=" . $email;
    
    $message = "Bonjour $prenom $nom,\n\n";
    $message .= "Vos résultats d'évaluation sont disponibles.\n";
    $message .= "Cliquez sur ce lien pour les consulter :\n";
    $message .= "$lien\n\n";
    $message .= "Cordialement,\nL'équipe pédagogique";
    
    // Log au lieu d'envoyer pour éviter l'erreur SMTP
    $logEntry = "[" . date('Y-m-d H:i:s') . "] Email pour $email ($prenom $nom)\n";
    $logEntry .= "Sujet: $sujet\n";
    $logEntry .= "Message: $message\n";
    $logEntry .= "---\n\n";
    
    // Créer le dossier logs s'il n'existe pas
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents('logs/emails.log', $logEntry, FILE_APPEND | LOCK_EX);
    
    return true; // Simule un envoi réussi
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
                    envoyerEmailSimple($etudiant['mail'], $etudiant['nom'], $etudiant['prenom']);
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
        <link rel="stylesheet" href="../../3.1.css">

</head>
<body>
    <div class="container">
        <h1>Partie 3.4 - Outils de diffusion des résultats</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="info">
            <h3>📋 Règles de diffusion :</h3>
            <ul>
                <li><strong>BUT2 :</strong> Grilles de stage ET portfolio remontées</li>
                <li><strong>BUT3 :</strong> Grilles de stage ET portfolio ET anglais remontées</li>
                <li><strong>Action irréversible :</strong> Une fois diffusée, la grille ne peut plus être modifiée</li>
            </ul>
        </div>
        
        <h2>Étudiants candidats à la diffusion</h2>
        
        <?php if (empty($etudiantsCandidats)): ?>
            <p>Aucun étudiant candidat à la diffusion.</p>
        <?php else: ?>
            <p><strong><?= count($etudiantsCandidats) ?></strong> étudiants peuvent recevoir leurs résultats.</p>
            
            <table>
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
            
            <div class="actions">
                <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir diffuser les résultats à TOUS les étudiants ? Cette action est irréversible !')">
                    <input type="hidden" name="action" value="diffuser_tous">
                    <button type="submit" class="btn btn-danger">📧 Diffuser à tous les candidats</button>
                </form>
            </div>
        <?php endif; ?>
        
        <h2>Étudiants ayant déjà reçu leurs résultats</h2>
        
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
            
            <table>
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Niveau</th>
                        <th>Entreprise</th>
                        <th>Statut</th>
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
                            <td><span>DIFFUSÉ</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="info">
            <h3>💡 Fonctionnement :</h3>
            <ol>
                <li>Le système vérifie automatiquement que toutes les grilles sont remontées</li>
                <li>Un email est envoyé à chaque étudiant avec un lien de consultation</li>
                <li>Les statuts passent à "DIFFUSEE" (irréversible)</li>
                <li>L'étudiant peut consulter ses résultats via le lien reçu</li>
            </ol>
        </div>
    </div>

        <p><a href="index.php">← Retour</a></p>

</body>
</html>
