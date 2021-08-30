<?php
namespace App\Service;

use App\Model\Post;
use App\Model\Social;
use App\Model\Comment;
use Cocur\Slugify\Slugify;
use App\Managers\PostManager;
use App\Managers\UserManager;
use App\Managers\AdminManager;
use App\Managers\CommentManager;
use App\Exceptions\FileException;
use App\Exceptions\FormException;
use App\Exceptions\FileServerException;
use App\Exceptions\FileTooBigException;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\RequestedEntityNotFound;

class FormHandler {
	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function contact(array $data): void {
		extract($data);

		if (isset($name, $email, $message, $subject)) {
			(new SendMail)->sendContactMail($name, $email, $subject, $message);
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * @param array $file
	 * @param Post|null $post
	 * 
	 * @return Post
	 */
	public function editPost(array $data, array $file, Post $post = null) : Post {
		extract($data);

		if (is_null($post)) {
			$post = new Post();
		}

		$post->setTitle($title);

		if (!$post->issetSlug()) {
			$slug = (new Slugify())->slugify($post->getTitle());

			$slugDuplicator = 0;

			while (!is_null((new PostManager)->findOneBy(['slug' => $slug]))) {
				$slugDuplicator++;

				$slug = (new Slugify())->slugify($post->getTitle(). " ". $slugDuplicator);
			}

			$post->setSlug($slug);
		}

		$post->setContent($content);
		$post->setAltCoverageImage($coverImageAlt);

		if (!isset($post->adminId)) {
			$post->setAdminId((new AdminManager)->findConnected()->getId());
		}

		if ($file['error'] != 4) {
			try {
				$targetFile = (new FileUploader)->upload($file, "uploads/post/", $post->getSlug(), FileUploader::IMAGE_TYPE);

				$post->setUrlCoverageImage($targetFile);
			} catch (FileTooBigException $e) {
				throw new FormException($e->getMessage());
			} catch (FileServerException $e) {
				throw new FileServerException($e->getMessage());
			} catch (FileException $e) {
				throw new FormException(FormReturnMessage::ERROR_WHILE_UPLOADING_FILE_RETRY);
			}
		}

		return $post;
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function addComment(array $data, string $slug): void {
		extract($data);

		$post = (new PostManager)->findOneBy([
			'slug' => $slug, 
		]);

		if (!is_null($post)) {
			$user = (new UserManager)->findConnected();

			if (!is_null($user)) {
				if (isset($content)) {
					$comment = new Comment([
						'userId' => $user->getId(), 
						'postId' => $post->getId(), 
						'content' => $content, 
					]);

					$commentManager = new CommentManager();

					$commentManager->create($comment);
				} else {
					throw new FormException(FormReturnMessage::MISSING_FIELD);
				}
			} else {
				throw new AccessDeniedException();
			}
		} else {
			throw new RequestedEntityNotFound();
		}
	}

	/**
	 * @param array $data
	 * @param Social|null $social
	 * 
	 * @return Social
	 */
	function editSocial(array $data, Social $social = null): Social {
		extract($data);

		if (is_null($social)) {
			$social = new Social();
		}

		if (isset($name, $url, $icon)) {
			$social->setName($name);
			$social->setUrl($url);
			$social->setIcon($icon);

			return $social;
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}
}
