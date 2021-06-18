<?php
namespace App\Core;

use Twig\Environment;
use App\Exceptions\TwigException;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use App\Exceptions\ConfigException;

class Twig {
	private $config;
	private $twig;

	public function __construct() {
		$confDir = CONF_DIR. "/config.yml";
		$this->config = yaml_parse_file($confDir);

		if (!$this->config) {
			throw new ConfigException("Error loading ". $confDir);
		}

		$loader = new FilesystemLoader(TEMPLATE_DIR);

		$loader->addPath(TEMPLATE_DIR. '/client', 'client');
        $loader->addPath(TEMPLATE_DIR. '/admin', 'admin');

		$twig = new Environment($loader, [
			'debug' => $this->config['env'] === 'dev'
		]);

		$twig->addExtension(new DebugExtension());

		$this->twig = $twig;
	}

	public function render($template, $array) {
        try {
            return $this->twig->render($template, $array);
        } catch (\Exception $e) {
			throw new TwigException($e);
        }
    }
}
