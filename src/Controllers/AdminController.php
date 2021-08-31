<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\AdminLogged;
use App\Service\FormHandler;
use App\Managers\PostManager;
use App\Managers\SocialManager;
use App\Exceptions\FormException;
use App\Service\FormReturnMessage;
use App\Service\RegisterProcessHandler;
use App\Service\EditAccountProcessHandler;
use App\Exceptions\RequestedEntityNotFound;

class AdminController extends Controller {
	/**
	 * @return void
	 */
	public function showProfile(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$template = "@admin/pages/profile.html.twig";

			$message = [];

			if (isset($_POST['submitButtonAccount'])) {
				try {
					$admin = (new EditAccountProcessHandler)->editAccount($_POST);

					$message = [
						'accountSuccess' => true, 
					];
				} catch (FormException $e) {
					$message = [
						'accountError' => $e->getMessage(), 
					];
				}
			}

			if (isset($_POST['submitButtonPassword'])) {
				try {
					(new EditAccountProcessHandler)->editPassword($_POST);

					$message = [
						'passwordSuccess' => true, 
					];
				} catch (FormException $e) {
					$message = [
						'passwordError' => $e->getMessage(), 
					];
				}
			}

			if (isset($_POST['submitButtonAdminInfo'])) {
				try {
					$admin = (new EditAccountProcessHandler)->editAdminInfo($_POST, $_FILES);

					$message = [
						'adminInfoSuccess' => true, 
					];
				} catch (FormException $e) {
					$message = [
						'adminInfoError' => $e->getMessage(), 
					];
				}
			}

			$this->render($template, [
				'admin' => $admin, 
				'message' => $message, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function showPost(): void {
		(new AdminLogged)->adminLogged(function() {
			$postManager = new PostManager();

			$post = $postManager->findBy([], [
				'created_at' => "DESC", 
			]);

			$this->render("@admin/pages/post.html.twig", [
				'post' => $post, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function addPost(): void {
		(new AdminLogged)->adminLogged(function() {
			$message = [];
			$form = [];

			if (isset($_POST['submitButton'])) {
				if ($_FILES['coverImageFile']['error'] != 4) {
					try {
						$post = (new FormHandler)->editPost($_POST, $_FILES['coverImageFile']);

						(new PostManager)->create($post);

						$message = [
							'success' => "L'article a bien été créé.", 
						];
					} catch (FormException $e) {
						extract($_POST);

						$form = [
							'title' => $title, 
							'content' => $content, 
							'coverImageAlt' => $coverImageAlt, 
						];

						$message = [
							'error' => $e->getMessage(), 
						];
					}
				} else {
					extract($_POST);

					$form = [
						'title' => $title, 
						'content' => $content, 
						'coverImageAlt' => $coverImageAlt, 
					];

					$message = [
						'error' => FormReturnMessage::MISSING_FIELD, 
					];
				}
			}

			$this->render("@admin/pages/post_add.html.twig", [
				'message' => $message, 
				'form' => $form, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function editPost(): void {
		(new AdminLogged)->adminLogged(function() {
			$message = [];
			$form = [];

			$slug = $this->params['slug'];

			$postManager = new PostManager();

			$post = $postManager->findOneBy([
				'slug' => $slug, 
			]);

			if (is_null($post)) {
				throw new RequestedEntityNotFound("Post not found");
			}

			if (isset($_POST['submitButton'])) {
				try {
					$post = (new FormHandler)->editPost($_POST, $_FILES['coverImageFile'], $post);

					$postManager->update($post);

					$message = [
						'success' => "L'article a bien été mis à jour.", 
					];
				} catch (FormException $e) {
					extract($_POST);

					$form = [
						'title' => $title, 
						'content' => $content, 
						'coverImageAlt' => $coverImageAlt, 
					];

					$message = [
						'error' => $e->getMessage(), 
					];
				}
			}

			$this->render("@admin/pages/post_edit.html.twig", [
				'post' => $post, 
				'message' => $message, 
				'form' => $form, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function deletePost(): void {
		(new AdminLogged)->adminLogged(function() {
			$slug = $this->params['slug'];

			$postManager = new PostManager();

			$post = $postManager->findOneBy([
				'slug' => $slug, 
			]);

			if (!is_null($post)) {
				$postManager->delete($post);
			}

			header("Location: /admin/blog");
		});
	}

	/**
	 * @return void
	 */
	public function showSocial(): void {
		(new AdminLogged)->adminLogged(function() {
			$this->render("@admin/pages/social.html.twig");
		});
	}

	/**
	 * @return void
	 */
	public function addSocial(): void {
		(new AdminLogged)->adminLogged(function() {
			$template = "@admin/pages/social_add.html.twig";

			$message = [];

			if (isset($_POST['submitButton'])) {
				try {
					$social = (new FormHandler)->editSocial($_POST);

					(new SocialManager)->create($social);
					$message = [
						'success' => "Le lien social a bien été ajouté.", 
					];
				} catch (FormException $e) {
					$message = [
						'error' => $e->getMessage(), 
					];
				}
			}

			$this->render($template, [
				'message' => $message, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function editSocial(): void {
		(new AdminLogged)->adminLogged(function() {
			$template = "@admin/pages/social_edit.html.twig";

			$message = [];

			$id = $this->params['id'];

			$socialManager = new SocialManager();

			$social = $socialManager->findOneBy([
				'id' => $id, 
			]);

			if (is_null($social)) {
				throw new RequestedEntityNotFound("Social not found");
			}

			if (isset($_POST['submitButton'])) {
				try {
					$social = (new FormHandler)->editSocial($_POST, $social);

					(new SocialManager)->update($social);

					$message = [
						'success' => "Le lien social a bien été mis à jour.", 
					];
				} catch (FormException $e) {
					$message = [
						'error' => $e->getMessage(), 
					];
				}
			}

			$this->render($template, [
				'social' => $social, 
				'message' => $message, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function deleteSocial(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			$socialManager = new SocialManager();

			$social = $socialManager->findOneBy([
				'id' => $id, 
			]);

			if (!is_null($social)) {
				$socialManager->delete($social);
			}

			header("Location: /admin/social");
		});
	}

	/**
	 * @return void
	 */
	public function showSetup(): void {
		$message = [];
		$form = [];

		if (isset($_POST['submitButton'])) {
			try {
				(new RegisterProcessHandler)->setup($_POST, $_FILES);

				$message = [
					'success' => "Le site a été initialisé, un mail de vérification de votre compte vous a été envoyé. Vous pouvez quitter cette page.", 
				];
			} catch (FormException $e) {
				extract($_POST);

				$message = [
					'error' => $e->getMessage(), 
				];
				$form = [
					'firstName' => $firstName, 
					'lastName' => $lastName, 
					'email' => $email, 
					'catchPhrase' => $catchPhrase, 
					'pictureAlt' => $pictureAlt, 
				];
			}
		}

		$this->render("@client/pages/setup.html.twig", [
			'message' => $message, 
			'form' => $form, 
		]);
	}
}