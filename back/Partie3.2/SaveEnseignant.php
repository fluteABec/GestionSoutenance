<?php
// Partie3.2/SaveEnseignant.php
require_once "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../mainAdministration.php?err=invalid");
        exit;
    }

    $idEtudiant = $_POST['idEtudiant'] ?? null;
    $type       = $_GET['type'] ?? null; // "stage" ou "anglais"

    if (!$idEtudiant || !$type) {
        header("Location: ../mainAdministration.php?err=invalid");
        exit;
    }

    // Normaliser les champs reçus
    $tuteur = isset($_POST['tuteur']) && $_POST['tuteur'] !== '' ? (int)$_POST['tuteur'] : null;
    $second = isset($_POST['second']) && $_POST['second'] !== '' ? (int)$_POST['second'] : null;

    if ($type === "stage") {
        // --- Soutenance Stage ---
        $stmt = $pdo->prepare("SELECT IdEvalStage, IdEnseignantTuteur, IdSecondEnseignant 
                               FROM EvalStage WHERE IdEtudiant = :id LIMIT 1");
        $stmt->execute(['id' => $idEtudiant]);
        $stage = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stage) {
            header("Location: ../mainAdministration.php?err=nostage");
            exit;
        }

        // Vérifier doublon
        $finalTuteur = $tuteur !== null ? $tuteur : $stage['IdEnseignantTuteur'];
        $finalSecond = $second !== null ? $second : $stage['IdSecondEnseignant'];

        if ($finalTuteur !== null && $finalSecond !== null && $finalTuteur === $finalSecond) {
            header("Location: ../mainAdministration.php?err=same");
            exit;
        }

        $updates = [];
        $params  = ['id' => $idEtudiant];
        if ($tuteur !== null) { $updates[] = "IdEnseignantTuteur = :tuteur"; $params['tuteur'] = $tuteur; }
        if ($second !== null) { $updates[] = "IdSecondEnseignant = :second"; $params['second'] = $second; }

        if (!empty($updates)) {
            $sql = "UPDATE EvalStage SET " . implode(", ", $updates) . " WHERE IdEtudiant = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

    } elseif ($type === "anglais") {
        // --- Soutenance Anglais ---
        $stmt = $pdo->prepare("SELECT IdEvalAnglais, IdEnseignant 
                               FROM EvalAnglais WHERE IdEtudiant = :id LIMIT 1");
        $stmt->execute(['id' => $idEtudiant]);
        $anglais = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$anglais) {
            header("Location: ../mainAdministration.php?err=noanglais");
            exit;
        }

        if ($tuteur !== null) {
            $stmt = $pdo->prepare("UPDATE EvalAnglais SET IdEnseignant = :tuteur WHERE IdEtudiant = :id");
            $stmt->execute(['id' => $idEtudiant, 'tuteur' => $tuteur]);
        }
    }

    header("Location: ../mainAdministration.php?success=1");
    exit;

} catch (\PDOException $e) {
    // echo "Erreur DB : " . $e->getMessage();
    header("Location: ../mainAdministration.php?err=dberror");
    exit;
}
