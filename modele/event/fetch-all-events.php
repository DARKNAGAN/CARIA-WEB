<?php
require '../../config/connexion_sql.php';
global $bdd;
$json = array();
$sqlQuery = "SELECT res.*, car.marque, car.modele, usr.pseudo, usr.adresse FROM Reservations res INNER JOIN Clients usr ON res.id_user = usr.id INNER JOIN Vehicules car ON res.id_vehicule = car.id ORDER BY res.id";
$stmt = $bdd->query($sqlQuery);
$eventArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($eventArray as &$event) {
    $event['vehicule'] = $event['marque'] . ' ' . $event['modele'];
}
foreach ($eventArray as &$event) {
    $event['utilisateur'] = $event['pseudo'];
    $event['adresse'] = $event['adresse'];

}
echo json_encode($eventArray);
$bdd = null;
?>