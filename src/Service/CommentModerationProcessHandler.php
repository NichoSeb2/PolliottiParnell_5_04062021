<?php
namespace App\Service;

use App\Managers\CommentManager;

class CommentModerationProcessHandler {
	public function updateCommentStatus(int $id, bool $status) {
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
