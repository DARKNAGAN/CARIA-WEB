<?php
// NOT WORKING - Activation email server necessaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Destinataire du mail
    $to = "contact@cunatbrule.fr";
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Composer le contenu de l'email
    $body = "Nom: $name\nEmail: $email\nSujet: $subject\n\nMessage:\n$message";

    // Envoyer l'email
    if (mail($to, $subject, $body, $headers)) {
        echo "Votre message a été envoyé avec succès.";
    } else {
        echo "Une erreur est survenue lors de l'envoi de votre message.";
    }
}
?>