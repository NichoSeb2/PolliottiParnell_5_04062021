<?php
namespace App\Service;

use App\Model\User;
use App\Service\AdminLogged;

class UserLogged {
	/**
	 * Redirect the client after connection based on permission
	 * 
	 * @param User $user
	 * 
	 * @return void
	 */
	public function redirectUser(User $user): void {
		$_SESSION['id'] = $user->getId();

		if ((new AdminLogged)->isAdmin($user)) {
			header("Location: /admin");
		} else {
			header("Location: /blog");
		}
	}
}
