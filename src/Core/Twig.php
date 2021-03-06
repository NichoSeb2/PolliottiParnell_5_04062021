<?php
namespace App\Core;

use Twig\Environment;
use App\Exceptions\TwigException;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use App\Exceptions\ConfigException;
use App\Service\TwigGlobalVariable;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class Twig {
	private array $config;

	private Environment $twig;

	public function __construct() {
		$confDir = CONF_DIR. "/config.yml";
		$this->config = yaml_parse_file($confDir);

		if (!$this->config) {
			throw new ConfigException("Error loading ". $confDir);
		}

		$loader = new FilesystemLoader(TEMPLATE_DIR);

        $loader->addPath(TEMPLATE_DIR. '/admin', 'admin');
		$loader->addPath(TEMPLATE_DIR. '/client', 'client');
		$loader->addPath(TEMPLATE_DIR. '/mail', 'mail');

		$twig = new Environment($loader, [
			'debug' => $this->config['env'] === "dev", 
		]);

		$twig->addExtension(new DebugExtension());
		$twig->addExtension(new MarkdownExtension());

		$twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
			public function load($class) {
				if (MarkdownRuntime::class === $class) {
					return new MarkdownRuntime(new DefaultMarkdown());
				}
			}
		});

		$twig->addGlobal('debug', $this->config['env'] === "dev");
		$twig->addGlobal('connected', (!empty($_SESSION['id']) && is_numeric($_SESSION['id'])));
		$twig->addGlobal('socials', TwigGlobalVariable::getSocials());
		$twig->addGlobal('admin', TwigGlobalVariable::getAdmin());
		$twig->addGlobal('blog', TwigGlobalVariable::getBlog());
		$twig->addGlobal('uri', TwigGlobalVariable::getCurrentUri());

		$this->twig = $twig;
	}

	/**
	 * Render a given template
	 * 
	 * @param string $template
	 * @param array $args
	 * 
	 * @return string
	 */
	public function render(string $template, array $args): string {
        try {
            return $this->twig->render($template, $args);
        } catch (\Exception $e) {
			throw new TwigException($e);
        }
    }
}
