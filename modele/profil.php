<?php
# Connexion modele
class Connexion 
{
    public function checkCredentials($pseudo, $password) {
		// Ici, vous devriez effectuer la validation des identifiants
        // en interagissant avec une base de données ou une autre source de données.
        // Pour simplifier, nous supposerons que les identifiants sont corrects.
        return true;
    }
function check_Password(){
		global $bdd;
		$pseudo=$_POST['pseudo_connect'];
		$req = $bdd->prepare('SELECT mdp, id, privilege, pseudo FROM Clients WHERE pseudo = :pseudo');
		$req->bindValue(':pseudo',$pseudo , PDO::PARAM_STR);
		$req->execute();
		$data = $req->fetch();
		return $data;
	}
}
function get_MemberCount(){
	global $bdd;
	$TotalDesMembres = $bdd->query('SELECT COUNT(*) FROM Clients')->fetchColumn();
	return $TotalDesMembres;
}
function get_LastMember(){
	global $bdd;
	$req = $bdd->query('SELECT pseudo, id FROM Clients ORDER BY id DESC LIMIT 0, 1');
	$data = $req->fetch();
	return $data;
}
function get_checkPseudo(){
	global $bdd;
	$pseudo=$_POST['pseudo'];
	$req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE pseudo =:pseudo');
	$req->bindValue(':pseudo',$pseudo, PDO::PARAM_STR);
	$req->execute();
	$pseudo_free=($req->fetchColumn()==0)?1:0;
	$req->CloseCursor();
	return $pseudo_free;
}
function get_checkMail(){
	global $bdd;
	$email = $_POST['email'];
	$req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE email =:mail');
	$req->bindValue(':mail',$email, PDO::PARAM_STR);
	$req->execute();
	$mail_free=($req->fetchColumn()==0)?1:0;
	$req->CloseCursor();
	return $mail_free;
}
function get_ProfilsInfo(){ // Informations de tout les vehicules dans un tableau
    global $bdd;
    $req = $bdd->prepare('SELECT id, privilege, dateenregistre, pseudo, prenom, nom, phone, adresse, mdp, email, avatar, phone FROM Clients ORDER BY id');
    $req->execute();
    $data = $req->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}
function get_MemberInfo(){
	global $bdd;
	$membre = isset($_GET['m']) ? (int)$_GET['m'] : 0;
	$req = $bdd->prepare('SELECT pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre FROM Clients WHERE id = :membre');
	$req->execute(array(':membre' => $membre));
	return $req->fetch(PDO::FETCH_ASSOC);
}
function get_MemberInfoId(){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;

	//On prend les infos du membre
	$req = $bdd->prepare('SELECT pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre FROM Clients WHERE id=:id');
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$data = $req->fetch();
	return $data;
}
function get_checkMail2(){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;
	//On commence donc par récupérer le mail
	$req = $bdd->prepare('SELECT email FROM Clients WHERE id =:id'); 
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$data = $req->fetch();
	return $data;
}
function get_Pseudo(){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;
	//On commence donc par récupérer le pseudo
	$req = $bdd->prepare('SELECT pseudo FROM Clients WHERE id =:id'); 
	$req->bindValue(':id',$id,PDO::PARAM_INT);
	$req->execute();
	$data = $req->fetch();
	return $data;
}
function get_checkCopyMail(){
	global $bdd;
	$email = $_POST['email'];
	//Il faut que l'adresse email n'ait jamais été utilisée
	$req = $bdd->prepare('SELECT COUNT(*) AS nbr FROM Clients WHERE email =:mail');
	$req->bindValue(':mail',$email,PDO::PARAM_STR);
	$req->execute();
	$mail_free = ($req->fetchColumn()==0)?1:0;
	$req->CloseCursor();
	return $mail_free;
}
# Inscription modele
function post_Registre(){
	global $bdd;
	$pseudo=$_POST['pseudo'];
	$pass = /*md5*/($_POST['password']);
	$email = $_POST['email'];
	$localisation = $_POST['localisation'];
	$prenom = $_POST['prenom'];
	$nom = $_POST['nom'];
	$phone = $_POST['phone'];
	$image = $_FILES['avatar'];
    // Créer le répertoire pour les images si nécessaire
    $dirPath = "./images/avatars/" . $pseudo . "/";
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0700, true);
    }
    // Vérifier si l'image est fournie et valide
    if (!empty($image['size']) && $image['error'] === UPLOAD_ERR_OK) {
        $nomimage = edit_avatar($image, $pseudo);
    } else {
        // Utiliser une image par défaut si aucune image n'est fournie
        $defaultImagePath = "./images/avatars/img_user.jpg";
        $dirPath = "./images/avatars/" . $pseudo . "/img_user.jpg";
        $nomimage = "/images/avatars/" . $pseudo . "/img_user.jpg";
        copy($defaultImagePath, $dirPath);
    }
	$req = $bdd->prepare('INSERT INTO Clients (pseudo, mdp, email, avatar, adresse, prenom, nom, phone, dateenregistre)
						VALUES (:pseudo, :pass, :email, :nomimage, :localisation, :prenom, :nom, :phone, NOW())');
	$req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
	$req->bindValue(':pass', $pass, PDO::PARAM_STR);
	$req->bindValue(':email', $email, PDO::PARAM_STR);
	$req->bindValue(':nomimage', $nomimage, PDO::PARAM_STR);
	$req->bindValue(':localisation', $localisation, PDO::PARAM_STR);
	$req->bindValue(':prenom', $prenom, PDO::PARAM_STR);
	$req->bindValue(':nom', $nom, PDO::PARAM_STR);
	$req->bindValue(':phone', $phone, PDO::PARAM_STR);
	$req->execute();
}
function post_RemoveAvatar($pseudo){
	global $bdd;
	$id=(isset($_SESSION['id_session']))?(int) $_SESSION['id_session']:0;
	// Utiliser une image par défaut si aucune image n'est fournie
	$defaultImagePath = "./images/avatars/img_user.jpg";
	$dirPath = "./images/avatars/" . $pseudo . "/img_user.jpg";
	$nomimage = "./images/avatars/" . $pseudo . "/img_user.jpg";
	copy($defaultImagePath, $dirPath);
}
function post_UpdateProfile($pseudo, $pass = null, $email = null, $localisation = null, $phone = null) {
    global $bdd;
    $id = isset($_SESSION['id_session']) ? (int)$_SESSION['id_session'] : 0;
    // Mise à jour de l'avatar
    if (isset($_FILES['avatar'])) {
        $image = $_FILES['avatar'];
        $dirPath = "./images/avatars/" . $pseudo . "/";
        // Vérifier si l'image est fournie et valide
        if (!empty($image['size']) && $image['error'] === UPLOAD_ERR_OK) {
            $nomimage = edit_avatar($image, $pseudo);
        } else {
            // Utiliser une image par défaut si aucune image n'est fournie
            $defaultImagePath = "./images/avatars/img_user.jpg";
            $dirPath = "./images/avatars/" . $pseudo . "/img_user.jpg";
            $nomimage = "./images/avatars/" . $pseudo . "/img_user.jpg";
            file_exists($nomimage) ? : copy($defaultImagePath, $nomimage);
        }
    }
    // Mise à jour des informations du membre
    if ($pass !== null || $email !== null || $localisation !== null || $phone !== null) {
        $fields = [];
        $params = [':id' => $id];
        if ($pass !== null) {
            $fields[] = 'mdp = :mdp';
            $params[':mdp'] = $pass; // Utilisez password_hash($pass, PASSWORD_DEFAULT) pour sécuriser les mots de passe
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
        if (!empty($fields)) {
            $sql = 'UPDATE Clients SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $req = $bdd->prepare($sql);
            $req->execute($params);
        }
    }
}
function edit_avatar($image, $pseudo) {
    if (isset($image)) {
        $source = $image['tmp_name'];
        $dir = "./images/avatars/" . $pseudo . "/img_user.jpg";
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
        // Sauvegarder l'image redimensionnée en JPEG
        imagejpeg($imageResized, $dir, 85); // Le troisième paramètre est la qualité de l'image JPEG (0-100)
        // Libérer la mémoire
        imagedestroy($imageResized);
        imagedestroy($imageSource);
        // Retourner le chemin relatif de l'image redimensionnée
        return $dir;
    }
}
function post_RemoveClient($id) {
    global $bdd;
    $id = $_SESSION['id_session'];
    // Récupérer le chemin de l'image à partir de la base de données
    $req = $bdd->prepare('SELECT avatar FROM Clients WHERE id= :id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $data = $req->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        // Supprimer l'image
        $imagePath = $data['avatar'];
        unlink($imagePath);
        // Supprimer le dossier
        $folderPath = dirname($imagePath);
        rmdir($folderPath);
    }
    // Supprimer le véhicule de la base de données
    $req = $bdd->prepare('DELETE FROM Clients WHERE id=:id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $req->closeCursor();
    session_destroy(); // Vous pouvez adapter cette étape en fonction de votre gestion de sessions
}
?>