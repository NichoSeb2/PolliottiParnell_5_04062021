<?php
namespace App\Managers;

use App\Model\Post;
use App\Core\Entity;
use App\Core\Manager;
use App\Model\Comment;

class PostManager extends Manager {
	public function __construct() {
		parent::__construct();

		$this->excludeGetterForInsert = [
			'getId', 
			'getCreatedAt', 
			'getUpdatedAt', 
			'getComments', 
		];

		$this->excludeGetterForUpdate = [
			'getId', 
			'getCreatedAt', 
			'getComments', 
		];
	}

	/**
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return Post|null
	 */
	public function findOneByWithComment(array $where = [], array $orderBy = [], int $limit = null, int $offset = null) {
		$sql = "SELECT p.*, ". $this->_computeField([
			'id' => "temp_post_id", 
			'created_at' => "temp_post_created_at", 
			'updated_at' => "temp_post_updated_at", 
			'content' => "temp_post_content", 
		], "p"). ", c.*, ". $this->_computeField([
			'id' => "temp_comment_id", 
			'created_at' => "temp_comment_created_at", 
			'updated_at' => "temp_comment_updated_at", 
			'content' => "temp_comment_content", 
		], "c"). " FROM post AS p LEFT JOIN comment AS c ON p.id = c.post_id AND c.status = true";

		$result = $this->_appendIfCorrect($sql, $where, $orderBy, $limit, $offset);

		$request = $this->pdo->prepare($result[0]);
		$request->execute($result[1]);
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

				if (!is_null($tempCommentData['temp_comment_id']) && ((bool) $tempCommentData['status'])) {
					$tempCommentData['id'] = $tempCommentData['temp_comment_id'];
					$tempCommentData['created_at'] = $tempCommentData['temp_comment_created_at'];
					$tempCommentData['updated_at'] = $tempCommentData['temp_comment_updated_at'];
					$tempCommentData['content'] = $tempCommentData['temp_comment_content'];

					$comment = new Comment($tempCommentData);

					$post->addComment($comment);
				}
			}

			return $post;
		}

		return null;
	}

	/**
	 * @param Post $post
	 * 
	 * @return void
	 */
	public function delete(Entity $post): void {
		$sql = "DELETE p, c FROM post p LEFT JOIN comment c ON p.id = c.post_id WHERE p.slug = :slug";

		$request = $this->pdo->prepare($sql);
		$request->execute([
			'slug' => $post->getSlug(), 
		]);
	}
}
