<?php
// Connexion MySQL
$pdo = new PDO("mysql:host=localhost;dbname=evaluationstages;charset=utf8", "root", "");

// Récupération des tâches
$sql = "SELECT * FROM Vue_Taches_Enseignants ORDER BY NomEnseignant, DateEval";
$stmt = $pdo->query($sql);
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Visualisation des tâches enseignants</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="../../stylee.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="admin-block">
        <h1 style="margin-bottom:24px;">Résumé des grilles à remplir par les enseignants</h1>

        <div class="controls-section" style="margin-bottom:24px;">
            <h3 style="margin-bottom:12px;">Masquer/Afficher les colonnes :</h3>
            <div class="controls-row">
                <div class="checkbox-group">
                    <div class="checkbox-item"><input type="checkbox" id="toggleEnseignant" checked><label for="toggleEnseignant">Enseignant</label></div>
                    <div class="checkbox-item"><input type="checkbox" id="toggleEtudiant" checked><label for="toggleEtudiant">Étudiant</label></div>
                    <div class="checkbox-item"><input type="checkbox" id="toggleTaches" checked><label for="toggleTaches">Type d'évaluation</label></div>
                    <div class="checkbox-item"><input type="checkbox" id="toggleStatut" checked><label for="toggleStatut">Statut</label></div>
                    <div class="checkbox-item"><input type="checkbox" id="toggleDate" checked><label for="toggleDate">Date</label></div>
                    <div class="checkbox-item"><input type="checkbox" id="toggleSalle" checked><label for="toggleSalle">Salle</label></div>
                </div>
                <div id="datatable-show-placeholder"></div>
            </div>
        </div>

        <div class="table-container" style="width:100%;">
            <table id="tachesTable" class="display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Étudiant</th>
                        <th>Type d'évaluation</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Salle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $t): ?>
                        <tr>
                            <td><?= $t['NomEnseignant'] . " " . $t['PrenomEnseignant'] ?></td>
                            <td><?= $t['NomEtudiant'] . " " . $t['PrenomEtudiant'] ?></td>
                            <td><?= $t['TypeEvaluation'] ?></td>
                            <td><?= $t['Statut'] ?></td>
                            <td><?= $t['DateEval'] ?></td>
                            <td><?= $t['Salle'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#tachesTable').DataTable({
                "order": [[0, "asc"], [4, "asc"]],
                "paging": true,
                "searching": false,
                "info": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                "columnDefs": [
                    { "visible": true, "targets": [0,1,2,3,4,5] }
                ]
            });

            // Déplace le selecteur "show" dans le placeholder
            var lengthControl = $(table.table().container()).find('.dataTables_length');
            $('#datatable-show-placeholder').append(lengthControl);

            // Boutons de tri personnalisés
            $('#triEnseignant').click(function() {
                table.order([0, 'asc']).draw();
            });
            $('#triEtudiant').click(function() {
                table.order([1, 'asc']).draw();
            });
            $('#triTaches').click(function() {
                table.order([2, 'asc']).draw();
            });
            $('#triDate').click(function() {
                table.order([4, 'asc']).draw();
            });

            // Contrôles de visibilité des colonnes
            $('#toggleEnseignant').change(function() {
                table.column(0).visible(this.checked);
            });
            $('#toggleEtudiant').change(function() {
                table.column(1).visible(this.checked);
            });
            $('#toggleTaches').change(function() {
                table.column(2).visible(this.checked);
            });
            $('#toggleStatut').change(function() {
                table.column(3).visible(this.checked);
            });
            $('#toggleDate').change(function() {
                table.column(4).visible(this.checked);
            });
            $('#toggleSalle').change(function() {
                table.column(5).visible(this.checked);
            });
        });
    </script>

</body>
</html>
