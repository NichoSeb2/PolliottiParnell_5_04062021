<?php
namespace App\Service;

use App\Model\Admin;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class TwigGlobalVariable {
	/**
	 * @return array
	 */
	public static function getSocials(): array {
		return (new SocialManager)->findAll();
	}

	/**
	 * @return Admin|null
	 */
	public static function getAdmin() {
		return (new AdminManager)->findById(1);
	}

	/**
	 * @return array
	 */
	public static function getBlog(): array {
		$admin = self::getAdmin();

		if (is_null($admin)) {
			return [
				'title' => "Setup", 
				'copyright' => "Parnell Polliotti", 
			];
		} else {
			return [
				'title' => $admin->getFirstName(). " ". $admin->getLastName(). "'s blog", 
				'copyright' => $admin->getFirstName(). " ". $admin->getLastName(), 
			];
		}
	}

	/**
	 * @return string
	 */
	public static function getCurrentUri(): string {
		return $_SERVER['REQUEST_URI'];
	}
}
