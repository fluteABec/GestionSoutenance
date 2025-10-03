<?php
include("config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $idEtudiant = intval($_POST["idEtudiant"]);
    $idEval = intval($_POST["id"]);
    $commentaire = isset($_POST["commentaireJury"]) ? $_POST["commentaireJury"] : "";
    $action = $_POST["action"];

    // Mapping pour éviter les if à rallonge
    $mainTables = [
        "portfolio"   => ["table" => "evalportfolio",  "col" => "IdEvalPortfolio"],
        "rapport"     => ["table" => "evalrapport",    "col" => "IdEvalRapport"],
        "soutenance"  => ["table" => "evalsoutenance", "col" => "IdEvalSoutenance"],
        "stage"       => ["table" => "evalstage",      "col" => "IdEvalStage"],
        "anglais"     => ["table" => "evalanglais",    "col" => "IdEvalAnglais"]
    ];

    $pivotTables = [
        "portfolio"   => ["table" => "lescriteresnotesportfolio", "col" => "IdEvalPortfolio", 'notecol' => 'noteCritere'],
        "rapport"     => ["table" => "lescriteresnotesrapport",   "col" => "IdEvalRapport", 'notecol' => 'noteCritere'],
        "soutenance"  => ["table" => "lescriteresnotessoutenance","col" => "IdEvalSoutenance", 'notecol' => 'noteCritere'],
        "stage"       => ["table" => "lescriteresnotesstage",     "col" => "IdEvalStage", 'notecol' => 'noteCritere'],
        "anglais"     => ["table" => "lescriteresnotesanglais",   "col" => "IdEvalAnglais", 'notecol' => 'noteCritere']
    ];

    if (!isset($mainTables[$type]) || !isset($pivotTables[$type])) {
        die("❌ Type de grille inconnu !");
    }

    $tableMain = $mainTables[$type]['table'];
    $colEval   = $mainTables[$type]['col'];

    $tablePivot = $pivotTables[$type]['table'];
    $colPivot   = $pivotTables[$type]['col'];

    // Déterminer le statut selon l'action
    $statut = "SAISIE";
    if ($action === "valider") $statut = "BLOQUEE";
    if ($action === "debloquer") $statut = "SAISIE";

    // Enregistrer les notes critère par critère
    if (isset($_POST['notes'])) {
        $noteCol = $pivotTables[$type]['notecol'];
        foreach ($_POST['notes'] as $idCrit => $noteCrit) {
            $noteCrit = trim($noteCrit);
            if ($noteCrit === '') continue;
            $valFloat = (float)$noteCrit;
            // Delete existing then insert (portable upsert)
            $del = $mysqli->prepare("DELETE FROM {$pivotTables[$type]['table']} WHERE {$pivotTables[$type]['col']}=? AND IdCritere=?");
            $del->bind_param('ii', $idEval, $idCrit);
            $del->execute();
            $ins = $mysqli->prepare("INSERT INTO {$pivotTables[$type]['table']} ({$pivotTables[$type]['col']}, IdCritere, $noteCol) VALUES (?, ?, ?)");
            $ins->bind_param('iid', $idEval, $idCrit, $valFloat);
            $ins->execute();
        }
    }

    // Recalculer la note totale
    $noteColForSum = $pivotTables[$type]['notecol'];
    $sql = "SELECT SUM(CAST($noteColForSum AS DECIMAL(10,2))) as total FROM {$pivotTables[$type]['table']} WHERE {$pivotTables[$type]['col']}=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idEval);
    $stmt->execute();
    $totalRow = $stmt->get_result()->fetch_assoc();
    $total = isset($totalRow['total']) ? (float)$totalRow['total'] : 0.0;

    // Règle spéciale pour le stage : n'autoriser la mise en BLOQUEE que si toutes
    // les autres évaluations de l'étudiant sont déjà BLOQUEE.
    if ($type === "stage" && $action === "valider") {
        $checks = [
            'evalportfolio' => 'portfolio',
            'evalrapport'   => 'rapport',
            'evalsoutenance'=> 'soutenance',
            'evalanglais'   => 'anglais'
        ];
        $notBlocked = [];
        foreach ($checks as $table => $label) {
            // Count any rows for this student that are not BLOQUEE
            $q = "SELECT COUNT(*) AS c FROM $table WHERE IdEtudiant=? AND Statut <> 'BLOQUEE'";
            $stm = $mysqli->prepare($q);
            if (!$stm) continue; // table might not exist or other issue
            $stm->bind_param('i', $idEtudiant);
            $stm->execute();
            $r = $stm->get_result()->fetch_assoc();
            $cnt = isset($r['c']) ? (int)$r['c'] : 0;
            if ($cnt > 0) $notBlocked[] = $label;
        }

        if (!empty($notBlocked)) {
            // Log which evaluations prevented blocking
            if (!file_exists('logs')) mkdir('logs', 0755, true);
            file_put_contents('logs/actions.log', date('c') . " - Cannot block stage for IdEtudiant=$idEtudiant; not blocked: " . implode(',', $notBlocked) . "\n", FILE_APPEND | LOCK_EX);
            // Redirect back with an error indicating missing blocked evals
            $details = urlencode(implode(',', $notBlocked));
            header("Location: index.php?nature=$type&error=not_all_blocked&details=$details");
            exit();
        }

        // All other evaluations are already BLOQUEE -> allow stage to be blocked
        $statut = "BLOQUEE";
    }

    // Mise à jour de la table principale
    $sql = "UPDATE $tableMain SET note=?, commentaireJury=?, Statut=? WHERE $colEval=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("dssi", $total, $commentaire, $statut, $idEval);

    // Wrap update in transaction for consistency
    $mysqli->begin_transaction();
    $ok = $stmt->execute();
    if ($ok) {
        $mysqli->commit();
    } else {
        $mysqli->rollback();
    }
    // Rediriger vers la page appropriée selon le type
    if ($type === 'anglais') {
        // rediriger vers Page A (Front_PartieA public index)
        $target = '../Front_PartieA/public/index.php';
        header("Location: $target");
    } else {
        // comportement historique : revenir à la page B
        $target = '../PAGEB/index.php?&etudiant_id=' . $idEtudiant;
        header("Location: $target");
    }
    exit();
}
