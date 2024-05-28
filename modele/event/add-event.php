<?php
require '../../config/connexion_sql.php';
global $bdd;

// Récupération et validation des entrées
$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;
$id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : null;
$id_vehicule = isset($_POST['id_vehicule']) ? intval($_POST['id_vehicule']) : null;

// Vérification que toutes les entrées sont présentes et valides
if (!$id_user || !$id_vehicule || !$start || !$end) {
    die(json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']));
}

try {
    // Démarrer la transaction
    $bdd->beginTransaction();

    // Préparation de la requête SQL avec un titre temporaire
    $sqlInsert = "INSERT INTO Reservations (title, id_user, id_vehicule, start, end) VALUES ('Temp Title', :id_user, :id_vehicule, :start, :end)";
    $stmt = $bdd->prepare($sqlInsert);

    // Liaison des valeurs aux paramètres
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
    $stmt->bindParam(':start', $start, PDO::PARAM_STR);
    $stmt->bindParam(':end', $end, PDO::PARAM_STR);

    // Exécution de la requête préparée
    if ($stmt->execute()) {
        // Récupérer l'ID de la dernière réservation insérée
        $lastInsertId = $bdd->lastInsertId();

        // Mise à jour du titre de la réservation avec l'ID
        $sqlUpdate = "UPDATE Reservations SET title = CONCAT('Reservation ', :id) WHERE id = :id";
        $stmtUpdate = $bdd->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':id', $lastInsertId, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            // Commit de la transaction
            $bdd->commit();
            echo json_encode(['status' => 'success', 'message' => 'Réservation créée avec succès.', 'reservation_id' => $lastInsertId]);
        } else {
            // Rollback de la transaction
            $bdd->rollBack();
            $error = $stmtUpdate->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la réservation : ' . $error[2]]);
        }
    } else {
        // Rollback de la transaction
        $bdd->rollBack();
        $error = $stmt->errorInfo();
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création de la réservation : ' . $error[2]]);
    }
} catch (PDOException $e) {
    // Rollback de la transaction en cas d'exception
    $bdd->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
}

// Fermeture de la connexion PDO
$bdd = null;
?>
