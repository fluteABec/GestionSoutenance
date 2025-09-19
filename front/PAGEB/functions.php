<?php
function estModifiable($statut) {
    return !in_array($statut, ['Bloquée', 'Remontée']);
}

function statutApresValidation($typeGrille, $pdo, $idStage) {
    switch ($typeGrille) {
        case 'rapport':
        case 'soutenance':
            return 'Validée';
        case 'portfolio':
            return 'Bloquée';
        case 'stage':
            $stmt = $pdo->prepare("SELECT statut FROM grille WHERE type IN ('rapport', 'soutenance') AND idStage = ?");
            $stmt->execute([$idStage]);
            foreach ($stmt as $row) {
                if ($row['statut'] !== 'Validée') return false;
            }
            return 'Bloquée';
    }
    return null;
}
