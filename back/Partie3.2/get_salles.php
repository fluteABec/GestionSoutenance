<?php

require_once "/opt/lampp/htdocs/projet_sql/db.php";

$date = $_GET['date'] ?? null;

if (!$date) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT s.IdSalle, s.description
    FROM Salles s
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
