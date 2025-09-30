<?php
require_once 'config.php';

// Fonction utilitaire pour envoyer une réponse d'erreur et arrêter
function bad_request($msg = 'Accès non autorisé') {
    http_response_code(403);
    echo '<h1>Accès refusé</h1>';
    echo '<p>' . htmlspecialchars($msg) . '</p>';
    exit;
}

if (!isset($_GET['token'])) {
    bad_request('Token manquant');
}

$token = $_GET['token'];

// Décomposer token
$parts = explode('.', $token);
if (count($parts) !== 2) {
    bad_request('Token invalide');
}

$payloadB64 = $parts[0];
$signature = $parts[1];

$payloadJson = base64_decode($payloadB64, true);
if ($payloadJson === false) {
    bad_request('Payload invalide');
}

// Vérifier la signature
$expectedSig = hash_hmac('sha256', $payloadJson, APP_SECRET);
if (!hash_equals($expectedSig, $signature)) {
    bad_request('Signature invalide');
}

$payload = json_decode($payloadJson, true);
if (!$payload || !isset($payload['id']) || !isset($payload['exp'])) {
    bad_request('Payload manquant');
}

// Vérifier l'expiration
if (time() > (int)$payload['exp']) {
    bad_request('Le lien a expiré');
}

$etudiantId = (int)$payload['id'];

// À ce stade le token est valide. Charger les résultats et afficher.
// Ici on effectue une connexion basique à la BDD via la config (qui a $pdo)
try {
    // Récupérer quelques infos étudiante pour l'affichage
    $stmt = $pdo->prepare("SELECT IdEtudiant, nom, prenom FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
    $stmt->execute([$etudiantId]);
    $etudiant = $stmt->fetch();

    if (!$etudiant) {
        bad_request('Étudiant introuvable');
    }

    echo '<h1>Résultats pour ' . htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) . '</h1>';
    echo '<p>Ici s\'afficheraient les résultats détaillés (hors périmètre de cet exemple).</p>';

} catch (Exception $e) {
    bad_request('Erreur interne');
}

?>