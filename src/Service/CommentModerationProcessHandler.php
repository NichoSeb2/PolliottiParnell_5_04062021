<?php
namespace App\Service;

use App\Managers\CommentManager;

class CommentModerationProcessHandler {
	/**
	 * Update a comment status in database
	 * 
	 * @param int $id The comment id
	 * @param bool $status The new status
	 * 
	 * @return void
	 */
	public function updateCommentStatus(int $id, bool $status): void {
		$commentManager = new CommentManager();

		$comment = $commentManager->findOneBy([
			'id' => $id, 
		]);

		if (!is_null($comment)) {
			$comment->setStatus($status);

			$commentManager->update($comment);
		}
	}
}
