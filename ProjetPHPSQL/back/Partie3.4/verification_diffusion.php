<?php
require_once 'config.php';

class VerificationDiffusion {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Vérifie si un étudiant peut recevoir ses résultats
     */
    public function peutDiffuser($etudiantId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT an.but3sinon2,
                       (SELECT COUNT(*) FROM EvalStage WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as stage_remonte,
                       (SELECT COUNT(*) FROM EvalPortFolio WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as portfolio_remonte,
                       (SELECT COUNT(*) FROM EvalAnglais WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as anglais_remonte
                FROM AnneeStage an 
                WHERE an.IdEtudiant = ? AND an.anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute([$etudiantId, $etudiantId, $etudiantId, $etudiantId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'eligible' => false,
                    'raison' => 'Aucun stage trouvé pour cet étudiant'
                ];
            }
            
            // Pour BUT2 : stage + portfolio
            if (!$result['but3sinon2']) {
                if ($result['stage_remonte'] == 0) {
                    return [
                        'eligible' => false,
                        'raison' => 'Grille de stage non remontée'
                    ];
                }
                if ($result['portfolio_remonte'] == 0) {
                    return [
                        'eligible' => false,
                        'raison' => 'Grille de portfolio non remontée'
                    ];
                }
                return [
                    'eligible' => true,
                    'raison' => 'Toutes les grilles requises sont remontées'
                ];
            }
            
            // Pour BUT3 : stage + portfolio + anglais
            if ($result['stage_remonte'] == 0) {
                return [
                    'eligible' => false,
                    'raison' => 'Grille de stage non remontée'
                ];
            }
            if ($result['portfolio_remonte'] == 0) {
                return [
                    'eligible' => false,
                    'raison' => 'Grille de portfolio non remontée'
                ];
            }
            if ($result['anglais_remonte'] == 0) {
                return [
                    'eligible' => false,
                    'raison' => 'Grille d\'anglais non remontée'
                ];
            }
            
            return [
                'eligible' => true,
                'raison' => 'Toutes les grilles requises sont remontées'
            ];
            
        } catch (PDOException $e) {
            return [
                'eligible' => false,
                'raison' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifie si un étudiant BUT2 peut recevoir ses résultats
     */
    public function peutDiffuserBUT2($etudiantId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM EvalStage WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as stage_remonte,
                    (SELECT COUNT(*) FROM EvalPortFolio WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as portfolio_remonte
            ");
            $stmt->execute([$etudiantId, $etudiantId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['stage_remonte'] > 0 && $result['portfolio_remonte'] > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Vérifie si un étudiant BUT3 peut recevoir ses résultats
     */
    public function peutDiffuserBUT3($etudiantId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM EvalStage WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as stage_remonte,
                    (SELECT COUNT(*) FROM EvalPortFolio WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as portfolio_remonte,
                    (SELECT COUNT(*) FROM EvalAnglais WHERE IdEtudiant = ? AND Statut = 'REMONTEE' AND anneeDebut = YEAR(CURDATE())) as anglais_remonte
            ");
            $stmt->execute([$etudiantId, $etudiantId, $etudiantId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['stage_remonte'] > 0 && $result['portfolio_remonte'] > 0 && $result['anglais_remonte'] > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Récupère la liste des étudiants éligibles pour la diffusion
     */
    public function getEtudiantsEligibles() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.IdEtudiant, e.nom, e.prenom, e.mail,
                       an.but3sinon2, an.alternanceBUT3,
                       ent.nom as nomEntreprise,
                       es.note as noteStage, es.Statut as statutStage,
                       ep.note as notePortfolio, ep.Statut as statutPortfolio,
                       ea.note as noteAnglais, ea.Statut as statutAnglais
                FROM EtudiantsBUT2ou3 e
                INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
                LEFT JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
                LEFT JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalPortFolio ep ON e.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalAnglais ea ON e.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
                WHERE es.Statut = 'REMONTEE' AND ep.Statut = 'REMONTEE'
                AND (an.but3sinon2 = 0 OR ea.Statut = 'REMONTEE')
                ORDER BY e.nom, e.prenom
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Récupère la liste des étudiants en retard
     */
    public function getEtudiantsEnRetard() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.IdEtudiant, e.nom, e.prenom, e.mail,
                       an.but3sinon2, an.alternanceBUT3,
                       ent.nom as nomEntreprise,
                       es.date_h as dateSoutenance,
                       es.Statut as statutStage,
                       ep.Statut as statutPortfolio,
                       ea.Statut as statutAnglais,
                       DATEDIFF(NOW(), es.date_h) as joursDepuisSoutenance
                FROM EtudiantsBUT2ou3 e
                INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
                LEFT JOIN Entreprises ent ON an.IdEntreprise = ent.IdEntreprise
                LEFT JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalPortFolio ep ON e.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalAnglais ea ON e.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
                WHERE es.date_h < NOW() 
                AND (es.Statut = 'SAISIE' OR es.Statut = 'VALIDEE' OR es.Statut = 'BLOQUEE')
                AND (ep.Statut = 'SAISIE' OR ep.Statut = 'VALIDEE' OR ep.Statut = 'BLOQUEE')
                AND (an.but3sinon2 = 0 OR ea.Statut = 'SAISIE' OR ea.Statut = 'VALIDEE' OR ea.Statut = 'BLOQUEE')
                ORDER BY es.date_h ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Vérifie l'intégrité des données avant diffusion
     */
    public function verifierIntegrite($etudiantId) {
        $erreurs = [];
        
        try {
            // Vérifier que l'étudiant existe
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
            $stmt->execute([$etudiantId]);
            if ($stmt->fetchColumn() == 0) {
                $erreurs[] = "Étudiant non trouvé";
                return $erreurs;
            }
            
            // Vérifier que l'étudiant a un stage pour l'année en cours
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM AnneeStage 
                WHERE IdEtudiant = ? AND anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute([$etudiantId]);
            if ($stmt->fetchColumn() == 0) {
                $erreurs[] = "Aucun stage trouvé pour l'année en cours";
            }
            
            // Vérifier les notes
            $stmt = $this->pdo->prepare("
                SELECT es.note as noteStage, ep.note as notePortfolio, ea.note as noteAnglais,
                       an.but3sinon2
                FROM AnneeStage an
                LEFT JOIN EvalStage es ON an.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalPortFolio ep ON an.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
                LEFT JOIN EvalAnglais ea ON an.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
                WHERE an.IdEtudiant = ? AND an.anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute([$etudiantId]);
            $notes = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$notes) {
                $erreurs[] = "Aucune donnée d'évaluation trouvée";
                return $erreurs;
            }
            
            // Vérifier la note de stage
            if ($notes['noteStage'] === null || $notes['noteStage'] < 0 || $notes['noteStage'] > 20) {
                $erreurs[] = "Note de stage invalide";
            }
            
            // Vérifier la note de portfolio
            if ($notes['notePortfolio'] === null || $notes['notePortfolio'] < 0 || $notes['notePortfolio'] > 20) {
                $erreurs[] = "Note de portfolio invalide";
            }
            
            // Vérifier la note d'anglais pour les BUT3
            if ($notes['but3sinon2'] && ($notes['noteAnglais'] === null || $notes['noteAnglais'] < 0 || $notes['noteAnglais'] > 20)) {
                $erreurs[] = "Note d'anglais invalide pour un étudiant BUT3";
            }
            
            // Vérifier que les grilles ne sont pas déjà diffusées
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM EvalStage 
                WHERE IdEtudiant = ? AND Statut = 'DIFFUSEE' AND anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute([$etudiantId]);
            if ($stmt->fetchColumn() > 0) {
                $erreurs[] = "Les résultats ont déjà été diffusés";
            }
            
        } catch (PDOException $e) {
            $erreurs[] = "Erreur de base de données : " . $e->getMessage();
        }
        
        return $erreurs;
    }
    
    /**
     * Génère un rapport de vérification
     */
    public function genererRapportVerification() {
        $rapport = [
            'date_generation' => date('Y-m-d H:i:s'),
            'total_etudiants' => 0,
            'etudiants_eligibles' => 0,
            'etudiants_en_retard' => 0,
            'etudiants_deja_diffuses' => 0,
            'erreurs_detectees' => []
        ];
        
        try {
            // Compter le total d'étudiants
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM EtudiantsBUT2ou3 e
                INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute();
            $rapport['total_etudiants'] = $stmt->fetchColumn();
            
            // Compter les étudiants éligibles
            $etudiantsEligibles = $this->getEtudiantsEligibles();
            $rapport['etudiants_eligibles'] = count($etudiantsEligibles);
            
            // Compter les étudiants en retard
            $etudiantsEnRetard = $this->getEtudiantsEnRetard();
            $rapport['etudiants_en_retard'] = count($etudiantsEnRetard);
            
            // Compter les étudiants déjà diffusés
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM EvalStage 
                WHERE Statut = 'DIFFUSEE' AND anneeDebut = YEAR(CURDATE())
            ");
            $stmt->execute();
            $rapport['etudiants_deja_diffuses'] = $stmt->fetchColumn();
            
            // Vérifier l'intégrité pour chaque étudiant éligible
            foreach ($etudiantsEligibles as $etudiant) {
                $erreurs = $this->verifierIntegrite($etudiant['IdEtudiant']);
                if (!empty($erreurs)) {
                    $rapport['erreurs_detectees'][] = [
                        'etudiant' => $etudiant['prenom'] . ' ' . $etudiant['nom'],
                        'erreurs' => $erreurs
                    ];
                }
            }
            
        } catch (PDOException $e) {
            $rapport['erreurs_detectees'][] = [
                'etudiant' => 'SYSTÈME',
                'erreurs' => ['Erreur de base de données : ' . $e->getMessage()]
            ];
        }
        
        return $rapport;
    }
    
    /**
     * Valide un ID d'étudiant pour la consultation
     */
    public function validerIdEtudiant($etudiantId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.IdEtudiant, e.nom, e.prenom, e.mail
                FROM EtudiantsBUT2ou3 e
                WHERE e.IdEtudiant = ?
            ");
            $stmt->execute([$etudiantId]);
            $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$etudiant) {
                return false;
            }
            
            return $etudiant;
            
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
