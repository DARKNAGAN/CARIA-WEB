<?php
require '../../config/connexion_sql.php';
global $bdd;
$id = $_POST['id'];
$sqlDelete = "DELETE FROM Reservations WHERE id=:id";
$stmt = $bdd->prepare($sqlDelete);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
// Utilisation de rowCount() pour obtenir le nombre de lignes affectées
$affectedRows = $stmt->rowCount();
echo $affectedRows;
// Fermeture de la connexion PDO
$bdd = null;
?>