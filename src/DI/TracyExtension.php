<?php

declare(strict_types=1);

namespace NAttreid\TracyPlugin\DI;

use NAttreid\TracyPlugin\Tracy;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\InvalidStateException;
use Nette\PhpGenerator\ClassType;

/**
 * Nastaveni Tracy
 *
 * @author Attreid <attreid@gmail.com>
 */
class TracyExtension extends CompilerExtension
{

	private $defaults = [
		'cookie' => null,
		'mailPath' => '%tempDir%/mail-panel-mails',
		'mailPanel' => true
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		if ($config['cookie'] === null) {
			throw new InvalidStateException("TracyPlugin: 'cookie' does not set in config.neon");
		}

		$config['mailPath'] = Helpers::expand($config['mailPath'], $builder->parameters);

		$builder->addDefinition($this->prefix('tracyPlugin'))
			->setType(Tracy::class)
			->setArguments([$config['cookie']])
			->addSetup('setMail', [$config['mailPath'], $config['mailPanel']]);
	}

	public function afterCompile(ClassType $class): void
	{
		$initialize = $class->methods['initialize'];
		if (class_exists('Tracy\Debugger')) {
			$initialize->addBody('$this->getService(?)->run();', [$this->prefix('tracyPlugin')]);
		}
	}

}
