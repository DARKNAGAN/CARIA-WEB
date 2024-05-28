<?php
require '../../config/connexion_sql.php';
global $bdd;

// Vérifier que l'utilisateur est connecté et que l'ID de session est défini
if (!isset($id_session)) {
    die(json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']));
}
$id_user = $id_session; // Récupérer l'ID de l'utilisateur depuis la session

// Fonction de validation des dates
function validateDateTime($dateTime, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $dateTime);
    return $d && $d->format($format) === $dateTime;
}

// Récupération et validation des entrées
$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;
$id_vehicule = isset($_POST['id_vehicule']) ? intval($_POST['id_vehicule']) : null;

// Vérification que toutes les entrées sont présentes et valides
if (!$id_vehicule || !$start || !$end) {
    die(json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']));
}

// Validation des formats de date
if (!validateDateTime($start) || !validateDateTime($end)) {
    die(json_encode(['status' => 'error', 'message' => 'Les dates doivent être dans un format valide (YYYY-MM-DD HH:MM:SS).']));
}

try {
    // Démarrer la transaction
    $bdd->beginTransaction();

    // Préparation de la requête SQL avec des paramètres liés
    $sqlInsert = "INSERT INTO Reservations (id_user, id_vehicule, start, end) VALUES (:id_user, :id_vehicule, :start, :end)";
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
