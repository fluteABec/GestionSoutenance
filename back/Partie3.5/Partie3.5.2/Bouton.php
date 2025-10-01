<?php
function btnAjouter($url, $label = "Ajouter") {
    return "<a href='$url' class='btn btn-success'>➕ $label</a>";
}

function btnModifier($url, $label = "Modifier") {
    return "<a href='$url' class='btn btn-warning'>✏️ $label</a>";
}

function btnSupprimer($url, $label = "Supprimer") {
    return "<a href='$url' class='btn btn-danger' onclick='return confirm(\"Supprimer ?\")'>🗑️ $label</a>";
}

function btnCopier($url, $label = "Copier") {
    return "<a href='$url' class='btn btn-warning'>➕ $label</a>";
}

function grilleDejaUtilisee($conn, $id_grille) {
    // Une seule requête avec UNION de toutes les tables d'évaluation
    $sql = "
        SELECT COUNT(*) AS nb FROM (
            SELECT IdModeleEval FROM EvalAnglais WHERE IdModeleEval = ?
            UNION ALL
            SELECT IdModeleEval FROM EvalRapport WHERE IdModeleEval = ?
            UNION ALL
            SELECT IdModeleEval FROM EvalSoutenance WHERE IdModeleEval = ?
            UNION ALL
            SELECT IdModeleEval FROM EvalStage WHERE IdModeleEval = ?
            UNION ALL
            SELECT IdModeleEval FROM EvalPortfolio WHERE IdModeleEval = ?
        ) AS union_eval
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $id_grille, $id_grille, $id_grille, $id_grille, $id_grille);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    return ($row['nb'] > 0); // true = déjà utilisée, false = jamais utilisée
}
?>