<?php
namespace App\Service;

class FormReturnMessage {
	const MISSING_FIELD = "Un champ n'est pas correctement remplie.";

	const WRONG_PASSWORD = "Mot de passe incorrect.";

	const PASSWORD_CPASSWORD_NOT_MATCH = "Le mot de passe et la confirmation du mot de passe doivent être identique.";

	const NO_ACCOUNT_FOR_EMAIL = "Aucun compte n'existe avec cette adresse email.";

	const NO_ACCOUNT_FOR_VERIFICATION_TOKEN = "Aucun compte n'est associé a ce token de vérification, essayer de demander un renvoie.";

	const VERIFICATION_TOKEN_ALREADY_USED = "Ce token de vérification a déjà été utiliser, votre compte est déjà vérifier.";

	const ACCOUNT_ALREADY_EXIST = "Un compte existe déjà avec cette adresse email.";

	const ACCOUNT_ALREADY_VERIFIED = "Votre compte est déjà vérifier.";

	const ACCOUNT_SUCCESSFULLY_VERIFIED = "Votre compte a bien été vérifier.";

	const VERIFICATION_MAIL_RESEND = "Le mail de verification a bien été renvoyer.";

	const MESSAGE_SUCCESSFULLY_SEND = "Votre message a bien été envoyé, une réponse vous sera transmise au plus vite.";
}
