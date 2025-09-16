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
    echo json_encode([]);
    exit;
}

$date = $_GET['date'] ?? null;

if (!$date) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT s.IdSalle, s.description
    FROM salles s
    WHERE s.IdSalle NOT IN (
        SELECT es.IdSalle
        FROM EvalStage es
        WHERE :date BETWEEN es.date_h
                        AND DATE_ADD(es.date_h, INTERVAL 1 HOUR)
        UNION
        SELECT ea.IdSalle
        FROM EvalAnglais ea
        WHERE :date BETWEEN ea.dateS
                        AND DATE_ADD(ea.dateS, INTERVAL 1 HOUR)
    )
";


$stmt = $pdo->prepare($sql);
$stmt->execute(['date' => $date]);
$salles = $stmt->fetchAll();

echo json_encode($salles ?: []);
