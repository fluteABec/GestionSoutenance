<?php
require 'db.php';

// Fonction pour vérifier si un mot de passe est déjà haché
function isHashed($password) {
    return (strlen($password) > 32) && (preg_match('/^\$2[ayb]\$.{56}$/', $password) === 1);
}

// Hacher les mots de passe des enseignants
$sql = "SELECT IdEnseignant, mdp FROM Enseignants";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$enseignants = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($enseignants as $enseignant) {
    if (!isHashed($enseignant['mdp'])) {
        $hashedPassword = password_hash($enseignant['mdp'], PASSWORD_DEFAULT);
        $updateSql = "UPDATE Enseignants SET mdp = :hashedPassword WHERE IdEnseignant = :IdEnseignant";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':hashedPassword', $hashedPassword);
        $updateStmt->bindParam(':IdEnseignant', $enseignant['IdEnseignant']);
        $updateStmt->execute();
        echo "Mot de passe de l'enseignant ID " . $enseignant['IdEnseignant'] . " haché.<br>";
    } else {
        echo "Mot de passe de l'enseignant ID " . $enseignant['IdEnseignant'] . " déjà haché.<br>";
    }
}

// Hacher les mots de passe des utilisateurs du back office
$sql = "SELECT identifiant, mdp FROM UtilisateursBackOffice";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($utilisateurs as $utilisateur) {
    if (!isHashed($utilisateur['mdp'])) {
        $hashedPassword = password_hash($utilisateur['mdp'], PASSWORD_DEFAULT);
        $updateSql = "UPDATE UtilisateursBackOffice SET mdp = :hashedPassword WHERE identifiant = :identifiant";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':hashedPassword', $hashedPassword);
        $updateStmt->bindParam(':identifiant', $utilisateur['identifiant']);
        $updateStmt->execute();
        echo "Mot de passe de l'utilisateur ID " . $utilisateur['identifiant'] . " haché.<br>";
    } else {
        echo "Mot de passe de l'utilisateur ID " . $utilisateur['identifiant'] . " déjà haché.<br>";
    }
}

echo "Hachage des mots de passe terminé.";
?>
