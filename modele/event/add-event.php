<?php
require '../../config/connexion_sql.php';
global $bdd;

// Fonction de validation des dates
function validateDateTime($dateTime, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $dateTime);
    return $d && $d->format($format) === $dateTime;
}

// Récupération et validation des entrées
$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;
$id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : null;
$id_vehicule = isset($_POST['id_vehicule']) ? intval($_POST['id_vehicule']) : null;

// Vérification que toutes les entrées sont présentes et valides
if (!$id_user || !$id_vehicule || !$start || !$end) {
    die(json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']));
}

// Vérification que l'ID du véhicule est un entier positif
if ($id_vehicule <= 0) {
    die(json_encode(['status' => 'error', 'message' => 'ID du véhicule invalide.']));
}

// Vérification que l'ID du véhicule existe dans la base de données
$stmtVehicule = $bdd->prepare("SELECT COUNT(*) FROM Vehicules WHERE id = :id_vehicule");
$stmtVehicule->bindParam(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
$stmtVehicule->execute();
if ($stmtVehicule->fetchColumn() == 0) {
    die(json_encode(['status' => 'error', 'message' => 'Le véhicule spécifié n\'existe pas.']));
}

// Vérification que l'ID du véhicule existe dans la base de données
$stmtUser = $bdd->prepare("SELECT COUNT(*) FROM Clients WHERE id = :id_user");
$stmtUser->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmtUser->execute();
if ($stmtUser->fetchColumn() == 0) {
    die(json_encode(['status' => 'error', 'message' => 'L\'utilisateur spécifié n\'existe pas.']));
}

// Validation des formats de date
if (!validateDateTime($start) || !validateDateTime($end)) {
    die(json_encode(['status' => 'error', 'message' => 'Les dates doivent être dans un format valide (YYYY-MM-DD HH:MM:SS).']));
}

// Vérification que la date de début est postérieure à la date actuelle
$currentDateTime = new DateTime();
$startDateTime = new DateTime($start);
if ($startDateTime <= $currentDateTime) {
    die(json_encode(['status' => 'error', 'message' => 'Vous ne pouvez réserver que pour le jour même ou les jours suivants.']));
}

// Vérification que la date de fin est postérieure à la date de début
$endDateTime = new DateTime($end);
if ($endDateTime <= $startDateTime) {
    die(json_encode(['status' => 'error', 'message' => 'La date de fin doit être postérieure à la date de début.']));
}

// Vérification que l'ID du client existe dans la base de données
$stmtResUser = $bdd->prepare("SELECT COUNT(*) FROM Reservations WHERE id_user = :id_user AND start < :end AND end > :start");
$stmtResUser->bindParam(':id_user', $id_user , PDO::PARAM_INT);
$stmtResUser->bindParam(':start', $start, PDO::PARAM_STR);
$stmtResUser->bindParam(':end', $end, PDO::PARAM_STR);
$stmtResUser->execute();
if ($stmtResUser->fetchColumn() > 0) {
    die(json_encode(['status' => 'error', 'message' => 'L\'utilisateur à déjà réservé un véhicule pendant cette période.']));
}

// Vérification que l'ID du véhicule existe dans la base de données
$stmtResVehicule = $bdd->prepare("SELECT COUNT(*) FROM Reservations WHERE id_vehicule = :id_vehicule AND start < :end AND end > :start");
$stmtResVehicule->bindParam(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
$stmtResVehicule->bindParam(':start', $start, PDO::PARAM_STR);
$stmtResVehicule->bindParam(':end', $end, PDO::PARAM_STR);
$stmtResVehicule->execute();
if ($stmtResVehicule->fetchColumn() > 0) {
    die(json_encode(['status' => 'error', 'message' => 'Le véhicule est déjà réservé pendant cette période.']));
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
