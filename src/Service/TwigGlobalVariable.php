<?php
namespace App\Service;

use App\Managers\AdminManager;
use App\Managers\SocialManager;

class TwigGlobalVariable {
	public static function getSocials() {
		return (new SocialManager)->findAll();
	}

	public static function getAdmin() {
		return (new AdminManager)->findById(1);
	}

	public static function getBlog(): array {
		$admin = self::getAdmin();

		return [
			'title' => $admin->getFirstName(). " ". $admin->getLastName(). "'s blog", 
			'copyright' => $admin->getFirstName(). " ". $admin->getLastName(), 
		];
	}

	public static function getCurrentUri() {
		return $_SERVER['REQUEST_URI'];
	}
}
