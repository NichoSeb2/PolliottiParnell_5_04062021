<?php
namespace App\Service;

use App\Core\Mail;
use App\Core\Twig;
use App\Model\User;
use App\Managers\AdminManager;

class SendMail {
	/**
	 * Send a contact mail to the main admin
	 * 
	 * @param string $name
	 * @param string $email
	 * @param string $subject
	 * @param string $message
	 * 
	 * @return void
	 */
	public function sendContactMail(string $name, string $email, string $subject, string $message): void {
		$mail = new Mail();

		$twig = new Twig();

		$html = $twig->render("@mail/pages/contact.html.twig", [
			'from' => $name, 
			'subject' => $subject, 
			'text' => $message, 
		]);

		$admin = (new AdminManager)->findById(1);

		$mail->send([$email, $name], [[$admin->getEmail(), $admin->getFirstName(). " ". $admin->getLastName()]], $subject, $html, "Message de contact provenant de : $name\nDisant : $message");
	}


	/**
	 * Send a verification mail
	 * 
	 * @param User $user
	 * 
	 * @return void
	 */
	public function sendVerificationMail(User $user): void {
		$mail = new Mail();

		$twig = new Twig();

		$verificationLink = "http". "://". $_SERVER['HTTP_HOST']. "/verify/". $user->getVerificationToken();

		$html = $twig->render("@mail/pages/verify.html.twig", [
			'link' => $verificationLink, 
		]);

		$mail->send([
			"no-reply@play-for-eternity.net", 
			"Parnell Polliotti's Blog"
		], [
			[$user->getEmail(), $user->getFirstName(). " ". $user->getLastName()]
		], "Vérification de votre compte", $html, "Bonjour, pour vérifier votre compte merci de suivre le lien ci-dessous :\n\n". $verificationLink);
	}

	/**
	 * Send a forgot password mail
	 * 
	 * @param User $user
	 * 
	 * @return void
	 */
	public function sendForgotPasswordMail(User $user): void {
		$mail = new Mail();

		$twig = new Twig();

		$forgotPasswordLink = "http". "://". $_SERVER['HTTP_HOST']. "/forget/". $user->getForgotPasswordToken();

		$html = $twig->render("@mail/pages/forget.html.twig", [
			'link' => $forgotPasswordLink, 
		]);

		$mail->send([
			"no-reply@play-for-eternity.net", 
			"Parnell Polliotti's Blog"
		], [
			[$user->getEmail(), $user->getFirstName(). " ". $user->getLastName()]
		], "Mot de passe oublié", $html, "Bonjour, pour changer votre mot de passe merci de suivre le lien ci-dessous :\n\n". $forgotPasswordLink);
	}
}
