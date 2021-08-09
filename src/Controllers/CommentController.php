<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\AdminLogged;
use App\Managers\CommentManager;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\RequestedEntityNotFound;
use App\Managers\PostManager;
use App\Managers\UserManager;
use App\Model\Comment;

class CommentController extends Controller {
	/**
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
	 * @return void
	 */
	public function addComment(): void {
		$slug = $this->params['slug'];

		if (isset($_POST['submitButton'])) {
			extract($_POST);

			$post = (new PostManager)->findOneBy([
				'slug' => $slug, 
			]);

			if (!is_null($post)) {
				$user = (new UserManager)->findConnected();

				if (!is_null($user)) {
					$comment = new Comment([
						'userId' => $user->getId(), 
						'postId' => $post->getId(), 
						'content' => $content, 
					]);

					$commentManager = new CommentManager();

					$commentManager->create($comment);

					header("Location: /blog/". $post->getSlug());
				} else {
					throw new AccessDeniedException();
				}
			} else {
				throw new RequestedEntityNotFound();
			}
		} else {
			throw new AccessDeniedException();
		}
	}

	/**
	 * @return void
	 */
	public function putOnline(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			$commentManager = new CommentManager();

			$comment = $commentManager->findOneBy([
				'id' => $id, 
			]);

			if (!is_null($comment)) {
				$comment->setStatus(true);

				$commentManager->update($comment);
			}
		});
	}

	/**
	 * @return void
	 */
	public function putOffline(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			$commentManager = new CommentManager();

			$comment = $commentManager->findOneBy([
				'id' => $id, 
			]);

			if (!is_null($comment)) {
				$comment->setStatus(false);

				$commentManager->update($comment);
			}
		});
	}
}