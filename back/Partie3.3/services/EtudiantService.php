<?php
/**
 * Fonctions pour la gestion des étudiants
 */

class EtudiantService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les étudiants BUT2 prêts à la remontée
     */
    public function getEtudiantsBUT2() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom,
                   s.Statut AS statut_stage,
                   p.Statut AS statut_portfolio
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = FALSE
              AND s.Statut = 'BLOQUEE'
              AND p.Statut = 'BLOQUEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les étudiants BUT3 prêts à la remontée
     */
    public function getEtudiantsBUT3() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom,
                   s.Statut AS statut_stage,
                   p.Statut AS statut_portfolio,
                   a.Statut AS statut_anglais
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = TRUE
              AND s.Statut = 'BLOQUEE'
              AND p.Statut = 'BLOQUEE'
              AND a.Statut = 'BLOQUEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les étudiants avec au moins 1 statut saisi
     */
    public function getEtudiantsNonBloques() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom,
                   s.Statut AS statut_stage,
                   p.Statut AS statut_portfolio,
                   COALESCE(a.Statut, 'NON CONCERNE') AS statut_anglais,
                   s.date_h AS date_soutenance
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            LEFT JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE s.date_h < NOW()
              AND (s.Statut = 'SAISIE' OR p.Statut = 'SAISIE' OR (ast.but3sinon2 = TRUE AND a.Statut = 'SAISIE'))
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les étudiants BUT2 déjà remontés
     */
    public function getEtudiantRemonter2A() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom,
                   s.Statut AS statut_stage,
                   p.Statut AS statut_portfolio
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = FALSE
              AND s.Statut = 'REMONTEE'
              AND p.Statut = 'REMONTEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les étudiants BUT3 déjà remontés
     */
    public function getEtudiantRemonter3A() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom,
                   s.Statut AS statut_stage,
                   p.Statut AS statut_portfolio,
                   a.Statut AS statut_anglais
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = TRUE
              AND s.Statut = 'REMONTEE'
              AND p.Statut = 'REMONTEE'
              AND a.Statut = 'REMONTEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste des élèves BUT3 remontés avec leurs notes
     */
    public function getListeEleveRemonter3A() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom, e.mail, 
                   a.note AS note_anglais, 
                   p.note AS note_portfolio, 
                   s.note AS note_stage
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = TRUE
              AND s.Statut = 'REMONTEE'
              AND p.Statut = 'REMONTEE'
              AND a.Statut = 'REMONTEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste des élèves BUT2 remontés avec leurs notes
     */
    public function getListeEleveRemonter2A() {
        $stmt = $this->pdo->query("
            SELECT e.IdEtudiant, e.nom, e.prenom, e.mail, 
                   p.note AS note_portfolio, 
                   s.note AS note_stage
            FROM EtudiantsBUT2ou3 e
            JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
            JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
            JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
            WHERE ast.but3sinon2 = FALSE
              AND s.Statut = 'REMONTEE'
              AND p.Statut = 'REMONTEE'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le mail d'un étudiant via son ID
     */
    public function getMailEtudiant($idEtudiant) {
        $stmt = $this->pdo->prepare("SELECT mail FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchColumn();
    }

    /**
     * Récupère le mail de l'enseignant tuteur d'un étudiant
     */
    public function getMailEnseignantTuteur($idEtudiant) {
        $stmt = $this->pdo->prepare("
            SELECT enseignants.mail 
            FROM evalstage
            JOIN enseignants ON enseignants.IdEnseignant = evalstage.IdEnseignantTuteur
            WHERE evalstage.IdEtudiant = ?
        ");
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchColumn();
    }
}