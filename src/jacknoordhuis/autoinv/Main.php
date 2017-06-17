<?php

/**
 * AutoInv Main class
 *
 * Created on Mar 24, 2016 at 10:09:00 PM
 *
 * @author Jack
 */

namespace jacknoordhuis\autoinv;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

	/** @var EventListener */
	public $listener;

	/** @var Config */
	private $settings;

	const SETTINGS_CONFIG = "Settings.yml";

	public function onEnable() {
		$this->saveResource(self::SETTINGS_CONFIG);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_CONFIG, Config::YAML);
		$this->setListener();
	}

	/**
	 * @return Config
	 */
	public function getSettings() : Config {
		return $this->settings;
	}

	/**
	 * Set the event listener
	 *
	 * @return null
	 */
	public function setListener() {
		if(!$this->listener instanceof EventListener) {
			$this->listener = new EventListener($this);
		}
		return;
	}

	/**
	 * Get the event listener
	 *
	 * @return null|EventListener
	 */
	public function getListener() {
		return $this->listener;
	}

}
