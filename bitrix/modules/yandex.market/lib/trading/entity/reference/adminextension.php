<?php

namespace Yandex\Market\Trading\Entity\Reference;

use Yandex\Market;
use Bitrix\Main;

abstract class AdminExtension
{
	protected $environment;

	public static function getClassName()
	{
		return '\\' . static::class;
	}

    //Переход на php8.2
//	public function __construct(Environment $environment)
//	{
//		$this->environment = $environment;
//	}
//    public function __construct(?Environment $environment)
//    {
//        if ($environment === null) {
//            throw new \InvalidArgumentException('Environment instance is required');
//        }
//        $this->environment = $environment;
//    }
    public function __construct(?Environment $environment = null)
    {
        $this->environment = $environment;
    }

	public function install()
	{
		$this->bind();
	}

	public function uninstall()
	{
		$this->unbind();
	}

	protected function bind()
	{
		$handlers = $this->getEventHandlers();

		Market\Utils\Event::bind(static::getClassName(), $handlers);
	}

	protected function unbind()
	{
		$handlers = $this->getEventHandlers();

		Market\Utils\Event::unbind(static::getClassName(), $handlers);
	}

	protected function getEventHandlers()
	{
		return [];
	}
}