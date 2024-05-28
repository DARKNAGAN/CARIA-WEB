<?php
# INITIALISATION DE LA MAP
require '../config/connexion_sql.php';
global $bdd;
// Requête pour récupérer les Vehicules
$query = "SELECT * FROM Vehicules";
$statement = $bdd->query($query);
$cars = $statement->fetchAll(PDO::FETCH_ASSOC);
// Initialisation d'un tableau pour stocker les données des Vehicules
$Vehicules = array();
// Remplissage du tableau avec les données récupérées de la base de données
foreach ($cars as $car) {
    $Vehicules[$car['id']] = array(
        "lat" => $car['latitude'],
        "lon" => $car['longitude'],
        "img" => $car['image'],
        "marque" => $car['marque'],
        "modele" => $car['modele'],
        "dispo" => $car['disponible'],
        "annee" => $car['annee']);
}
// Renvoi des données au format JSON
header('Content-Type: application/json');
echo json_encode($Vehicules);
?>