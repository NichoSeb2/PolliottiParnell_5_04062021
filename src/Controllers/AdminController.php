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
			if (isset($_POST['submitButtonAccount'])) {
				try {
					(new FormHandler)->editAccount($_POST);

					$admin = (new AdminLogged)->refreshAdmin($admin);

					$this->render("@admin/pages/profile.html.twig", [
						'active' => "profile", 
						'admin' => $admin, 
						'accountSuccess' => true, 
					]);
				} catch (FormException $e) {
					$this->render("@admin/pages/profile.html.twig", [
						'active' => "profile", 
						'admin' => $admin, 
						'accountError' => $e->getMessage(), 
					]);
				}

				exit();
			}

			$this->render("@admin/pages/profile.html.twig", [
				'active' => "profile", 
				'admin' => $admin, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function showPost(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$postManager = new PostManager();

			$post = $postManager->findBy([], [
				'created_at' => "DESC", 
			]);

			$this->render("@admin/pages/post.html.twig", [
				'active' => "showPost", 
				'admin' => $admin, 
				'post' => $post, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function addPost(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$this->render("@admin/pages/post_add.html.twig", [
				'active' => "addPost", 
				'admin' => $admin, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function editPost(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$slug = $this->params['slug'];

			$postManager = new PostManager();

			$post = $postManager->findOneBy([
				'slug' => $slug, 
			]);

			if (is_null($post)) {
				throw new RequestedEntityNotFound("Post not found");
			}

			$this->render("@admin/pages/post_edit.html.twig", [
				'active' => "showPost", 
				'admin' => $admin, 
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
		(new AdminLogged)->adminLogged(function($admin) {
			$socialManager = new SocialManager();

			$socials = $socialManager->findAll();

			$this->render("@admin/pages/social.html.twig", [
				'active' => "showSocial", 
				'admin' => $admin, 
				'socials' => $socials, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function addSocial(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$this->render("@admin/pages/social_add.html.twig", [
				'active' => "addSocial", 
				'admin' => $admin, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function editSocial(): void {
		(new AdminLogged)->adminLogged(function($admin) {
			$id = $this->params['id'];

			$socialManager = new SocialManager();

			$social = $socialManager->findOneBy([
				'id' => $id, 
			]);

			if (is_null($social)) {
				throw new RequestedEntityNotFound("Social not found");
			}

			$this->render("@admin/pages/social_edit.html.twig", [
				'active' => "showSocial", 
				'admin' => $admin, 
				'social' => $social, 
			]);
		});
	}

	/**
	 * @return void
	 */
	public function deleteSocial(): void {
		(new AdminLogged)->adminLogged(function() {
			$id = $this->params['id'];

			// no render. action and after redirect
		});
	}
}