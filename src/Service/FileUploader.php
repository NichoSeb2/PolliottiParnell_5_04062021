<?php
namespace App\Service;

use App\Exceptions\FileException;
use App\Exceptions\FormException;
use App\Exceptions\FileServerException;
use App\Exceptions\FileTooBigException;

class FileUploader {
	const IMAGE_TYPE = ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'];

	/**
	 * Upload a file to the server
	 * 
	 * @param array $file
	 * @param string $outputDir
	 * @param string $outputFileName
	 * @param array $allowedType
	 * 
	 * @return string
	 */
	public function upload(array $file, string $outputDir, string $outputFileName, array $allowedType) {
		$extension = explode(".", $file['name']);

		switch ($file['error']) {
			case 1:
			case 2:
				throw new FileTooBigException("Le fichier est trop volumineux, il ne peut pas dépacer : ". ini_get('upload_max_filesize'). ".");
			case 3:
				throw new FileException("Le fichier n'a été que partiellement téléversé.");
			case 4:
				throw new FileException("Aucun fichier n'a été téléversé.");
			case 6:
				throw new FileServerException("Un dossier temporaire est manquant.");
			case 7:
				throw new FileServerException("Échec de l'écriture du fichier sur le disque.");
			case 8:
				throw new FileServerException("Une exception PHP a arrêté l'envoi de fichier.");
			default:
				break;
		}

		if (in_array($file['type'], $allowedType)) {
			$targetFile = $outputDir. $outputFileName. ".". end($extension);

			if (move_uploaded_file($file["tmp_name"], $targetFile)) {
				return "/". $targetFile;
			} else {
				throw new FileException("Le fichier n'a pas pu être téléversé.");
			}
		} else {
			throw new FormException("Le type du fichier téléversé n'est pas autorisé.");
		}
	}
}
