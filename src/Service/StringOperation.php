<?php
namespace App\Service;

class StringOperation {
	/**
	 * Check if a string starts with an other
	 * 
	 * This function is useless if php 8 is used
	 * 
	 * @param string $haystack
	 * @param string $needle
	 * 
	 * @return bool
	 */
	public function str_starts_with(string $haystack, string $needle): bool {
		return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
	}
}