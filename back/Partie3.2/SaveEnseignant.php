<?php
require_once "/opt/lampp/htdocs/projet_sql/db.php";

$idEtudiant = $_POST['idEtudiant'];
$tuteur = $_POST['tuteur'] ?? null;
$second = $_POST['second'] ?? null;

// Vérif tuteur ≠ second
if ($tuteur && $second && $tuteur == $second) {
    die("⚠️ Le tuteur et le second doivent être différents.");
}

// Vérifier si une ligne existe déjà
$stmt = $pdo->prepare("SELECT IdEvalStage FROM EvalStage WHERE IdEtudiant = ?");
$stmt->execute([$idEtudiant]);
$exist = $stmt->fetch();

if ($exist) {
    // Update uniquement les champs envoyés
    if ($tuteur !== null) {
        $sql = "UPDATE EvalStage SET IdEnseignantTuteur = :tuteur WHERE IdEtudiant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idEtudiant, 'tuteur' => $tuteur]);
    }
    if ($second !== null) {
        $sql = "UPDATE EvalStage SET IdSecondEnseignant = :second WHERE IdEtudiant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idEtudiant, 'second' => $second]);
    }
} else {
    // Insertion minimale
    $sql = "INSERT INTO EvalStage (IdEtudiant, IdEnseignantTuteur, IdSecondEnseignant, date_h, IdSalle, anneeDebut, IdModeleEval, Statut) 
            VALUES (:id, :tuteur, :second, NULL, NULL, YEAR(CURDATE()), 1, 'non planifié')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $idEtudiant,
        'tuteur' => $tuteur,
        'second' => $second
    ]);
}

// Retour
header("Location: ../mainAdministration.php?success=1");
exit;
