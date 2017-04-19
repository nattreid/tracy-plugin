<?php

declare(strict_types=1);

namespace NAttreid\TracyPlugin;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\SmartObject;
use Nextras\MailPanel\FileMailer;
use Nextras\MailPanel\MailPanel;
use Tracy\Debugger;

/**
 * Nastaveni pro Tracy
 *
 * @author Attreid <attreid@gmail.com>
 */
class Tracy
{
	use SmartObject;

	/** @var string */
	private $cookie;

	/** @var string */
	private $mailPath;

	/** @var bool */
	private $mailPanel;

	/** @var Container */
	private $container;

	/** @var Request */
	private $request;

	/** @var Response */
	private $response;

	/** @var bool */
	private $enable;

	public function __construct(string $cookie, Container $container, Request $request, Response $response)
	{
		$this->cookie = $cookie;
		$this->container = $container;
		$this->request = $request;
		$this->response = $response;

		$this->enable = $this->request->getCookie(Configurator::COOKIE_SECRET) !== null;
	}

	/**
	 * Nastavi mail panel
	 * @param string $mailPath
	 * @param bool $mailPanel
	 */
	public function setMail(string $mailPath, bool $mailPanel): void
	{
		$this->mailPath = $mailPath;
		$this->mailPanel = $mailPanel;
	}

	/**
	 * Zapne nebo vypne mail panel
	 * @param bool $enable
	 */
	public function enableMail(bool $enable = true): void
	{
		$this->mailPanel = $enable;
	}

	/**
	 * Je debugger zapnuty (pomoci cookie)
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->enable;
	}

	/**
	 * Zapnuti debug modu pomoci cookie
	 */
	public function enable(): void
	{
		$this->response->setCookie(Configurator::COOKIE_SECRET, $this->cookie, strtotime('1 years'), '/', '', '', true);
		$this->enable = true;
	}

	/**
	 * Vypnuti debug modu pomoci cookie
	 */
	public function disable(): void
	{
		$this->response->deleteCookie(Configurator::COOKIE_SECRET);
		$this->enable = false;
	}

	/**
	 * Mail panel
	 */
	private function mailer(): void
	{
		if ($this->mailPanel) {
			$mailer = new FileMailer($this->mailPath);

			$service = 'mail.mailer';
			$this->container->removeService($service);
			$this->container->addService($service, $mailer);

			$this->container->getService('tracy.bar')
				->addPanel(new MailPanel($this->mailPath, $this->request, $this->container->getService($service)));
		}
	}

	public function run(): void
	{
		if (!Debugger::$productionMode) {
			$this->mailer();
		}
	}

}
