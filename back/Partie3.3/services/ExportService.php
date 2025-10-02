<?php
/**
 * Service pour l'export CSV
 */
class ExportService {
    private $etudiantService;
    private $mailService;

    public function __construct($etudiantService, $mailService) {
        $this->etudiantService = $etudiantService;
        $this->mailService = $mailService;
    }

    /**
     * Écrit les données dans un fichier CSV
     */
    private function ecrireDonneesCSV($liste, $nomFichier) {
        $output = fopen($nomFichier, "w");
        if (!empty($liste)) {
            fputcsv($output, array_keys($liste[0])); // en-têtes
            foreach ($liste as $ligne) {
                fputcsv($output, $ligne);
            }
        }
        fclose($output);
    }

    /**
     * Exporte et télécharge directement un CSV
     */
    public function exporterCSV($type) {
        if ($type === 'but2') {
            $liste = $this->etudiantService->getListeEleveRemonter2A();
            $nomFichier = "export_remontee_BUT2.csv";
        } else {
            $liste = $this->etudiantService->getListeEleveRemonter3A();
            $nomFichier = "export_remontee_BUT3.csv";
        }

        // Nettoie le tampon de sortie pour éviter d'inclure du HTML dans le CSV
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$nomFichier\"");

        $output = fopen("php://output", "w");
        if (!empty($liste)) {
            fputcsv($output, array_keys($liste[0]));
            foreach ($liste as $ligne) {
                fputcsv($output, $ligne);
            }
        }
        fclose($output);
        exit;
    }

    /**
     * Envoie un CSV par mail (BUT2)
     */
    public function envoiCSVMailBUT2() {
        $liste = $this->etudiantService->getListeEleveRemonter2A();

        if (empty($liste)) {
            return ['success' => false, 'message' => 'Aucun étudiant BUT2 en REMONTEE → CSV non généré.'];
        }

        $nomFichier = sys_get_temp_dir() . "/export_remontee_BUT2.csv";
        $this->ecrireDonneesCSV($liste, $nomFichier);

        $identifiantAdmin = $_SESSION['identifiant'] ?? null;
        if ($identifiantAdmin) {
            $sujet = "Export des notes BUT2";
            $message = "<p>Bonjour,<br>Veuillez trouver ci-joint les résultats BUT2 au format CSV.</p>";
            $success = $this->mailService->envoieMail($identifiantAdmin, $sujet, $message, $nomFichier);
            
            return [
                'success' => $success,
                'message' => $success ? 'Le CSV BUT2 a été envoyé par mail.' : 'Erreur lors de l\'envoi du mail.'
            ];
        }
        
        return ['success' => false, 'message' => 'Utilisateur non connecté.'];
    }

    /**
     * Envoie un CSV par mail (BUT3)
     */
    public function envoiCSVMailBUT3() {
        $liste = $this->etudiantService->getListeEleveRemonter3A();
        
        if (empty($liste)) {
            return ['success' => false, 'message' => 'Aucun étudiant BUT3 en REMONTEE → CSV non généré.'];
        }

        $nomFichier = sys_get_temp_dir() . "/export_remontee_BUT3.csv";
        $this->ecrireDonneesCSV($liste, $nomFichier);

        $identifiantAdmin = $_SESSION['identifiant'] ?? null;
        if ($identifiantAdmin) {
            $sujet = "Export des notes BUT3";
            $message = "<p>Bonjour,<br>Veuillez trouver ci-joint les résultats BUT3 au format CSV.</p>";
            $success = $this->mailService->envoieMail($identifiantAdmin, $sujet, $message, $nomFichier);
            
            return [
                'success' => $success,
                'message' => $success ? 'Le CSV BUT3 a été envoyé par mail.' : 'Erreur lors de l\'envoi du mail.'
            ];
        }
        
        return ['success' => false, 'message' => 'Utilisateur non connecté.'];
    }
}