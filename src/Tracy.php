<?php

namespace NAttreid\TracyPlugin;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\Response;
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

	use \Nette\SmartObject;

	/** @var string */
	private $cookie;

	/** @var string */
	private $mailPath;

	/** @var boolean */
	private $mailPanel;

	/** @var Container */
	private $container;

	/** @var Request */
	private $request;

	/** @var Response */
	private $response;

	/** @var boolean */
	private $enable;

	public function __construct($cookie, Container $container, Request $request, Response $response)
	{
		$this->cookie = $cookie;
		$this->container = $container;
		$this->request = $request;
		$this->response = $response;

		$this->enable = $this->request->getCookie(Configurator::COOKIE_SECRET) !== NULL;
	}

	/**
	 * Nastavi mail panel
	 * @param string $mailPath
	 * @param boolean $mailPanel
	 */
	public function setMail($mailPath, $mailPanel)
	{
		$this->mailPath = $mailPath;
		$this->mailPanel = $mailPanel;
	}

	/**
	 * Zapne nebo vypne mail panel
	 * @param boolean $enable
	 */
	public function enableMail($enable = TRUE)
	{
		$this->mailPanel = $enable;
	}

	/**
	 * Je debugger zapnuty (pomoci cookie)
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->enable;
	}

	/**
	 * Zapnuti debug modu pomoci cookie
	 */
	public function enable()
	{
		$this->response->setCookie(Configurator::COOKIE_SECRET, $this->cookie, strtotime('1 years'), '/', '', '', TRUE);
		$this->enable = TRUE;
	}

	/**
	 * Vypnuti debug modu pomoci cookie
	 */
	public function disable()
	{
		$this->response->deleteCookie(Configurator::COOKIE_SECRET);
		$this->enable = FALSE;
	}

	/**
	 * Mail panel
	 */
	private function mailer()
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

	public function run()
	{
		if (!Debugger::$productionMode) {
			$this->mailer();
		}
	}

}
