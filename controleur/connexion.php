<?php
// Vérification des identifiants de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $pseudoCo = $_POST['pseudo_connect'] ?? '';
    $passwordCo = $_POST['password_connect'] ?? '';

    // Vérification des identifiants en utilisant la méthode checkCredentials()
    $connexionModel = new Connexion();

    // Vérifier si l'utilisateur est bloqué
    if ($connexionModel->isUserBlocked($pseudoCo)) {
        // Afficher un message d'erreur indiquant que l'utilisateur est bloqué
        require './vue/connexion.html';
        displayBlockedMessage();
        exit(); // Arrêter l'exécution du script
    }

    $userData = $connexionModel->check_Password($pseudoCo);
    if ($userData && $connexionModel->checkCredentials($pseudoCo, $passwordCo)) {
        // Les identifiants sont corrects, connecter l'utilisateur
        connectUser($userData);
    } else {
        // Enregistrer une tentative de connexion infructueuse
        $connexionModel->recordFailedLoginAttempt($pseudoCo);
        // Afficher un message d'erreur en cas de connexion échouée
        require './vue/connexion.html';
        displayErrorMessage();
    }
}
// Fonction pour connecter l'utilisateur
function connectUser($userData) {
    $_SESSION['pseudo_session'] = $userData['pseudo'];
    $_SESSION['privilege_session'] = $userData['privilege'];
    $_SESSION['id_session'] = $userData['id'];
    // Redirection vers la page principale
    header('Location: index.php');
    exit();
}
// Fonction pour afficher un message d'erreur
function displayErrorMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var errorMessage = `
                <div class="alert alert-danger">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Echec de la connexion</h3>
                            </div>
                            <div class="card-body">
                                <p>La combinaison du pseudo et mot de passe saisie n\'est pas correcte.</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = errorMessage;
        });
    </script>';
}
// Fonction pour afficher un message de bloquage
function displayBlockedMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var blockedMessage = `
                <div class="alert alert-danger">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Connexion bloquée</h3>
                            </div>
                            <div class="card-body">
                                <p>Votre compte a été temporairement bloqué en raison de trop de tentatives de connexion infructueuses. Veuillez réessayer plus tard.</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = blockedMessage;
        });
    </script>';
}
?>
