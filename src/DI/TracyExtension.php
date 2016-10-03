<?php

namespace NAttreid\TracyPlugin\DI;

use NAttreid\TracyPlugin\Tracy;

/**
 * Nastaveni Tracy
 *
 * @author Attreid <attreid@gmail.com>
 */
class TracyExtension extends \Nette\DI\CompilerExtension
{

	private $defaults = [
		'cookie' => null,
		'mailPath' => '%tempDir%/mail-panel-mails',
		'mailPanel' => true
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		if (!isset($config['cookie'])) {
			throw new \Nette\InvalidStateException("TracyPlugin: 'cookie' does not set in config.neon");
		}

		$config['mailPath'] = \Nette\DI\Helpers::expand($config['mailPath'], $builder->parameters);

		$builder->addDefinition($this->prefix('tracyPlugin'))
			->setClass(Tracy::class)
			->setArguments([$config['cookie']])
			->addSetup('setMail', [$config['mailPath'], $config['mailPanel']]);
	}

	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->methods['initialize'];
		if (class_exists('Tracy\Debugger')) {
			$initialize->addBody('$this->getService(?)->run();', [$this->prefix('tracyPlugin')]);
		}
	}

}
