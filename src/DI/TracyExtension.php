<?php

namespace NAttreid\TracyPlugin\DI;

use NAttreid\TracyPlugin\Tracy;

/**
 * Nastaveni Tracy
 * 
 * @author Attreid <attreid@gmail.com>
 */
class TracyExtension extends \Nette\DI\CompilerExtension {

    private $defaults = [
        'cookie' => NULL,
        'mailPath' => '%tempDir%/mail-panel-mails',
        'mailPanel' => NULL
    ];

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults, $this->getConfig());

        if (!isset($config['cookie'])) {
            throw new \Nette\InvalidStateException("Neni nastavena 'cookie' v config.neon pro TracyPlugin");
        } elseif (!isset($config['mailPanel'])) {
            throw new \Nette\InvalidStateException("Neni nastaveno 'mailPanel' v config.neon pro TracyPlugin");
        }

        $config['mailPath'] = \Nette\DI\Helpers::expand($config['mailPath'], $this->getContainerBuilder()->parameters);

        $builder->addDefinition($this->prefix('tracyPlugin'))
                ->setClass(Tracy::class)
                ->setArguments([$config['cookie'], $config['mailPath'], $config['mailPanel']])
                ->addTag('run');
    }

}
