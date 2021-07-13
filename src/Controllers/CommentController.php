<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\AdminManager;
use App\Managers\CommentManager;

class CommentController extends Controller {
	//! temporary
	private int $loggedUserId = 1;

	/**
	 * @return void
	 */
	public function showComment(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$commentManager = new CommentManager();

		$comments = $commentManager->findBy([], [
			'created_at' => "DESC", 
		]);

		$this->render("@admin/pages/comment.html.twig", [
			'active' => "showComment", 
			'admin' => $admin, 
			'comments' => $comments, 
		]);
	}

	/**
	 * @return void
	 */
	public function putOnline(): void {
		$id = $this->params['id'];

		// only action since called by ajax
	}

	/**
	 * @return void
	 */
	public function putOffline(): void {
		$id = $this->params['id'];

		// only action since called by ajax
	}
}