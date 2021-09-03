<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\AdminLogged;
use App\Managers\CommentManager;
use App\Service\CommentModerationProcessHandler;

class CommentController extends Controller {
	/**
	 * Display comment list
	 * 
	 * @return void
	 */
	public function showComment(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$commentManager = new CommentManager();

			$comments = $commentManager->findBy([], [
				'created_at' => "DESC", 
			]);

			$this->render("@admin/pages/comment.html.twig", [
				'active' => "showComment", 
				'admin' => $admin, 
				'comments' => $comments, 
			]);
		});
	}

	/**
	 * Put a comment online, called by ajax request
	 * 
	 * @return void
	 */
	public function putOnline(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			(new CommentModerationProcessHandler)->updateCommentStatus($id, true);
		});
	}

	/**
	 * Put a comment offline, called by ajax request
	 * 
	 * @return void
	 */
	public function putOffline(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			(new CommentModerationProcessHandler)->updateCommentStatus($id, false);
		});
	}
}