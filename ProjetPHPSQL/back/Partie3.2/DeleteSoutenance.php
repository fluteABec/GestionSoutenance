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

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int) $_GET['id'];
    $type = $_GET['type'];

    if ($type === 'stage') {
        $sql = "DELETE FROM evalstage WHERE IdEvalStage = :id";
    } elseif ($type === 'anglais') {
        $sql = "DELETE FROM evalanglais WHERE IdEvalAnglais = :id";
    } else {
        die("Type de soutenance invalide !");
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    header("Location: ../mainAdministration.php?deleted=1");
    exit;
} else {
    echo "Paramètres manquants.";
}
