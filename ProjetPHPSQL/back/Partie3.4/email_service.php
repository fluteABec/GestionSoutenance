<?php
require_once 'config.php';

class EmailService {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Envoie un email avec les résultats à un étudiant
     */
    public function envoyerResultats($etudiantId) {
        try {
            // Récupérer les informations de l'étudiant
            $etudiant = $this->getEtudiantInfo($etudiantId);
            if (!$etudiant) {
                throw new Exception("Étudiant non trouvé");
            }
            
            // Générer le lien de consultation
            $lienConsultation = $this->genererLienConsultation($etudiantId);
            
            // Préparer le contenu de l'email
            $sujet = $this->genererSujet($etudiant);
            $message = $this->genererMessage($etudiant, $lienConsultation);
            
            // Envoyer l'email
            $resultat = $this->envoyerEmail($etudiant['mail'], $sujet, $message);
            
            if ($resultat) {
                logAction("Email de résultats envoyé", "Étudiant: {$etudiant['prenom']} {$etudiant['nom']}");
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            logAction("Erreur envoi email", "Étudiant ID: $etudiantId - Erreur: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Envoie un email de rappel pour la validation des grilles
     */
    public function envoyerRappelValidation($etudiantId, $enseignantId) {
        try {
            $etudiant = $this->getEtudiantInfo($etudiantId);
            $enseignant = $this->getEnseignantInfo($enseignantId);
            
            if (!$etudiant || !$enseignant) {
                throw new Exception("Données manquantes");
            }
            
            $sujet = "Rappel - Validation des grilles d'évaluation";
            $message = $this->genererMessageRappel($etudiant, $enseignant);
            
            $resultat = $this->envoyerEmail($enseignant['mail'], $sujet, $message);
            
            if ($resultat) {
                logAction("Email de rappel envoyé", "Enseignant: {$enseignant['prenom']} {$enseignant['nom']} - Étudiant: {$etudiant['prenom']} {$etudiant['nom']}");
            }
            
            return $resultat;
            
        } catch (Exception $e) {
            logAction("Erreur envoi rappel", "Étudiant ID: $etudiantId - Enseignant ID: $enseignantId - Erreur: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Envoie un email de notification de changement de statut
     */
    public function envoyerNotificationStatut($etudiantId, $ancienStatut, $nouveauStatut) {
        try {
            $etudiant = $this->getEtudiantInfo($etudiantId);
            if (!$etudiant) {
                throw new Exception("Étudiant non trouvé");
            }
            
            $sujet = "Notification - Changement de statut d'évaluation";
            $message = $this->genererMessageNotification($etudiant, $ancienStatut, $nouveauStatut);
            
            // Envoyer à l'étudiant
            $this->envoyerEmail($etudiant['mail'], $sujet, $message);
            
            // Envoyer aussi aux enseignants concernés
            $enseignants = $this->getEnseignantsConcernes($etudiantId);
            foreach ($enseignants as $enseignant) {
                $this->envoyerEmail($enseignant['mail'], $sujet, $message);
            }
            
            logAction("Notification de statut envoyée", "Étudiant: {$etudiant['prenom']} {$etudiant['nom']} - $ancienStatut -> $nouveauStatut");
            
        } catch (Exception $e) {
            logAction("Erreur notification statut", "Étudiant ID: $etudiantId - Erreur: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Récupère les informations d'un étudiant
     */
    private function getEtudiantInfo($etudiantId) {
        $stmt = $this->pdo->prepare("
            SELECT e.IdEtudiant, e.nom, e.prenom, e.mail,
                   an.but3sinon2, an.alternanceBUT3,
                   ent.nom as nomEntreprise
            FROM EtudiantsBUT2ou3 e
            LEFT JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
            LEFT JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
            WHERE e.IdEtudiant = ?
        ");
        $stmt->execute([$etudiantId]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère les informations d'un enseignant
     */
    private function getEnseignantInfo($enseignantId) {
        $stmt = $this->pdo->prepare("
            SELECT IdEnseignant, nom, prenom, mail
            FROM Enseignants
            WHERE IdEnseignant = ?
        ");
        $stmt->execute([$enseignantId]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère les enseignants concernés par un étudiant
     */
    private function getEnseignantsConcernes($etudiantId) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT e.IdEnseignant, e.nom, e.prenom, e.mail
            FROM Enseignants e
            INNER JOIN EvalStage es ON e.IdEnseignant = es.IdEnseignantTuteur OR e.IdEnseignant = es.IdSecondEnseignant
            WHERE es.IdEtudiant = ? AND es.anneeDebut = YEAR(CURDATE())
            
            UNION
            
            SELECT DISTINCT e.IdEnseignant, e.nom, e.prenom, e.mail
            FROM Enseignants e
            INNER JOIN EvalAnglais ea ON e.IdEnseignant = ea.IdEnseignant
            WHERE ea.IdEtudiant = ? AND ea.anneeDebut = YEAR(CURDATE())
        ");
        $stmt->execute([$etudiantId, $etudiantId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Génère le lien de consultation
     */
    private function genererLienConsultation($etudiantId) {
        return APP_URL . "/consultation_resultats.php?id=" . $etudiantId;
    }
    
    /**
     * Génère le sujet de l'email
     */
    private function genererSujet($etudiant) {
        $annee = date('Y');
        return "Vos résultats d'évaluation - Année $annee";
    }
    
    /**
     * Génère le message de l'email avec les résultats
     */
    private function genererMessage($etudiant, $lienConsultation) {
        $niveau = $etudiant['but3sinon2'] ? 'BUT3' : 'BUT2';
        if ($etudiant['but3sinon2'] && $etudiant['alternanceBUT3']) {
            $niveau .= ' (Alternance)';
        }
        
        $message = "Bonjour " . $etudiant['prenom'] . " " . $etudiant['nom'] . ",\n\n";
        $message .= "Vos résultats d'évaluation pour l'année " . date('Y') . " sont maintenant disponibles.\n\n";
        $message .= "Niveau : $niveau\n";
        if ($etudiant['nomEntreprise']) {
            $message .= "Entreprise : " . $etudiant['nomEntreprise'] . "\n";
        }
        $message .= "\n";
        $message .= "Vous pouvez consulter vos résultats en cliquant sur le lien suivant :\n";
        $message .= $lienConsultation . "\n\n";
        $message .= "IMPORTANT :\n";
        $message .= "- Ce lien est personnel et confidentiel\n";
        $message .= "- Ne le partagez pas avec d'autres personnes\n\n";
        $message .= "Si vous avez des questions concernant vos résultats, n'hésitez pas à contacter votre secrétariat.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe pédagogique\n";
        $message .= "IUT - Département Informatique";
        
        return $message;
    }
    
    /**
     * Génère le message de rappel
     */
    private function genererMessageRappel($etudiant, $enseignant) {
        $message = "Bonjour " . $enseignant['prenom'] . " " . $enseignant['nom'] . ",\n\n";
        $message .= "Ce message est un rappel concernant la validation des grilles d'évaluation.\n\n";
        $message .= "Étudiant concerné : " . $etudiant['prenom'] . " " . $etudiant['nom'] . "\n";
        $message .= "Niveau : " . ($etudiant['but3sinon2'] ? 'BUT3' : 'BUT2') . "\n";
        if ($etudiant['nomEntreprise']) {
            $message .= "Entreprise : " . $etudiant['nomEntreprise'] . "\n";
        }
        $message .= "\n";
        $message .= "Il reste des grilles d'évaluation à valider pour cet étudiant.\n";
        $message .= "Veuillez vous connecter à l'application pour finaliser les évaluations.\n\n";
        $message .= "Merci de votre attention.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe pédagogique";
        
        return $message;
    }
    
    /**
     * Génère le message de notification de changement de statut
     */
    private function genererMessageNotification($etudiant, $ancienStatut, $nouveauStatut) {
        $message = "Bonjour,\n\n";
        $message .= "Le statut de l'évaluation de " . $etudiant['prenom'] . " " . $etudiant['nom'] . " a été modifié.\n\n";
        $message .= "Ancien statut : " . $ancienStatut . "\n";
        $message .= "Nouveau statut : " . $nouveauStatut . "\n";
        $message .= "Date de modification : " . date('d/m/Y H:i') . "\n\n";
        $message .= "Cordialement,\n";
        $message .= "Système de gestion des soutenances";
        
        return $message;
    }
    
    /**
     * Envoie un email
     */
    private function envoyerEmail($destinataire, $sujet, $message) {
        $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        return mail($destinataire, $sujet, $message, $headers);
    }
    
    /**
     * Enregistre la date d'envoi de l'email (fonction supprimée car pas de colonne dateConsultation)
     */
    private function enregistrerEnvoi($etudiantId) {
        // Fonction supprimée car la colonne dateConsultation n'existe pas dans la structure existante
        return true;
    }
    
    /**
     * Envoie des emails en lot
     */
    public function envoyerEnLot($etudiantsIds) {
        $resultats = [];
        
        foreach ($etudiantsIds as $etudiantId) {
            try {
                $resultats[$etudiantId] = $this->envoyerResultats($etudiantId);
                
            } catch (Exception $e) {
                $resultats[$etudiantId] = false;
                logAction("Erreur envoi en lot", "Étudiant ID: $etudiantId - Erreur: " . $e->getMessage());
            }
        }
        
        return $resultats;
    }
    
    /**
     * Teste la configuration email
     */
    public function testerConfiguration() {
        $testEmail = "test@example.com";
        $sujet = "Test de configuration email";
        $message = "Ceci est un email de test pour vérifier la configuration.";
        
        return $this->envoyerEmail($testEmail, $sujet, $message);
    }
}
?>
