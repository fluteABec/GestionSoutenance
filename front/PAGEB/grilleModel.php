<?php
function getInfosEtudiant($idEtudiant) {
    $_SESSION['idEtu'] = $idEtudiant;
    global $pdo;
    $sql = "SELECT e.IdEtudiant as idEtu, e.nom, e.prenom, a.sujet, ent.nom AS entreprise, a.nomMaitreStageApp As maitreStage, es.date_h, es.Statut As Statut,s.description AS salle

            FROM EtudiantsBUT2ou3 e
            JOIN AnneeStage a ON a.IdEtudiant = e.IdEtudiant
            JOIN Entreprises ent ON ent.IdEntreprise = a.IdEntreprise
            JOIN EvalStage es ON es.IdEtudiant = e.IdEtudiant
            JOIN Salles s ON s.IdSalle = es.IdSalle
            WHERE e.IdEtudiant = ? AND a.anneedebut = es.anneedebut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idEtudiant]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoleUtilisateur($idUser, $idEtudiant) {
    $_SESSION['idEtu'] = $idEtudiant;
    global $pdo;
    // Cas 1 : professeur tuteur
    $sql = "SELECT 1 FROM EvalStage WHERE IdEnseignantTuteur = ? AND IdEtudiant = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser, $idEtudiant]);
    if ($stmt->fetch()) return "TUTEUR";

    // Cas 2 : professeur secondaire
    $sql = "SELECT 1 FROM EvalStage WHERE IdSecondEnseignant = ? AND IdEtudiant = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser, $idEtudiant]);
    if ($stmt->fetch()) return "SECONDAIRE";

    // Cas 3 : secrétaire (pas lié aux tables EvalStage)
    return "SECRETAIRE";
}