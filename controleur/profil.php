<?php
require_once './modele/profil.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $id = (int)$_SESSION['id_session'];
    post_RemoveClient($id);
    header("Location: index.php"); // Redirigez vers la page appropriée après la suppression
    displayDeleteProfilSuccessMessage();
    exit();
}
$vehicules = get_ProfilsInfo();
// On récupère la valeur de nos variables passées par URL
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'consulter';
$membre = isset($_GET['m']) ? (int)$_GET['m'] : '';

switch($action){
    case "consulter":
        // On affiche les infos sur le membre
        $data = get_MemberInfo();
        require './vue/profile_view.html';
        break;
    case "modifier":
        handleProfileModification();
        break;
    default:
        echo '<p>Cette action est impossible</p>';
}
function handleProfileModification(){
    if (empty($_POST['sent'])) {
        // On commence par s'assurer que le membre est connecté
        // if ($id == 0) erreur(ERR_IS_NOT_CO);
        // Les infos du membre
        $data = get_MemberInfoId();
        require './vue/edit_profile_view.html';
    } else {
        $i = 0;
        $pass = /*md5*/($_POST['password']);
        $confirm = /*md5*/($_POST['confirm']);
        $email = $_POST['email'];
        $localisation = $_POST['localisation'];
        $phone = $_POST['phone'];
        $pseudo = $_SESSION['pseudo_session'];
        $extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
        // Vérification des champs du formulaire
        if ($pass != $confirm || empty($confirm) || empty($pass)) {
            $mdp_erreur = "Votre mot de passe et la confirmation sont différents ou sont vides";
            $i++;
        }
        if (!empty($_FILES['avatar']['size'])) {
            $extension_upload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
            if (!in_array($extension_upload, $extensions_valides)) {
                $i++;
                $avatar_erreur3 = "Extension de l'avatar incorrecte";
            }
        }
        if ($i == 0) {
            if (isset($_POST['delete'])) {
                post_RemoveAvatar($pseudo);
            }
            // On modifie la table
            post_UpdateProfile($pseudo, $pass, $email, $localisation, $phone);
            $data = get_MemberInfoId(); // Récupérer les informations mises à jour
            require_once './vue/edit_profile_view.html';
            displayModificationProfilSuccessMessage();
            exit;
        } else {
		$data = get_MemberInfoId(); // Récupérer les informations mises à jour
		require_once './vue/edit_profile_view.html';
		displayModificationProfilErrorMessage($i, $avatar_erreur3 ?? null, $mdp_erreur ?? null);
        }
    }
}
// Fonction pour afficher un message de succès
function displayModificationProfilSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
				<div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
								<h2>Modification de profil terminée</h2>
							</div>
							<div class="card-body">
								<h5>Votre profil a été modifié avec succès !</h5>
							</div>
						</div>
					</section>
				</div>
            `;
            messageDiv.innerHTML = successMessage;
        });
    </script>';
}
function displayModificationProfilErrorMessage($i, $avatar_erreur3 = null, $mdp_erreur = null) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var errorMessage = `
				<div class="alert alert-danger">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
								<h2>Modification de profil interrompue</h2>
							</div>
							<div class="card-body">
								<h5>Nous avons rencontré ' . $i . ' erreur(s) lors de la modification de votre profil :</h5><br>
								<ul style="list-style-type:none;">';
									if (isset($avatar_erreur3)) {
										echo '<li>' . $avatar_erreur3 . '</li>';
									}
									if (isset($mdp_erreur)) {
										echo '<li>' . $mdp_erreur . '</li>';
									}
		echo '      			</ul>
							</div>
						</div>
					</section>
				</div>
            `;
            messageDiv.innerHTML = errorMessage;
        });
    </script>';
}
function displayDeleteProfilSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var errorMessage = `
				<div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h2>Suppression de compte confirmée</h2>
							</div>
							<div class="card-body">
                                <p>Votre compte a été supprimé avec succès. Nous vous remercions pour votre utilisation de notre service.</p>
							</div>
						</div>
					</section>
				</div>
            `;
            messageDiv.innerHTML = errorMessage;
        });
    </script>';
}
?>