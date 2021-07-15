<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\PostManager;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class AdminController extends Controller {
	//! temporary
	private int $loggedUserId = 1;

	/**
	 * @return void
	 */
	public function showProfile(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$this->render("@admin/pages/profile.html.twig", [
			'active' => "profile", 
			'admin' => $admin, 
		]);
	}

	/**
	 * @return void
	 */
	public function showPost(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$postManager = new PostManager();

		$post = $postManager->findBy([], [
			'created_at' => "DESC", 
		]);

		$this->render("@admin/pages/post.html.twig", [
			'active' => "showPost", 
			'admin' => $admin, 
			'post' => $post, 
		]);
	}

	/**
	 * @return void
	 */
	public function addPost(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$this->render("@admin/pages/post_add.html.twig", [
			'active' => "addPost", 
			'admin' => $admin, 
		]);
	}

	/**
	 * @return void
	 */
	public function editPost(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$slug = $this->params['slug'];

		$postManager = new PostManager();

		$post = $postManager->findOneBy([
			'slug' => $slug, 
		]);

		if (is_null($post)) {
			$controller = new ErrorController("show404");

			$controller->execute();

			return;
		}

		$this->render("@admin/pages/post_edit.html.twig", [
			'active' => "editPost", 
			'admin' => $admin, 
			'post' => $post, 
		]);
	}

	/**
	 * @return void
	 */
	public function deletePost(): void {
		$slug = $this->params['slug'];

		// no render. action and after redirect
	}

	/**
	 * @return void
	 */
	public function showSocial(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@admin/pages/social.html.twig", [
			'active' => "showSocial", 
			'admin' => $admin, 
			'socials' => $socials, 
		]);
	}

	/**
	 * @return void
	 */
	public function addSocial(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$this->render("@admin/pages/social_add.html.twig", [
			'active' => "addSocial", 
			'admin' => $admin, 
		]);
	}

	/**
	 * @return void
	 */
	public function editSocial(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById($this->loggedUserId);

		$id = $this->params['id'];

		$socialManager = new SocialManager();

		$social = $socialManager->findOneBy([
			'id' => $id, 
		]);

		if (is_null($social)) {
			$controller = new ErrorController("show404");

			$controller->execute();

			return;
		}

		$this->render("@admin/pages/social_edit.html.twig", [
			'active' => "editSocial", 
			'admin' => $admin, 
			'social' => $social, 
		]);
	}

	/**
	 * @return void
	 */
	public function deleteSocial(): void {
		$id = $this->params['id'];

		// no render. action and after redirect
	}
}