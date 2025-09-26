<?php

require_once "../../db.php";

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int) $_GET['id'];
    $type = $_GET['type'];

    if ($type === 'stage') {
        $sql = "DELETE FROM EvalStage WHERE IdEvalStage = :id";
    } elseif ($type === 'anglais') {
        $sql = "DELETE FROM EvalAnglais WHERE IdEvalAnglais = :id";
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
