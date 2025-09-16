<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'evaluationstages';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

// Configuration des emails
define('EMAIL_FROM', 'noreply@iut.fr');
define('EMAIL_FROM_NAME', 'IUT - Gestion des soutenances');

// Configuration de l'application
define('APP_URL', 'http://localhost/envoie%20de%20mail');

// Fonction pour nettoyer les données d'entrée
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fonction pour formater une date
function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

// Fonction pour calculer la note finale d'une grille
function calculerNoteFinale($notes, $notesMax, $noteMaxGrille) {
    if (empty($notes) || empty($notesMax) || $noteMaxGrille <= 0) {
        return 0;
    }
    
    $sommeNotes = array_sum($notes);
    $sommeNotesMax = array_sum($notesMax);
    
    if ($sommeNotesMax == 0) return 0;
    
    return ($sommeNotes / $sommeNotesMax) * $noteMaxGrille;
}

// Fonction pour vérifier les droits d'accès
function checkAccess($requiredType) {
    if (!isset($_SESSION['user_type'])) {
        header('Location: login.php');
        exit;
    }
    
    if ($_SESSION['user_type'] !== $requiredType) {
        die('Accès non autorisé');
    }
}

// Fonction pour logger les actions
function logAction($action, $details = '') {
    $logFile = 'logs/actions.log';
    $timestamp = date('Y-m-d H:i:s');
    $user = $_SESSION['user_name'] ?? 'Anonyme';
    $logEntry = "[$timestamp] $user - $action";
    if ($details) {
        $logEntry .= " - $details";
    }
    $logEntry .= PHP_EOL;
    
    // Créer le dossier logs s'il n'existe pas
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
