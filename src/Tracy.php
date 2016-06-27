<?php

namespace \NAttreid\TracyPlugin;

use Nette\DI\Container,
    Nextras\MailPanel\MailPanel,
    Nextras\MailPanel\FileMailer,
    Nette\Http\Request,
    Nette\Http\Response,
    Nette\Configurator,
    Tracy\Debugger;

/**
 * Nastaveni pro Tracy
 *
 * @author Attreid <attreid@gmail.com>
 */
class Tracy {

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

    public function __construct($cookie, $mailPath, $mailPanel, Container $container, Request $request, Response $response) {
        $this->cookie = $cookie;
        $this->mailPath = $mailPath;
        $this->mailPanel = $mailPanel;
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;

        $this->enable = $this->request->getCookie(Configurator::COOKIE_SECRET) !== NULL;

        if (!Debugger::$productionMode) {
            $this->mailer();
        }
    }

    /**
     * Je debugger zapnuty (pomoci cookie)
     * @return boolean
     */
    public function isEnabled() {
        return $this->enable;
    }

    /**
     * Zapnuti debug modu pomoci cookie
     */
    public function enable() {
        $this->response->setCookie(Configurator::COOKIE_SECRET, $this->cookie, strtotime('1 years'), '/', '', '', TRUE);
        $this->enable = TRUE;
    }

    /**
     * Vypnuti debug modu pomoci cookie
     */
    public function disable() {
        $this->response->deleteCookie(Configurator::COOKIE_SECRET);
        $this->enable = FALSE;
    }

    /**
     * Mail panel
     */
    private function mailer() {
        if ($this->mailPanel) {
            $mailer = new FileMailer($this->mailPath);

            $service = 'mail.mailer';
            $this->container->removeService($service);
            $this->container->addService($service, $mailer);

            $this->container->getService('tracy.bar')
                    ->addPanel(new MailPanel($this->mailPath, $this->container->getService('http.request'), $this->container->getService('mail.mailer')));
        }
    }

}
