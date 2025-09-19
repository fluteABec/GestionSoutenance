<?php
require_once __DIR__ . "/../../config/database.php";  
require_once __DIR__ . "/../models/Soutenance.php";

class SoutenanceController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function afficherPageA($enseignantId) {
        // Récupère le nom/prénom de l'enseignant connecté
        $stmt = $this->pdo->prepare("SELECT IdEnseignant, nom, prenom, mail FROM Enseignants WHERE IdEnseignant = ?");
        $stmt->execute([$enseignantId]);
        $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$enseignant) {
            // Rediriger si l'enseignant n'existe pas
            header('Location: ../../../../../index.html?error=2');
            exit();
        }
        
        // Formater le nom complet
        $enseignantFullName = $enseignant['prenom'] . ' ' . $enseignant['nom'];

        // Récupère les soutenances via le modèle
        $model = new Soutenance($this->pdo);
        $aVenir = $model->getSoutenancesAVenir($enseignantId);
        $passees = $model->getSoutenancesPassees($enseignantId);

        // Passe les variables à la vue
        extract([
            'enseignantFullName' => $enseignantFullName,
            'aVenir' => $aVenir,
            'passees' => $passees,
            'enseignant' => $enseignant
        ]);
        
        // Inclure la vue
        include __DIR__ . '/../views/pageA.php';
    }
}
