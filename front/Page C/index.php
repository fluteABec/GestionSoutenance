<?php
    include("config/db.php");

    // Pour le deboggage
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Variables
    session_start();
    $idEnseignant;
    $infoEtud; 
    
    if (isset($_SESSION["professeur_id"])) {
        $idEnseignant = $_SESSION["professeur_id"];
    } else {
        // Cas ou il n'y a pas de idEnseignant dans l'URL
        $idEnseignant = 0; 
    }

    

    // Création des requetes (qui vont chacune retournée un resultat)
    function getIdEtud($mysqli)
    {
        $requete = "SELECT EtudiantsBUT2ou3.IdEtudiant FROM EtudiantsBUT2ou3 ORDER BY EtudiantsBUT2ou3.IdEtudiant;";
        $result = $mysqli->query($requete);

        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    // Permet de récupérer chaque enseignantTuteur avec leur Etudiant ainsi que l'AnnéeUniversité
    function getEnseiWithTheirEtud($mysqli, $idEnseignant)
    {
        $stmt = $mysqli->prepare("SELECT EvalStage.IdEvalStage, Enseignants.IdEnseignant, EtudiantsBUT2ou3.IdEtudiant, AnneesUniversitaires.anneeDebut FROM EvalStage
        JOIN Enseignants ON EvalStage.IdEnseignantTuteur = Enseignants.IdEnseignant
        JOIN EtudiantsBUT2ou3 ON  EvalStage.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
        JOIN AnneesUniversitaires ON EvalStage.anneeDebut = AnneesUniversitaires.anneeDebut
        WHERE Enseignants.IdEnseignant = ?;");
        
        $stmt->bind_param("i", $idEnseignant);
        $stmt->execute();
        $result = $stmt->get_result();

        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    // Liaison de l'enseignant avec letudiant 
    $infoEtud = getEnseiWithTheirEtud($mysqli, $idEnseignant);
    
    function getPortfolioGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("
            SELECT DISTINCT EvalPortFolio.IdEvalPortfolio, EvalPortFolio.IdEtudiant, EtudiantsBUT2ou3.nom, EtudiantsBUT2ou3.prenom, 
                EvalPortFolio.note, EvalPortFolio.commentaireJury, StatutsEval.Statut
            FROM EvalPortFolio
            JOIN EtudiantsBUT2ou3 ON EvalPortFolio.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
            JOIN StatutsEval ON EvalPortFolio.Statut = StatutsEval.Statut
            JOIN EvalStage ON EvalPortFolio.anneeDebut = EvalStage.anneeDebut
            WHERE EtudiantsBUT2ou3.IdEtudiant = ? ;
        ");

        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }

    function getEnglishGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT EvalAnglais.IdEvalAnglais, EtudiantsBUT2ou3.IdEtudiant, EtudiantsBUT2ou3.nom, EtudiantsBUT2ou3.prenom, EvalAnglais.note, EvalAnglais.commentaireJury, EvalAnglais.dateS, StatutsEval.Statut FROM EvalAnglais
        JOIN EtudiantsBUT2ou3 ON EvalAnglais.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
        JOIN StatutsEval ON EvalAnglais.Statut = StatutsEval.Statut
        WHERE EtudiantsBUT2ou3.IdEtudiant = ?");

        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }

    function getSoutenanceGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT EvalSoutenance.IdEvalSoutenance, EtudiantsBUT2ou3.IdEtudiant, EtudiantsBUT2ou3.nom, EtudiantsBUT2ou3.prenom, EvalSoutenance.note, EvalSoutenance.commentaireJury, StatutsEval.Statut FROM EvalSoutenance
        JOIN EtudiantsBUT2ou3 ON EvalSoutenance.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
        JOIN StatutsEval ON EvalSoutenance.Statut = StatutsEval.Statut
        WHERE EtudiantsBUT2ou3.IdEtudiant = ?");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }


    function getRapportGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT EvalRapport.IdEvalRapport, EtudiantsBUT2ou3.IdEtudiant, EtudiantsBUT2ou3.nom, EtudiantsBUT2ou3.prenom, EvalRapport.note, EvalRapport.commentaireJury, StatutsEval.Statut FROM EvalRapport
        JOIN EtudiantsBUT2ou3 ON EvalRapport.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
        JOIN StatutsEval ON  EvalRapport.Statut = StatutsEval.Statut
        WHERE EtudiantsBUT2ou3.IdEtudiant = ?;");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    function getStageGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT EvalStage.IdEvalStage, EtudiantsBUT2ou3.IdEtudiant, EtudiantsBUT2ou3.nom, EtudiantsBUT2ou3.prenom, EvalStage.note, EvalStage.commentaireJury, EvalStage.date_h, Salles.description, StatutsEval.Statut FROM EvalStage
        JOIN EtudiantsBUT2ou3 ON EvalStage.IdEtudiant = EtudiantsBUT2ou3.IdEtudiant
        JOIN Salles ON EvalStage.IdSalle = Salles.IdSalle
        JOIN StatutsEval ON EvalStage.Statut = StatutsEval.Statut
        WHERE EtudiantsBUT2ou3.IdEtudiant = ?;");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            echo "<br> La requete à échoué. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    // Fonction générique pour actions
    function renderActions($statut) {
        if ($statut === "BLOQUEE") {
            return "<button type='submit' name='action' value='debloquer'>Débloquer</button>";
        } elseif ($statut === "REMONTÉE" || $statut === "DIFFUSÉE") {
            return "⛔ Non modifiable";
        } else {
            return "<button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                    <button type='submit' name='action' value='valider'>Valider</button>";
        }
    }

    function readonlyIfLocked($statut) {
        return in_array($statut, ["BLOQUEE","REMONTÉE","DIFFUSÉE"]) ? "readonly" : "";
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Grilles - Enseignant n°<?=$idEnseignant?></title>
        <link rel="stylesheet" href="style.css">
        <meta charset="UTF-8">
    </head>
    <body>
    <h1>Gestion des grilles - Enseignant n°<?=$idEnseignant?> </h1>
    
    <?php foreach ($infoEtud as $idEtud): ?>
    <div class="student-block">
        <h2>Grilles de l'étudiant <?= $idEtud["IdEtudiant"] ?></h2>

                <!-- Portfolio -->
                <div class="card"><h3>PORTFOLIO</h3>
                    <table>
                        <tr>
                            <th>IdPortfolio</th><th>IdEtudiant</th><th>Nom</th>
                            <th>Prénom</th><th>Note</th><th>Commentaire jury</th>
                            <th>Statut</th><th>Actions</th>
                        </tr>
                        <?php foreach (getPortfolioGrid($mysqli, $idEtud["IdEtudiant"]) as $etu): ?>
                        <tr>
                            <form method="POST" action="update.php">
                                <input type="hidden" name="type" value="portfolio">
                                <input type="hidden" name="id" value="<?=$etu['IdEvalPortfolio']?>">
                                <input type="hidden" name="idEtudiant" value="<?=$etu['IdEtudiant']?>">
                                <td><?=$etu['IdEvalPortfolio']?></td>
                                <td><?=$etu['IdEtudiant']?></td>
                                <td><?=$etu['nom']?></td>
                                <td><?=$etu['prenom']?></td>
                                <td><input type="number" name="note" value="<?=$etu['note']?>" min="0" max="20" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><input type="text" name="commentaireJury" value="<?=$etu['commentaireJury']?>" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><?=$etu['Statut']?></td>
                                <td><?=renderActions($etu['Statut'])?></td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Anglais -->
                <div class="card"><h3>ANGLAIS</h3>
                    <table>
                        <tr>
                            <th>IdAnglais</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                            <th>Note</th><th>Commentaire jury</th><th>Date</th>
                            <th>Statut</th><th>Actions</th>
                        </tr>
                        <?php foreach (getEnglishGrid($mysqli, $idEtud["IdEtudiant"]) as $etu): ?>
                        <tr>
                            <form method="POST" action="update.php">
                                <input type="hidden" name="type" value="anglais">
                                <input type="hidden" name="id" value="<?=$etu['IdEvalAnglais']?>">
                                <input type="hidden" name="idEtudiant" value="<?=$etu['IdEtudiant']?>">
                                <td><?=$etu['IdEvalAnglais']?></td>
                                <td><?=$etu['IdEtudiant']?></td>
                                <td><?=$etu['nom']?></td>
                                <td><?=$etu['prenom']?></td>
                                <td><input type="number" name="note" value="<?=$etu['note']?>" min="0" max="20" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><input type="text" name="commentaireJury" value="<?=$etu['commentaireJury']?>" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><?=$etu['dateS']?></td>
                                <td><?=$etu['Statut']?></td>
                                <td><?=renderActions($etu['Statut'])?></td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Soutenance -->
                <div class="card"><h3>SOUTENANCE</h3>
                    <table>
                        <tr>
                            <th>IdSoutenance</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                            <th>Note</th><th>Commentaire jury</th><th>Statut</th><th>Actions</th>
                        </tr>
                        <?php foreach (getSoutenanceGrid($mysqli, $idEtud["IdEtudiant"]) as $etu): ?>
                        <tr>
                            <form method="POST" action="update.php">
                                <input type="hidden" name="type" value="soutenance">
                                <input type="hidden" name="id" value="<?=$etu['IdEvalSoutenance']?>">
                                <input type="hidden" name="idEtudiant" value="<?=$etu['IdEtudiant']?>">
                                <td><?=$etu['IdEvalSoutenance']?></td>
                                <td><?=$etu['IdEtudiant']?></td>
                                <td><?=$etu['nom']?></td>
                                <td><?=$etu['prenom']?></td>
                                <td><input type="number" name="note" value="<?=$etu['note']?>" min="0" max="20" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><input type="text" name="commentaireJury" value="<?=$etu['commentaireJury']?>" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><?=$etu['Statut']?></td>
                                <td><?=renderActions($etu['Statut'])?></td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Rapport -->
                <div class="card"><h3>RAPPORT</h3>
                    <table>
                        <tr>
                            <th>IdRapport</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                            <th>Note</th><th>Commentaire jury</th><th>Statut</th><th>Actions</th>
                        </tr>
                        <?php foreach (getRapportGrid($mysqli, $idEtud["IdEtudiant"]) as $etu): ?>
                        <tr>
                            <form method="POST" action="update.php">
                                <input type="hidden" name="type" value="rapport">
                                <input type="hidden" name="id" value="<?=$etu['IdEvalRapport']?>">
                                <input type="hidden" name="idEtudiant" value="<?=$etu['IdEtudiant']?>">
                                <td><?=$etu['IdEvalRapport']?></td>
                                <td><?=$etu['IdEtudiant']?></td>
                                <td><?=$etu['nom']?></td>
                                <td><?=$etu['prenom']?></td>
                                <td><input type="number" name="note" value="<?=$etu['note']?>" min="0" max="20" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><input type="text" name="commentaireJury" value="<?=$etu['commentaireJury']?>" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><?=$etu['Statut']?></td>
                                <td><?=renderActions($etu['Statut'])?></td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Stage -->
                <div class="card"><h3>STAGE</h3>
                    <table>
                        <tr>
                            <th>IdStage</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                            <th>Note</th><th>Commentaire jury</th><th>Date</th><th>Description</th>
                            <th>Statut</th><th>Actions</th>
                        </tr>
                        <?php foreach (getStageGrid($mysqli, $idEtud["IdEtudiant"]) as $etu): ?>
                        <tr>
                            <form method="POST" action="update.php">
                                <input type="hidden" name="type" value="stage">
                                <input type="hidden" name="id" value="<?=$etu['IdEvalStage']?>">
                                <input type="hidden" name="idEtudiant" value="<?=$etu['IdEtudiant']?>">
                                <td><?=$etu['IdEvalStage']?></td>
                                <td><?=$etu['IdEtudiant']?></td>
                                <td><?=$etu['nom']?></td>
                                <td><?=$etu['prenom']?></td>
                                <td><input type="number" name="note" value="<?=$etu['note']?>" min="0" max="20" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><input type="text" name="commentaireJury" value="<?=$etu['commentaireJury']?>" <?=readonlyIfLocked($etu['Statut'])?>></td>
                                <td><?=$etu['date_h']?></td>
                                <td><?=$etu['description']?></td>
                                <td><?=$etu['Statut']?></td>
                                <td><?=renderActions($etu['Statut'])?></td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

            </div>
        <?php endforeach; ?>
    </body>
</html>