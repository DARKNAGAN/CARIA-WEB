<?php
function get_checkPlaque(){ // Verification pour ajout d'une vehicule
	global $bdd;
	$plaque=$_POST['plaque'];
	$req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Vehicules WHERE plaque =:plaque');
	$req->bindValue(':plaque',$plaque, PDO::PARAM_STR);
	$req->execute();
	$plaque_free=($req->fetchColumn()==0)?1:0;
	$req->CloseCursor();
	return $plaque_free;
}
function get_CarsInfo(){ // Informations de tout les vehicules dans un tableau
    global $bdd;
    $req = $bdd->prepare('SELECT id, plaque, image, marque, modele, annee, disponible, latitude, longitude, ip FROM Vehicules ORDER BY id');
    $req->execute();
    $data = $req->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}
function getCarInfoMap() {
    global $bdd;
    // Requête pour récupérer les Vehicules
    $query = "SELECT * FROM Vehicules";
    $statement = $bdd->query($query);
    $cars = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Initialisation d'un tableau pour stocker les données des Vehicules
    $vehiculesData = array();

    // Remplissage du tableau avec les données récupérées de la base de données
    foreach ($cars as $car) {
        $vehiculesData[$car['id']] = array(
            "lat" => $car['latitude'],
            "lon" => $car['longitude'],
            "img" => $car['image'],
            "marque" => $car['marque'],
            "modele" => $car['modele'],
            "dispo" => $car['disponible'],
            "annee" => $car['annee']
        );
    }
    return json_encode($vehiculesData);
}
function get_VehiculesCount(){
	global $bdd;
	$TotalDesVehiculess = $bdd->query('SELECT COUNT(*) FROM Vehicules')->fetchColumn();
	return $TotalDesVehiculess;
}
function get_CarInfoId(){
	global $bdd;
	$id=1;
	//On prend les infos de la Vehicules
	$req = $bdd->prepare('SELECT latitude, longitude FROM Vehicules WHERE id=:id');
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$data = $req->fetch();
	return $data;
}
function post_RemoveVehicule($id) {
    global $bdd;
    
    // Récupérer le chemin de l'image à partir de la base de données
    $req = $bdd->prepare('SELECT image FROM Vehicules WHERE id=:id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $data = $req->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        // Supprimer l'image
        $imagePath = "." . $data['image'];
        unlink($imagePath);
    }
    // Supprimer le véhicule de la base de données
    $req = $bdd->prepare('DELETE FROM Vehicules WHERE id=:id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $req->closeCursor();
}
function post_RegistreCar(){ // Ajout vehicule en DB
	global $bdd;
	$plaque=$_POST['plaque'];
	$marque=$_POST['marque'];
	$modele=$_POST['modele'];
	$annee=$_POST['annee'];
	$image = $_FILES['image'];
    // Créer le répertoire pour les images si nécessaire
    $dirPath = "./images/vehicules/" . $plaque . "/";
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0700, true);
    }
    // Vérifier si l'image est fournie et valide
    if (!empty($image['size']) && $image['error'] === UPLOAD_ERR_OK) {
        $nomimage = edit_image($image, $plaque);
    } else {
        // Utiliser une image par défaut si aucune image n'est fournie
        $defaultImagePath = "./images/vehicules/img_vehicule.png";
        $dirPath = "./images/vehicules/" . $plaque . "/img_vehicule.png";
        $nomimage = "/images/vehicules/" . $plaque . "/img_vehicule.png";
        copy($defaultImagePath, $dirPath);
    }
	$req = $bdd->prepare('INSERT INTO Vehicules (plaque, marque, modele, annee, image) VALUES (:plaque, :marque, :modele, :annee, :nomimage)');
	$req->bindValue(':plaque', $plaque, PDO::PARAM_STR);
	$req->bindValue(':marque', $marque, PDO::PARAM_STR);
	$req->bindValue(':modele', $modele, PDO::PARAM_STR);
	$req->bindValue(':annee', $annee, PDO::PARAM_STR);
	$req->bindValue(':nomimage', $nomimage, PDO::PARAM_STR);
	$req->execute();
}
function edit_image($image, $plaque) {
    if (isset($image)) {
        $source = $image['tmp_name'];
        $dir = "./images/vehicules/" . $plaque . "/img_vehicule.png";
        $nomimage = "/images/vehicules/" . $plaque . "/img_vehicule.png";
        // Redimensionner l'image à la taille spécifiée (300x300)
        $newWidth = 300;
        $newHeight = 300;
        list($width, $height) = getimagesize($source);
        $imageResized = imagecreatetruecolor($newWidth, $newHeight);
        switch (exif_imagetype($source)) {
            case IMAGETYPE_JPEG:
                $imageSource = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $imageSource = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $imageSource = imagecreatefromgif($source);
                break;
            default:
                return "Unsupported image type";
        }
        imagecopyresized($imageResized, $imageSource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        // Sauvegarder l'image redimensionnée
        imagepng($imageResized, $dir);
        // Libérer la mémoire
        imagedestroy($imageResized);
        imagedestroy($imageSource);
        // Retourner le chemin relatif de l'image redimensionnée
        return $nomimage;
    }
}
// Fonction pour afficher un message d'erreur
function displayInscriptionErrorMessage($errors) {
    echo '<script>document.addEventListener("DOMContentLoaded", function() {
    var messageDiv = document.getElementById("message");
    var errorMessage = `<div class="alert alert-danger">
        <section id="content" class="page-content">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Echec de l\'inscription</h3>
                </div>
                <div class="card-body">
                    <p>Erreur(s) dans le formulaire d\'inscription :</p>
                    <ul style="list-style-type:none;">'; // Début de la liste d'erreurs
                        foreach ($errors as $error) {
                            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>"; // Ajout de chaque erreur à la liste
                        }
        echo '       </ul>
                    </div>
                </div>
            </section>
        </div>
    `;
    messageDiv.innerHTML = errorMessage;
    });
    </script>';
}

// Fonction pour afficher un message de succès
function displayInscriptionSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Inscription terminée</h3>
                            </div>
                            <div class="card-body">
                                <h5>Bienvenue ' . htmlspecialchars($_POST['pseudo'], ENT_QUOTES, 'UTF-8') . '<br><br> vous êtes maintenant inscrit sur la plateforme!</h5><br>
                                <p>Cliquez <a href="./index.php">ici</a> pour revenir à l\'acceuil</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = "index.php";
            }, 5000); // 5 secondes
        });
    </script>';
}
?>