<?php
namespace App\Controllers;

use App\Core\Controller;

class CommentController extends Controller {
	public function putOnline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/put_online.html.twig", [
			'id' => $id
		]);
	}

	public function putOffline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/put_offline.html.twig", [
			'id' => $id
		]);
	}
}