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
                    WHEN es.IdEnseignantTuteur = ? THEN 'tuteur'
                    WHEN es.IdSecondEnseignant = ? THEN 'second'
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
                            AND (es.IdEnseignantTuteur = ? OR es.IdSecondEnseignant = ?)

            UNION ALL

            /* Soutenances d'anglais (dateS) */
            SELECT
                ea.IdEtudiant AS idEtudiant,
                e.nom AS etudiant_nom,
                e.prenom AS etudiant_prenom,
                ent.nom AS entreprise,
                NULL AS maitre,
                NULL AS maitre_present,
                ea.dateS AS date_heure,
                ea.IdSalle AS salle,
                0 AS confidentiel,
                CASE
                    WHEN ea.IdEnseignant = ? THEN 'tuteur'
                    ELSE NULL
                END AS role,
                'Anglais' AS type_stage
            FROM evalanglais ea
            JOIN etudiantsbut2ou3 e ON e.IdEtudiant = ea.IdEtudiant
            LEFT JOIN anneestage an ON an.IdEtudiant = e.IdEtudiant AND an.anneeDebut = ea.anneeDebut
            LEFT JOIN entreprises ent ON ent.IdEntreprise = an.IdEntreprise
                        WHERE ea.dateS IS NOT NULL
                            AND ea.dateS >= NOW()
                            AND (ea.IdEnseignant = ?)

            ORDER BY date_heure ASC
        ";

    $stmt = $this->pdo->prepare($sql);
    // six placeholders total in this query (case tuteur/second x2, where tuteur/second x2, anglais case x1, anglais where x1)
    $stmt->execute([$idEnseignant, $idEnseignant, $idEnseignant, $idEnseignant, $idEnseignant, $idEnseignant]);

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
            es.Statut,
            CASE
                WHEN an.alternanceBUT3 = 1 THEN 'Alternance 3A'
                WHEN an.but3sinon2 = 1 THEN 'Stage 3A'
                ELSE 'Stage 2A'
            END AS type_stage
        FROM EvalStage es
        JOIN EtudiantsBUT2ou3 e 
            ON e.IdEtudiant = es.IdEtudiant
        JOIN AnneeStage an 
            ON an.IdEtudiant = e.IdEtudiant 
           AND an.anneeDebut = es.anneeDebut
        JOIN Entreprises ent 
            ON ent.IdEntreprise = an.IdEntreprise
                WHERE es.date_h <= NOW()
                    AND es.IdEnseignantTuteur = ?

        UNION ALL

        /* Anglais passées */
        SELECT
            ea.IdEtudiant AS idEtudiant,
            e.nom AS etudiant_nom,
            e.prenom AS etudiant_prenom,
            ent.nom AS entreprise,
            ea.dateS AS date_heure,
            ea.Statut,
            'Anglais' AS type_stage
        FROM evalanglais ea
        JOIN etudiantsbut2ou3 e ON e.IdEtudiant = ea.IdEtudiant
        LEFT JOIN anneestage an ON an.IdEtudiant = e.IdEtudiant AND an.anneeDebut = ea.anneeDebut
        LEFT JOIN entreprises ent ON ent.IdEntreprise = an.IdEntreprise
                WHERE ea.dateS <= NOW()
                    AND ea.IdEnseignant = ?

        ORDER BY date_heure DESC
    ";
    $stmt = $this->pdo->prepare($sql);
    // deux placeholders : stage.tuteur, anglais -> fournir l'Id enseignant deux fois
    $stmt->execute([$idEnseignant, $idEnseignant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
