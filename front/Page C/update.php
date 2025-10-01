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
    if ($action === "valider") $statut = "VALIDEE";
    if ($action === "debloquer") $statut = "SAISIE";

    // Enregistrer les notes critère par critère
    if (isset($_POST['notes'])) {
        $noteCol = $pivotTables[$type]['notecol'];
        foreach ($_POST['notes'] as $idCrit => $noteCrit) {
            $noteCrit = trim($noteCrit);
            if ($noteCrit === '') continue;
            // Delete existing then insert (portable upsert)
            $del = $mysqli->prepare("DELETE FROM {$pivotTables[$type]['table']} WHERE {$pivotTables[$type]['col']}=? AND IdCritere=?");
            $del->bind_param('ii', $idEval, $idCrit);
            $del->execute();
            $ins = $mysqli->prepare("INSERT INTO {$pivotTables[$type]['table']} ({$pivotTables[$type]['col']}, IdCritere, $noteCol) VALUES (?, ?, ?)");
            $ins->bind_param('iis', $idEval, $idCrit, $noteCrit);
            $ins->execute();
        }
    }

    // Recalculer la note totale
    $noteColForSum = $pivotTables[$type]['notecol'];
    $sql = "SELECT SUM(CAST($noteColForSum AS DECIMAL(10,2))) as total FROM {$pivotTables[$type]['table']} WHERE {$pivotTables[$type]['col']}=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idEval);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    // Règle spéciale pour le stage
    if ($type === "stage" && $action === "valider") {
    $res1 = $mysqli->query("SELECT Statut FROM evalrapport WHERE IdEtudiant=$idEtudiant");
    $res2 = $mysqli->query("SELECT Statut FROM evalsoutenance WHERE IdEtudiant=$idEtudiant");

        $rapport = $res1->fetch_assoc()["Statut"] ?? "";
        $soutenance = $res2->fetch_assoc()["Statut"] ?? "";

        if ($rapport === "VALIDEE" && $soutenance === "VALIDEE") {
            $statut = "BLOQUEE";
            $mysqli->query("UPDATE evalrapport SET Statut='BLOQUEE' WHERE IdEtudiant=$idEtudiant");
            $mysqli->query("UPDATE evalsoutenance SET Statut='BLOQUEE' WHERE IdEtudiant=$idEtudiant");
        } else {
            die("❌ Impossible de valider le stage tant que rapport et soutenance ne sont pas validés !");
        }
    }

    // Mise à jour de la table principale
    $sql = "UPDATE $tableMain SET note=?, commentaireJury=?, Statut=? WHERE $colEval=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("dssi", $total, $commentaire, $statut, $idEval);

    if ($stmt->execute()) {
        echo "✅ Mise à jour réussie ($type : $statut)";
    } else {
        echo "❌ Erreur : " . $stmt->error;
    }

    // Retour
    header("Location: index.php?nature=$type");
    exit();
}
