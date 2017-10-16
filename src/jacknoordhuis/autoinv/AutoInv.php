<?php

/**
 * AutoInv AutoInv class
 *
 * Created on Mar 24, 2016 at 10:09:00 PM
 *
 * @author Jack
 */

namespace jacknoordhuis\autoinv;

use jacknoordhuis\autoinv\event\EventManager;
use jacknoordhuis\autoinv\util\config\EventConfigurationLoader;
use pocketmine\plugin\PluginBase;

class AutoInv extends PluginBase {

	/** @var EventManager */
	public $eventManager;

	/** @var EventConfigurationLoader */
	private $eventConfigLoader;

	const SETTINGS_CONFIG = "Settings.yml";

	public function onEnable() {
		$this->saveResource(self::SETTINGS_CONFIG);
		$this->setEventManager();
		$this->eventConfigLoader = new EventConfigurationLoader($this, $this->getDataFolder() . self::SETTINGS_CONFIG);
	}

	public function getEventConfigurationLoader() : EventConfigurationLoader {
		return $this->eventConfigLoader;
	}

	public function setEventManager() : bool {
		if(!($this->eventManager instanceof EventManager)) {
			$this->eventManager = new EventManager($this);
			return true;
		}
		return false;
	}

	public function getEventManager() : ?EventManager {
		return $this->eventManager;
	}

}
