<?php
    // Préparer la requête SQL pour la mise à jour des données existantes
    $stmt = $bdd->prepare("UPDATE Vehicules SET latitude = :latitude, longitude = :longitude WHERE id = :id");
    // Liage des valeurs des données POST aux paramètres de la requête SQL
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->bindParam(':latitude', $_POST['latitude']);
    $stmt->bindParam(':longitude', $_POST['longitude']);
    // Exécution de la requête SQL
    $stmt->execute();
    echo "Données POST mises à jour avec succès dans la base de données.";
    // Fermeture de la connexion à la base de données
    $bdd = null;
    // Sauvegarde des données POST dans un fichier
    $file_path = 'logs/post_data_location.txt';
    $post_data = json_encode($_POST);
    $file_handle = fopen($file_path, 'a');
    fwrite($file_handle, $post_data . PHP_EOL);
    fclose($file_handle);
?>