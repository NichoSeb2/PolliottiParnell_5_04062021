<?php
namespace App\Managers;

use App\Model\Post;
use App\Model\User;
use App\Core\Entity;
use App\Model\Admin;
use App\Core\Manager;
use App\Model\Comment;
use App\Service\StringOperation;

class PostManager extends Manager {
	/**
	 * Filter temporary data based on the given prefix
	 * 
	 * @param string $prefix
	 * @param array $tempData
	 * 
	 * @return array
	 */
	private function _extractFromTempData(string $prefix, array $tempData): array {
		$result = [];

		foreach ($tempData as $key => $value) {
			if ((new StringOperation)->str_starts_with($key, $prefix)) {
				$result[str_replace($prefix. "_", "", $key)] = $value;
			}
		}

		return $result;
	}

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
	 * Return a post with associated comments
	 * 
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return Post|null
	 */
	public function findOneByWithComment(array $where = [], array $orderBy = [], int $limit = null, int $offset = null) {
		$sql = "SELECT ". $this->_computeField([
			'id' => "post_id", 
			'created_at' => "post_created_at", 
			'updated_at' => "post_updated_at", 
			'slug' => "post_slug", 
			'title' => "post_title", 
			'content' => "post_content", 
			'url_coverage_image' => "post_url_coverage_image", 
			'alt_coverage_image' => "post_alt_coverage_image", 
		], "p"). ", ". $this->_computeField([
			'id' => "comment_id", 
			'post_id' => "comment_post_id", 
			'created_at' => "comment_created_at", 
			'updated_at' => "comment_updated_at", 
			'content' => "comment_content", 
		], "c"). ", ". $this->_computeField([
			'id' => "admin_id", 
			'first_name' => "admin_first_name", 
			'last_name' => "admin_last_name", 
		], "ua"). ", ". $this->_computeField([
			'id' => "user_id", 
			'first_name' => "user_first_name", 
			'last_name' => "user_last_name", 
		], "u"). " FROM post AS p LEFT JOIN comment AS c ON p.id = c.post_id AND c.status = true JOIN admin AS a ON a.id = p.admin_id JOIN user AS ua ON ua.id = a.user_id LEFT JOIN user AS u on u.id = c.user_id";

		$result = $this->_appendIfCorrect($sql, $where, $orderBy, $limit, $offset);

		$request = $this->pdo->prepare($result[0]);
		$request->execute($result[1]);
		$results = $request->fetchAll();

		if (!empty($results)) {
			$post = new Post($this->_extractFromTempData("post", $results[0]));
			$post->setAdmin(new Admin($this->_extractFromTempData("admin", $results[0])));

			foreach ($results as $result) {
				if (!is_null($result['comment_id'])) {
					$comment = new Comment($this->_extractFromTempData("comment", $result));
					$comment->setUser(new User($this->_extractFromTempData("user", $result)));

					$post->addComment($comment);
				}
			}

			return $post;
		}

		return null;
	}

	/**
	 * Delete a post and associated comments
	 * 
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
