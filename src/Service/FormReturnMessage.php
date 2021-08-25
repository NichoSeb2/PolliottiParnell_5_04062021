<?php
namespace App\Service;

class FormReturnMessage {
	const MISSING_FIELD = "Un champ n'est pas correctement rempli.";

	const WRONG_PASSWORD = "Mot de passe incorrect.";

	const OLD_PASSWORD_INCORRECT = "Ancien mot de passe incorrect.";

	const PASSWORD_CPASSWORD_NOT_MATCH = "Le mot de passe et la confirmation du mot de passe doivent être identiques.";

	const NO_ACCOUNT_FOR_EMAIL = "Aucun compte n'existe avec cette adresse email.";

	const NO_ACCOUNT_FOR_VERIFICATION_TOKEN = "Aucun compte n'est associé à ce token de vérification, essayez de demander un renvoi.";

	const NO_ACCOUNT_FOR_FORGOT_PASSWORD_TOKEN = "Aucun compte n'est associé à ce token, essayez d'en demander un nouveau.";

	const VERIFICATION_TOKEN_ALREADY_USED = "Ce token de vérification a déjà été utilisé, votre compte est déjà vérifié.";

	const ACCOUNT_ALREADY_EXIST = "Un compte existe déjà avec cette adresse email.";

	const ACCOUNT_ALREADY_VERIFIED = "Votre compte est déjà vérifié.";

	const ACCOUNT_SUCCESSFULLY_VERIFIED = "Votre compte a bien été vérifié.";

	const COMMENT_SUCCESSFULLY_SENT = "Votre commentaire a été enregistré, il sera visible dès qu'un administrateur l'aura validé.";

	const ACCOUNT_NOT_VERIFIED = "Votre compte n'est pas vérifié, vous ne pouvez donc pas vous connecter.";

	const VERIFICATION_MAIL_RESEND = "Le mail de verification a bien été renvoyé.";

	const MESSAGE_SUCCESSFULLY_SEND = "Votre message a bien été envoyé, une réponse vous sera transmise au plus vite.";

	const FORGOT_PASSWORD_MAIL_SEND = "Le mail relatif à votre mot de passe oublié a bien été envoyé.";

	const PASSWORD_SUCCESSFULLY_CHANGED = "Votre mot de passe a bien été mis à jour.";

	const ERROR_WHILE_UPLOADING_FILE_RETRY = "Erreur pendant le téléversement du fichier, merci de réesayer.";
}
