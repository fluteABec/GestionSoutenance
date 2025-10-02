<?php
session_start();

// Autoload des classes
require_once 'config/database.php';
require_once 'services/EtudiantService.php';
require_once 'services/NoteService.php';
require_once 'services/MailService.php';
require_once 'services/ExportService.php';
require_once 'utils/MessageManager.php';

/**
 * Contrôleur principal pour la gestion des évaluations
 */
class EvaluationController {
    private $database;
    private $pdo;
    private $etudiantService;
    private $noteService;
    private $mailService;
    private $exportService;
    private $messageManager;

    public function __construct() {
        $this->database = new Database();
        $this->pdo = $this->database->getPDO();
        
        $this->etudiantService = new EtudiantService($this->pdo);
        $this->mailService = new MailService($this->pdo);
        $this->noteService = new NoteService($this->pdo, $this->mailService);
        $this->exportService = new ExportService($this->etudiantService, $this->mailService);
        $this->messageManager = new MessageManager();
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    private function estAutoriseAdmin() {
        $listeAutorises = $this->mailService->recupererMailsAdmin();
        $mailActuel = $_SESSION['identifiant'] ?? null;
        return $mailActuel && in_array($mailActuel, $listeAutorises);
    }

    /**
     * Traite les actions GET
     */
    public function traiterActionGet() {
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $idEtudiant = (int)$_GET['id'];
            $isBUT3 = isset($_GET['but3']) && $_GET['but3'] == 1;

            switch ($_GET['action']) {
                case 'remonter':
                    if ($this->estAutoriseAdmin()) {
                        $this->noteService->remonterNotes($idEtudiant, $isBUT3);
                        $this->messageManager->addSuccess("Statuts remontés et mail envoyé pour l'étudiant ID $idEtudiant");
                    } else {
                        $this->messageManager->addError("Vous n'êtes pas autorisé à effectuer cette action.");
                    }
                    break;

                case 'bloquer':
                    $this->noteService->bloquerNotes($idEtudiant, $isBUT3);
                    $this->messageManager->addSuccess("Statuts re-bloqués et mail envoyé à l'étudiant ID $idEtudiant");
                    break;

                case 'rappel':
                    if ($this->estAutoriseAdmin()) {
                        $mailProf = $this->etudiantService->getMailEnseignantTuteur($idEtudiant);
                        $this->mailService->rappelMail($mailProf);
                        $this->messageManager->addSuccess("Mail de rappel envoyé à l'enseignant $mailProf");
                    } else {
                        $this->messageManager->addError("Vous n'êtes pas autorisé à effectuer cette action.");
                    }
                    break;

                case 'autoriser':
                    if ($this->estAutoriseAdmin()) {
                        $this->noteService->autoriserSaisie($idEtudiant, $isBUT3);
                        $this->messageManager->addSuccess("La saisie a été ré-autorisée pour l'étudiant ID $idEtudiant");
                    } else {
                        $this->messageManager->addError("Vous n'êtes pas autorisé à effectuer cette action.");
                    }
                    break;
            }
        }
    }

    /**
     * Traite les actions POST
     */
    public function traiterActionPost() {
        // Export CSV direct
        if (isset($_POST['export_csv'])) {
            $this->exportService->exporterCSV($_POST['export_csv']);
        }

        // Export CSV par mail
        if (isset($_POST['export_csv_mail'])) {
            if ($_POST['export_csv_mail'] === 'but2') {
                $result = $this->exportService->envoiCSVMailBUT2();
            } else {
                $result = $this->exportService->envoiCSVMailBUT3();
            }

            if ($result['success']) {
                $this->messageManager->addSuccess($result['message']);
            } else {
                $this->messageManager->addError($result['message']);
            }
        }

        // Remonter tout
        if (isset($_POST['remonter_tout'])) {
            if ($this->estAutoriseAdmin()) {
                $this->noteService->remonterTout($this->etudiantService);
                $this->messageManager->addSuccess("Toutes les notes prêtes ont été remontées.");
            } else {
                $this->messageManager->addError("Vous n'êtes pas autorisé à effectuer cette action.");
            }
        }
    }

    /**
     * Récupère toutes les données nécessaires pour l'affichage
     */
    public function getDonnees() {
        return [
            'etudiantsBUT2' => $this->etudiantService->getEtudiantsBUT2(),
            'etudiantsBUT3' => $this->etudiantService->getEtudiantsBUT3(),
            'etudiantsNonBloques' => $this->etudiantService->getEtudiantsNonBloques(),
            'etudiantsRemonteeBUT2' => $this->etudiantService->getEtudiantRemonter2A(),
            'etudiantsRemonteeBUT3' => $this->etudiantService->getEtudiantRemonter3A(),
            'messages' => $this->messageManager->getMessages()
        ];
    }

    /**
     * Traite toutes les requêtes
     */
    public function traiterRequetes() {
        $this->traiterActionGet();
        $this->traiterActionPost();
        return $this->getDonnees();
    }
}