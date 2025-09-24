<?php
class Soutenance {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Soutenances à venir pour un enseignant
    public function getSoutenancesAVenir($idEnseignant) {
        $sql = "
            SELECT 
                e.idEtudiant,
                e.nom AS etudiant_nom,
                e.prenom AS etudiant_prenom,
                ent.nom AS entreprise,
                an.nomMaitreStageApp AS maitre,
                es.presenceMaitreStageApp AS maitre_present,
                es.date_h AS date_heure,
                es.IdSalle AS salle,
                es.confidentiel,
                CASE
                    WHEN es.IdEnseignantTuteur = :id THEN 'tuteur'
                    WHEN es.IdSecondEnseignant = :id THEN 'second'
                    ELSE NULL
                END AS role,
                CASE
                    WHEN an.alternanceBUT3 = 1 THEN 'Alternance 3A'
                    WHEN an.but3sinon2 = 1 THEN 'Stage 3A'
                    ELSE 'Stage 2A'
                END AS type_stage
            FROM EvalStage es
            JOIN EtudiantsBUT2ou3 e ON e.IdEtudiant = es.IdEtudiant
            JOIN AnneeStage an ON an.IdEtudiant = e.IdEtudiant AND an.anneeDebut = es.anneeDebut
            JOIN Entreprises ent ON ent.IdEntreprise = an.IdEntreprise
            WHERE es.date_h IS NOT NULL
              AND es.date_h >= NOW()
              AND (es.IdEnseignantTuteur = :id OR es.IdSecondEnseignant = :id)
            ORDER BY es.date_h ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idEnseignant]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Soutenances passées (uniquement si tuteur)
    public function getSoutenancesPassees($idEnseignant) {
    $sql = "
        SELECT 
            e.idEtudiant,
            e.nom AS etudiant_nom,
            e.prenom AS etudiant_prenom,
            ent.nom AS entreprise,
            es.date_h AS date_heure,
            es.Statut
        FROM EvalStage es
        JOIN EtudiantsBUT2ou3 e 
            ON e.IdEtudiant = es.IdEtudiant
        JOIN AnneeStage an 
            ON an.IdEtudiant = e.IdEtudiant 
           AND an.anneeDebut = es.anneeDebut
        JOIN Entreprises ent 
            ON ent.IdEntreprise = an.IdEntreprise
        WHERE es.date_h <= NOW()
          AND es.IdEnseignantTuteur = :id
        ORDER BY es.date_h DESC
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idEnseignant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
