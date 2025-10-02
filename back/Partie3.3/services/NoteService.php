<?php
/**
 * Service pour la gestion des notes et des statuts
 */

class NoteService {
    private $pdo;
    private $mailService;

    public function __construct($pdo, $mailService) {
        $this->pdo = $pdo;
        $this->mailService = $mailService;
    }

    /**
     * Remonte les notes d'un étudiant et envoie un mail
     */
    public function remonterNotes($idEtudiant, $isBUT3 = false) {
        $stmt = $this->pdo->prepare("UPDATE EvalStage SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
        $stmt->execute([$idEtudiant]);
        
        $stmt = $this->pdo->prepare("UPDATE EvalPortfolio SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
        $stmt->execute([$idEtudiant]);
        
        if ($isBUT3) {
            $stmt = $this->pdo->prepare("UPDATE EvalAnglais SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
            $stmt->execute([$idEtudiant]);
        }

        $mail = $_SESSION['identifiant'] ?? null;
        if ($mail) {
            $sujet = "Vos évaluations ont été remontées";
            $message = "<p>Bonjour,<br>Les notes de l'élève avec l'ID $idEtudiant ont été <b>remontées</b> par l'administration.<br>Cordialement.</p>";
            $this->mailService->envoieMail($mail, $sujet, $message);
        }
    }

    /**
     * Bloque les notes d'un étudiant et envoie un mail
     */
    public function bloquerNotes($idEtudiant, $isBUT3 = false) {
        $stmt = $this->pdo->prepare("UPDATE EvalStage SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);
        
        $stmt = $this->pdo->prepare("UPDATE EvalPortfolio SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);
        
        if ($isBUT3) {
            $stmt = $this->pdo->prepare("UPDATE EvalAnglais SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
            $stmt->execute([$idEtudiant]);
        }

        $mail = $_SESSION['identifiant'] ?? null;
        if ($mail) {
            $sujet = "Vos évaluations ont été bloquées";
            $message = "<p>Bonjour,<br>Les notes de l'élève avec l'ID $idEtudiant ont été <b>bloquées</b> par l'administration.<br>Cordialement.</p>";
            $this->mailService->envoieMail($mail, $sujet, $message);
        }
    }

    /**
     * Autorise la saisie pour un étudiant
     */
    public function autoriserSaisie($idEtudiant, $isBUT3 = false) {
        $stmt = $this->pdo->prepare("UPDATE EvalStage SET Statut = 'SAISIE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);
        
        $stmt = $this->pdo->prepare("UPDATE EvalPortfolio SET Statut = 'SAISIE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);

        if ($isBUT3) {
            $stmt = $this->pdo->prepare("UPDATE EvalAnglais SET Statut = 'SAISIE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
            $stmt->execute([$idEtudiant]);
        }

        $stmt = $this->pdo->prepare("UPDATE EvalRapport SET Statut = 'SAISIE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);

        $stmt = $this->pdo->prepare("UPDATE EvalSoutenance SET Statut = 'SAISIE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmt->execute([$idEtudiant]);
    }

    /**
     * Remonte toutes les notes prêtes
     */
    public function remonterTout($etudiantService) {
        $etudiantsBUT2 = $etudiantService->getEtudiantsBUT2();
        foreach ($etudiantsBUT2 as $etudiant) {
            $this->remonterNotes($etudiant['IdEtudiant'], false);
        }
        
        $etudiantsBUT3 = $etudiantService->getEtudiantsBUT3();
        foreach ($etudiantsBUT3 as $etudiant) {
            $this->remonterNotes($etudiant['IdEtudiant'], true);
        }
    }
}