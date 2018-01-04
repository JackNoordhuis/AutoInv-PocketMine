<?php

/**
 * EventHandler.php – AutoInv
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

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;

/**
 * Basic class for all event handlers to extend
 */
abstract class EventHandler implements Listener {

	/** @var EventManager */
	private $manager;

	public function __construct(EventManager $manager) {
		$this->manager = $manager;
	}

	public function getManager() : ?EventManager {
		return $this->manager;
	}

	/**
	 * Returns an array of events that the handler handles
	 *
	 * @return string[]
	 */
	abstract public function handles() : array;

	/**
	 * Returns the priority of the event handler
	 *
	 * @return int
	 */
	public function getEventPriority() : int {
		return EventPriority::NORMAL;
	}

	/**
	 * Returns whether the handler should be called if the event is cancelled
	 *
	 * @return bool
	 */
	public function ignoreCancelled() : bool {
		return true;
	}

}