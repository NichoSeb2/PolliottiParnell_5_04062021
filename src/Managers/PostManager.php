<?php
namespace App\Managers;

use App\Core\Manager;
use App\Model\Comment;
use App\Model\Post;

class PostManager extends Manager {
	/**
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return Post|null
	 */
	public function findOneByWithComment(array $where = [], array $orderBy = [], int $limit = null, int $offset = null) {
		$sql = "SELECT *, ". $this->_computeField([
			'id' => "temp_post_id", 
			'created_at' => "temp_post_created_at", 
			'updated_at' => "temp_post_updated_at", 
			'content' => "temp_post_content", 
		], "p"). ", ". $this->_computeField([
			'id' => "temp_comment_id", 
			'created_at' => "temp_comment_created_at", 
			'updated_at' => "temp_comment_updated_at", 
			'content' => "temp_comment_content", 
		], "c"). " FROM post AS p LEFT JOIN comment AS c ON p.id = c.post_id";

		$sql = $this->_appendIfCorrect($sql, $where, $orderBy, $limit, $offset);

		$sql .= " OR c.status IS NULL";

		$request = $this->pdo->query($sql);
		$results = $request->fetchAll();

		if (!empty($results)) {
			$tempPostData = $results[0];

			$tempPostData['id'] = $tempPostData['temp_post_id'];
			$tempPostData['created_at'] = $tempPostData['temp_post_created_at'];
			$tempPostData['updated_at'] = $tempPostData['temp_post_updated_at'];
			$tempPostData['content'] = $tempPostData['temp_post_content'];

			$post = new Post($tempPostData);

			foreach ($results as $result) {
				$tempCommentData = $result;

				if (!is_null($tempCommentData['temp_comment_id'])) {
					$tempCommentData['id'] = $tempCommentData['temp_comment_id'];
					$tempCommentData['created_at'] = $tempCommentData['temp_comment_created_at'];
					$tempCommentData['updated_at'] = $tempCommentData['temp_comment_updated_at'];
					$tempCommentData['content'] = $tempCommentData['temp_comment_content'];

					$comment = new Comment($tempCommentData);

					$post->addComment($comment);
				}
			}

			return $post;
		} else {
			return null;
		}
	}
}
