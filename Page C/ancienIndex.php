
<?php 
/*
Code au cas ou ne pas supprimé

            foreach ($infoEtud as $idEtud) {
                echo "<div class='student-block'>";
                echo "<h2>Grilles de l'étudiant ". $idEtud["IdEtudiant"]. "</h2>";

                // === Portfolio ===
                echo "<div class='card'><h3>PORTFOLIO</h3>";
                echo "<table>
                    <tr>
                        <th>IdPortfolio</th><th>IdEtudiant</th><th>Nom</th>
                        <th>Prénom</th><th>Note</th><th>Commentaire jury</th>
                        <th>Statut</th><th>Actions</th>
                    </tr>";

                foreach (getPortfolioGrid($mysqli, $idEtud["IdEtudiant"]) as $etudiantActuel) {
                    $bloquee = ($etudiantActuel['Statut'] === 'BLOQUEE');
                    echo "<tr>
                        <form method='POST' action='update.php'>
                            <input type='hidden' name='type' value='portfolio'>
                            <input type='hidden' name='id' value='{$etudiantActuel['IdEvalPortfolio']}'>
                            <input type='hidden' name='idEtudiant' value='{$etudiantActuel['IdEtudiant']}'>
                            <td>{$etudiantActuel['IdEvalPortfolio']}</td>
                            <td>{$etudiantActuel['IdEtudiant']}</td>
                            <td>{$etudiantActuel['nom']}</td>
                            <td>{$etudiantActuel['prenom']}</td>
                            <td><input type='number' name='note' value='{$etudiantActuel['note']}' min='0' max='20'></td>
                            <td><input type='text' name='commentaireJury' value='{$etudiantActuel['commentaireJury']}'></td>
                            <td>{$etudiantActuel['Statut']}</td>
                            <td>
                                ";

                    if ($bloquee) {
                        // Si bloquée → proposer de débloquer
                        echo "<button type='submit' name='action' value='debloquer'>Débloquer</button>";
                    } else {
                        // Sinon → proposer enregistrer/valider
                        echo "<button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                              <button type='submit' name='action' value='valider'>Valider</button>";
                    }
                
                    echo "</td>
                        </form>
                    </tr>";
                }
                echo "</table></div>";

                // === Anglais ===
                echo "<div class='card'><h3>ANGLAIS</h3>";
                echo "<table>
                    <tr>
                        <th>IdAnglais</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                        <th>Note</th><th>Commentaire jury</th><th>Date</th>
                        <th>Statut</th><th>Actions</th>
                    </tr>";

                foreach (getEnglishGrid($mysqli, $idEtud["IdEtudiant"]) as $etudiantActuel) {
                    echo "<tr>
                        <form method='POST' action='update.php'>
                            <input type='hidden' name='type' value='anglais'>
                            <input type='hidden' name='id' value='{$etudiantActuel['IdEvalAnglais']}'>
                            <input type='hidden' name='idEtudiant' value='{$etudiantActuel['IdEtudiant']}'>
                            <td>{$etudiantActuel['IdEvalAnglais']}</td>
                            <td>{$etudiantActuel['IdEtudiant']}</td>
                            <td>{$etudiantActuel['nom']}</td>
                            <td>{$etudiantActuel['prenom']}</td>
                            <td><input type='number' name='note' value='{$etudiantActuel['note']}' min='0' max='20'></td>
                            <td><input type='text' name='commentaireJury' value='{$etudiantActuel['commentaireJury']}'></td>
                            <td>{$etudiantActuel['dateS']}</td>
                            <td>{$etudiantActuel['Statut']}</td>
                            <td>
                                <button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                                <button type='submit' name='action' value='valider'>Valider</button>
                            </td>
                        </form>
                    </tr>";
                }
                echo "</table></div>";

                // === Soutenance ===
                echo "<div class='card'><h3>SOUTENANCE</h3>";
                echo "<table>
                    <tr>
                        <th>IdSoutenance</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                        <th>Note</th><th>Commentaire jury</th><th>Statut</th><th>Actions</th>
                    </tr>";
                foreach (getSoutenanceGrid($mysqli, $idEtud["IdEtudiant"]) as $etudiantActuel) {
                    echo "<tr>
                        <form method='POST' action='update.php'>
                            <input type='hidden' name='type' value='soutenance'>
                            <input type='hidden' name='id' value='{$etudiantActuel['IdEvalSoutenance']}'>
                            <input type='hidden' name='idEtudiant' value='{$etudiantActuel['IdEtudiant']}'>
                            <td>{$etudiantActuel['IdEvalSoutenance']}</td>
                            <td>{$etudiantActuel['IdEtudiant']}</td>
                            <td>{$etudiantActuel['nom']}</td>
                            <td>{$etudiantActuel['prenom']}</td>
                            <td><input type='number' name='note' value='{$etudiantActuel['note']}' min='0' max='20'></td>
                            <td><input type='text' name='commentaireJury' value='{$etudiantActuel['commentaireJury']}'></td>
                            <td>{$etudiantActuel['Statut']}</td>
                            <td>
                                <button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                                <button type='submit' name='action' value='valider'>Valider</button>
                            </td>
                        </form>
                    </tr>";
                }
                echo "</table></div>";

                // === Rapport ===
                echo "<div class='card'><h3>RAPPORT</h3>";
                echo "<table>
                    <tr>
                        <th>IdRapport</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                        <th>Note</th><th>Commentaire jury</th><th>Statut</th><th>Actions</th>
                    </tr>";
                foreach (getRapportGrid($mysqli, $idEtud["IdEtudiant"]) as $etudiantActuel) {
                    echo "<tr>
                        <form method='POST' action='update.php'>
                            <input type='hidden' name='type' value='rapport'>
                            <input type='hidden' name='id' value='{$etudiantActuel['IdEvalRapport']}'>
                            <input type='hidden' name='idEtudiant' value='{$etudiantActuel['IdEtudiant']}'>
                            <td>{$etudiantActuel['IdEvalRapport']}</td>
                            <td>{$etudiantActuel['IdEtudiant']}</td>
                            <td>{$etudiantActuel['nom']}</td>
                            <td>{$etudiantActuel['prenom']}</td>
                            <td><input type='number' name='note' value='{$etudiantActuel['note']}' min='0' max='20'></td>
                            <td><input type='text' name='commentaireJury' value='{$etudiantActuel['commentaireJury']}'></td>
                            <td>{$etudiantActuel['Statut']}</td>
                            <td>
                                <button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                                <button type='submit' name='action' value='valider'>Valider</button>
                            </td>
                        </form>
                    </tr>";
                }
                echo "</table></div>";

                // === Stage ===
                echo "<div class='card'><h3>STAGE</h3>";
                echo "<table>
                    <tr>
                        <th>IdStage</th><th>IdEtudiant</th><th>Nom</th><th>Prénom</th>
                        <th>Note</th><th>Commentaire jury</th><th>Date</th><th>Description</th>
                        <th>Statut</th><th>Actions</th>
                    </tr>";
                foreach (getStageGrid($mysqli, $idEtud["IdEtudiant"]) as $etudiantActuel) {
                    echo "<tr>
                        <form method='POST' action='update.php'>
                            <input type='hidden' name='type' value='stage'>
                            <input type='hidden' name='id' value='{$etudiantActuel['IdEvalStage']}'>
                            <input type='hidden' name='idEtudiant' value='{$etudiantActuel['IdEtudiant']}'>
                            <td>{$etudiantActuel['IdEvalStage']}</td>
                            <td>{$etudiantActuel['IdEtudiant']}</td>
                            <td>{$etudiantActuel['nom']}</td>
                            <td>{$etudiantActuel['prenom']}</td>
                            <td><input type='number' name='note' value='{$etudiantActuel['note']}' min='0' max='20'></td>
                            <td><input type='text' name='commentaireJury' value='{$etudiantActuel['commentaireJury']}'></td>
                            <td>{$etudiantActuel['date_h']}</td>
                            <td>{$etudiantActuel['description']}</td>
                            <td>{$etudiantActuel['Statut']}</td>
                            <td>
                                <button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                                <button type='submit' name='action' value='valider'>Valider</button>
                            </td>
                        </form>
                    </tr>";
                }
                echo "</table></div>";

                echo "</div>"; // fin student-block
                
            }
             */
        ?>
       