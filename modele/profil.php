<?php
class Connexion {
   // Méthode pour enregistrer une tentative de connexion infructueuse
    public function recordFailedLoginAttempt($pseudo) {
        global $bdd;
        $timestamp = time();
        $req = $bdd->prepare('INSERT INTO tentatives_connexion (pseudo, timestamp) VALUES (:pseudo, :timestamp)');
        $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $req->bindValue(':timestamp', $timestamp, PDO::PARAM_INT);
        $req->execute();
    }

    // Méthode pour vérifier si un utilisateur est bloqué
    public function isUserBlocked($pseudo) {
        global $bdd;
        $blockDuration = 300; // Durée de blocage en secondes (300 secondes = 5 minutes)
        $timestamp = time() - $blockDuration; // Calculer le timestamp il y a 5 minutes

        $req = $bdd->prepare('SELECT COUNT(*) AS attempts FROM tentatives_connexion WHERE pseudo = :pseudo AND timestamp > :timestamp');
        $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $req->bindValue(':timestamp', $timestamp, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);

        // Si le nombre de tentatives de connexion infructueuses dépasse un certain seuil, l'utilisateur est bloqué
        return $result['attempts'] >= 3; // Vous pouvez ajuster ce nombre selon vos besoins
    }
    function get_MemberInfoId(){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;

	//On prend les infos du membre
	$req = $bdd->prepare('SELECT pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre, privilege FROM Clients WHERE id=:id');
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$userData = $req->fetch();
	return $userData;
    }

    public function checkCredentials($pseudo, $password) {
        global $bdd;
        $req = $bdd->prepare('SELECT mdp FROM Clients WHERE pseudo = :pseudo');
        $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $req->execute();
        $userData = $req->fetch();
        if ($userData && password_verify($password, $userData['mdp'])) {
            return true;
        }
        return false;
    }

    public function check_Password($pseudo) {
        global $bdd;
        $req = $bdd->prepare('SELECT mdp, id, privilege, pseudo FROM Clients WHERE pseudo = :pseudo');
        $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $req->execute();
        $userData = $req->fetch(PDO::FETCH_ASSOC);
        return $userData ?: null; // Renvoyer null si aucune donnée n'est trouvée
    }
}
// Fonction pour vérifier si l'email est disponible
function isEmailAvailable($email) {
    return get_checkMail($email);
}
function get_MemberInfoId(){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;

	//On prend les infos du membre
	$req = $bdd->prepare('SELECT pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre, privilege FROM Clients WHERE id=:id');
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$userData = $req->fetch();
	return $userData;
}

function get_MemberCount() {
    global $bdd;
    return $bdd->query('SELECT COUNT(*) FROM Clients')->fetchColumn();
}

function get_LastMember() {
    global $bdd;
    $req = $bdd->query('SELECT pseudo, id FROM Clients ORDER BY id DESC LIMIT 1');
    return $req->fetch();
}

function get_checkPseudo($pseudo) {
    global $bdd;
    $req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE pseudo = :pseudo');
    $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
    $req->execute();
    return $req->fetchColumn() == 0;
}

function get_checkMail($email) {
    global $bdd;
    $req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE email = :mail');
    $req->bindValue(':mail', $email, PDO::PARAM_STR);
    $req->execute();
    return $req->fetchColumn() == 0;
}

function get_checkMyMail($email,$id) {
    global $bdd;
    $req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE email = :mail AND id != :id');
    $req->bindValue(':mail', $email, PDO::PARAM_STR);
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    return $req->fetchColumn() == 0;
}

function get_ProfilsInfo() {
    global $bdd;
    $req = $bdd->query('SELECT id, privilege, dateenregistre, pseudo, prenom, nom, phone, adresse, mdp, email, avatar FROM Clients ORDER BY id');
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function get_MemberInfo($id) {
    global $bdd;
    $req = $bdd->prepare('SELECT pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre FROM Clients WHERE id = :id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch(PDO::FETCH_ASSOC);
}

function post_Registre($pseudo, $password, $email, $localisation, $prenom, $nom, $phone, $avatar) {
    global $bdd;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $imagePath = handleAvatarUpload($pseudo, $avatar);

    $req = $bdd->prepare('INSERT INTO Clients (pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre)
                          VALUES (:pseudo, :pass, :email, :avatar, :localisation, :prenom, :nom, :phone, NOW())');
    $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
    $req->bindValue(':pass', $hashedPassword, PDO::PARAM_STR);
    $req->bindValue(':email', $email, PDO::PARAM_STR);
    $req->bindValue(':avatar', $imagePath, PDO::PARAM_STR);
    $req->bindValue(':localisation', $localisation, PDO::PARAM_STR);
    $req->bindValue(':prenom', $prenom, PDO::PARAM_STR);
    $req->bindValue(':nom', $nom, PDO::PARAM_STR);
    $req->bindValue(':phone', $phone, PDO::PARAM_STR);
    $req->execute();
}

// Pour la fonction post_UpdateProfile
function post_UpdateProfile($id, $pseudo, $pass = null, $email = null, $localisation = null, $phone = null, $avatar = null) {
    global $bdd;
    $fields = [];
    $params = [':id' => $id];

    if ($pass !== null) {
        $fields[] = 'mdp = :mdp';
        $params[':mdp'] = password_hash($pass, PASSWORD_DEFAULT);
    }
    if ($email !== null) {
        $fields[] = 'email = :mail';
        $params[':mail'] = $email;
    }
    if ($localisation !== null) {
        $fields[] = 'adresse = :loc';
        $params[':loc'] = $localisation;
    }
    if ($phone !== null) {
        $fields[] = 'phone = :phone';
        $params[':phone'] = $phone;
    }
    if (!empty($_FILES['avatar']['name'])) {
    $fields[] = 'avatar = :avatar';
    $params[':avatar'] = handleAvatarUpload($pseudo, $_FILES['avatar']);
    }

    $sql = 'UPDATE Clients SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $req = $bdd->prepare($sql);
    foreach ($params as $key => $value) {
        $req->bindValue($key, $value, PDO::PARAM_STR);
    }
    $req->execute();
}

function handleAvatarUpload($pseudo, $avatar) {
    $dirPath = "./images/avatars/" . $pseudo . "/";
    $Path = "./images/avatars/" . $pseudo . "/";
	$defaultDirImagePath = "./images/avatars/img_user.jpg";
    $dirImagePath = "./images/avatars/" . $pseudo . "/img_user.jpg";
    $imagePath = "/images/avatars/" . $pseudo . "/img_user.jpg";
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0700, true);
    }
    if (!empty($avatar['size']) && $avatar['error'] === UPLOAD_ERR_OK) {
        return edit_avatar($avatar, $pseudo);
    } else {
        copy($defaultDirImagePath, $dirImagePath);
        return $imagePath;
    }
}

function edit_avatar($image, $pseudo) {
	$defaultDirImagePath = "./images/avatars/img_user.jpg";
    $dirImagePath = "./images/avatars/" . $pseudo . "/img_user.jpg";
    $imagePath = "/images/avatars/" . $pseudo . "/img_user.jpg";
    $newWidth = 100;
    $newHeight = 100;
    list($width, $height) = getimagesize($image['tmp_name']);
    $imageResized = imagecreatetruecolor($newWidth, $newHeight);
    
    switch (exif_imagetype($image['tmp_name'])) {
        case IMAGETYPE_JPEG:
            $imageSource = imagecreatefromjpeg($image['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $imageSource = imagecreatefrompng($image['tmp_name']);
            break;
        case IMAGETYPE_GIF:
            $imageSource = imagecreatefromgif($image['tmp_name']);
            break;
        default:
            return "Unsupported image type";
    }
    
    imagecopyresampled($imageResized, $imageSource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($imageResized, $dirImagePath, 85);
    imagedestroy($imageResized);
    imagedestroy($imageSource);
    return $imagePath;
}
function post_RemoveAvatar($pseudo){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;
	$defaultDirImagePath = "images/avatars/img_user.jpg";
    $dirImagePath = "images/avatars/" . $pseudo . "/img_user.jpg";
    $imagePath = "/images/avatars/" . $pseudo . "/img_user.jpg";
	// Utiliser une image par défaut si aucune image n'est fournie
    copy($defaultDirImagePath, $dirImagePath);
}

function post_RemoveClient($id) {
    global $bdd;
     // Suppression des réservations lié à l'utilisateurs de la base de données
    $req = $bdd->prepare('DELETE FROM Reservations WHERE id_user=:id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    // Suppression de l'image
    $req = $bdd->prepare('SELECT avatar FROM Clients WHERE id= :id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $userData = $req->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $imagePath = "." . $userData['avatar'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $folderPath = dirname($imagePath);
        if (is_dir($folderPath)) {
            rmdir($folderPath);
        }
    }
    // Suppression de l'utilisateur de la base de données
    $req = $bdd->prepare('DELETE FROM Clients WHERE id=:id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    session_destroy();
}
?>