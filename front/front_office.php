<!DOCTYPE html>

<?php require '../db.php';
?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Front Office - Gestion des évaluations</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="containerHAUT">
<?php
// Vérifier si l'utilisateur est connecté
session_start();
// Récupérer l'identifiant du professeur depuis la session
if (!isset($_SESSION['identifiant']) ) {
    header("Location: ../indedddx.html");
    exit();
}
$identifiant = $_SESSION['identifiant'];
$professorName = 'Default';

// Récupérer le nom du professeur depuis la base de données
$sql = "SELECT nom FROM Enseignants WHERE mail = :identifiant";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':identifiant', $identifiant);
$stmt->execute();
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le professeur a été trouvé
if ($professor) {
    $professorName = $professor['nom'];
} else {
    $professorName = 'Inconnu';
}

echo "<h1>Bienvenue, professorName </h1>"

?>
    <div class="containerBAS">
        <h2>Liste des étudiants associés</h2>
        <table class="student-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>  
                    <th>Grilles d'évaluation</th>
                </tr>
            </thead>
            <tbody id="student-list">
                <!-- Les étudiants seront ajoutés ici dynamiquement -->
            </tbody>
        </table>
    </div>
    <script>
        // Récupérer le nom du professeur depuis la session (via PHP)
        document.addEventListener('DOMContentLoaded', function() {
            fetch('front.php?action=getProfessorName')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('professor-name').textContent = data.professorName;
                });

            // Récupérer la liste des étudiants associés
            fetch('../front.php?action=getStudents')
                .then(response => response.json())
                .then(data => {
                    const studentList = document.getElementById('student-list');
                    data.students.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${student.nom}</td>
                            <td>${student.prenom}</td>
                            <td>${student.mail}</td>
                            <td>
                                <a href="grille_portfolio.html?student=${student.id}">Portfolio</a><br>
                                <a href="grille_soutenance.html?student=${student.id}">Soutenance</a><br>
                                <a href="grille_rapport.html?student=${student.id}">Rapport</a>
                            </td>
                        `;
                        studentList.appendChild(row);
                    });
                });
        });
    </script>
</body>
</html>
