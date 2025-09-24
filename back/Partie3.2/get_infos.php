<?php

require_once "/opt/lampp/htdocs/projet_sql/db.php";

// Récup paramètres
$idEtudiant  = $_GET['etudiant'] ?? null;
$idSalle     = $_GET['salle'] ?? null;
$idEns       = $_GET['enseignant'] ?? null;

$result = [];

// Infos étudiant + son stage
if ($idEtudiant) {
    $sql = "SELECT e.IdEtudiant, e.nom, e.prenom, e.mail, 
                   st.sujet, st.entreprise, st.nomMaitreStageApp
            FROM etudiants e
            LEFT JOIN stages st ON st.IdEtudiant = e.IdEtudiant
            WHERE e.IdEtudiant = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idEtudiant]);
    $result['etudiant'] = $stmt->fetch();
}

// Infos enseignant
if ($idEns) {
    $sql = "SELECT IdEnseignant, nom, prenom, mail
            FROM enseignants WHERE IdEnseignant = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idEns]);
    $result['enseignant'] = $stmt->fetch();
}

// Infos salle
if ($idSalle) {
    $sql = "SELECT IdSalle, description, capacite
            FROM salles WHERE IdSalle = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idSalle]);
    $result['salle'] = $stmt->fetch();
}

// Infos tuteur (lié à l’étudiant si existe)
if ($idEtudiant) {
    $sql = "SELECT ens.IdEnseignant, ens.nom, ens.prenom, ens.mail
            FROM evalstage es
            INNER JOIN Enseignants ens ON ens.IdEnseignant = es.IdEnseignantTuteur
            WHERE es.IdEtudiant = :id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idEtudiant]);
    $result['tuteur'] = $stmt->fetch();
}

header('Content-Type: application/json');
echo json_encode($result);
