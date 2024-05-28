<?php
require_once './modele/profil.php';
// Fonction pour vérifier si le pseudo est disponible
function isPseudoAvailable($pseudo) {
    $pseudo_free = get_checkPseudo();
    if (!$pseudo_free) {
        return false; // Pseudo non disponible
    }
    return true; // Pseudo disponible
}
// Fonction pour vérifier si l'email est disponible
function isEmailAvailable($email) {
    $mail_free = get_checkMail();
    if (!$mail_free) {
        return false; // Email non disponible
    }
    return true; // Email disponible
}
// Fonction pour vérifier l'extension de l'avatar
function isValidAvatarExtension($filename, $extensions_valides) {
    $extension_upload = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension_upload, $extensions_valides);
}
// Fonction pour inscrire l'utilisateur
function inscriptionUser($userData) {
    global $bdd;
    // Enregistrement de l'utilisateur
    post_Registre();
    // Définition des variables de session
    $_SESSION['pseudo_session'] = $userData['pseudo'];
    $_SESSION['id_session'] = $bdd->lastInsertId(); // Assurez-vous d'avoir accès à $bdd ici
    $_SESSION['privilege_session'] = 2;
}
// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    // On récupère les variables
    $extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
    $pseudo = $_POST['pseudo'];
    $email = $_POST['email'];
    $localisation = $_POST['localisation'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $pass = md5($_POST['password']);
    $confirm = md5($_POST['confirm']);
    // Validation du pseudo
    if (!isPseudoAvailable($pseudo)) {
        $errors[] = "Votre pseudo est déjà utilisé par un membre.";
    }
    // Validation du mot de passe
    if ($pass !== $confirm || empty($confirm) || empty($pass)) {
        $errors[] = "Votre mot de passe et la confirmation sont différents, ou sont vides.";
    }
    // Validation de l'email
    if (!isEmailAvailable($email)) {
        $errors[] = "Votre adresse email est déjà utilisée par un membre.";
    }
    // Validation de l'avatar s'il est uploadé
    if (!empty($_FILES['avatar']['size'])) {
        if (!isValidAvatarExtension($_FILES['avatar']['name'], $extensions_valides)) {
            $errors[] = "Extension de l'avatar incorrecte.";
        }
    }
    if (empty($errors)) {
        $userData = [
            'pseudo' => $pseudo,
            'email' => $email,
            // Autres champs du formulaire
        ];
require_once './vue/inscription.html';
            inscriptionUser($userData);
            displayInscriptionSuccessMessage();
            exit;
    }
}
// Affichage du formulaire d'inscription avec les éventuelles erreurs
require_once './vue/inscription.html';
if (!empty($errors)) {
    displayInscriptionErrorMessage($errors);
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