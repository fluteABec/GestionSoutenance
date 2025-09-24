<?php
function btnAjouter($url, $label = "Ajouter") {
    return "<a href='$url' class='btn btn-success'>➕ $label</a>";
}

function btnModifier($url, $label = "Modifier") {
    return "<a href='$url' class='btn btn-warning'>✏️ $label</a>";
}

function btnSupprimer($url, $label = "Supprimer") {
    return "<a href='$url' class='btn btn-danger' onclick='return confirm(\"Supprimer ?\")'>🗑️ $label</a>";
}

?>