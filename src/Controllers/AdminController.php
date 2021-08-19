<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\AdminLogged;
use App\Service\FormHandler;
use App\Managers\PostManager;
use App\Managers\SocialManager;
use App\Exceptions\FormException;
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
					$admin = (new FormHandler)->editAccount($_POST);

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
					(new FormHandler)->editPassword($_POST);

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
					$admin = (new FormHandler)->editAdminInfo($_POST, $_FILES);

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
			$this->render("@admin/pages/post_add.html.twig");
		});
	}

	/**
	 * @return void
	 */
	public function editPost(): void {
		(new AdminLogged)->adminLogged(function() {
			$slug = $this->params['slug'];

			$postManager = new PostManager();

			$post = $postManager->findOneBy([
				'slug' => $slug, 
			]);

			if (is_null($post)) {
				throw new RequestedEntityNotFound("Post not found");
			}

			$this->render("@admin/pages/post_edit.html.twig", [
				'post' => $post, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function deletePost(): void {
		(new AdminLogged)->adminLogged(function() {
			$slug = $this->params['slug'];

			// no render. action and after redirect
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
		(new AdminLogged)->adminLogged(function($admin) {
			$template = "@admin/pages/social_add.html.twig";

			$message = [];

			if (isset($_POST['submitButton'])) {
				try {
					$social = (new FormHandler)->editSocial($_POST);

					(new SocialManager)->create($social);
					$message = [
						'success' => "Le lien social a bien été ajouter.", 
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
		(new AdminLogged)->adminLogged(function($admin) {
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
						'success' => "Le lien social a bien été mise a jour.", 
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
}