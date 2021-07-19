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
}
