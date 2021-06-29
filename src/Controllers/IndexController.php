<?php
namespace App\Controllers;

use App\Core\Controller;

class IndexController extends Controller {	
	public function showHome() {
		$post = [[
			'slug' => 2, 
			'title' => "Saepe nostrum ullam eveniet pariatur voluptates odit", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		], [
			'slug' => 1, 
			'title' => "Laborum aute elit cillum commodo minim occaecat", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "18/06/2021", 
		]];

		$this->render("@client/pages/index.html.twig", [
			'post' => $post, 
		]);
	}

	public function showContact() {
		$this->render("@client/pages/contact.html.twig");
	}
}