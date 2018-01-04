<?php

/**
 * EventManager.php – AutoInv
 *
 * Copyright (C) 2015-2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack Noordhuis
 *
 */

namespace jacknoordhuis\autoinv\event;

use jacknoordhuis\autoinv\AutoInv;
use pocketmine\plugin\MethodEventExecutor;

class EventManager {

	/** @var AutoInv */
	private $plugin;

	/** @var EventHandler[] */
	private $eventHandlers;

	public function __construct(AutoInv $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return AutoInv
	 */
	public function getPlugin() : AutoInv {
		return $this->plugin;
	}

	/**
	 * Register an event handler
	 *
	 * @param EventHandler $handler
	 */
	public function registerHandler(EventHandler $handler) {
		$this->eventHandlers[] = $handler;

		foreach($handler->handles() as $eventClass => $handleFunc) {
			$this->plugin->getLogger()->debug("Registered " . (new \ReflectionClass($eventClass))->getShortName() . " for " . (new \ReflectionObject($handler))->getShortName() . "::" . $handleFunc);
			$this->plugin->getServer()->getPluginManager()->registerEvent($eventClass, $handler, $handler->getEventPriority(), new MethodEventExecutor($handleFunc), $this->plugin, $handler->ignoreCancelled());
		}
	}

}