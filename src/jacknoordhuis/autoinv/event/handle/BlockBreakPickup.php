<?php

/**
 * BlockBreakPickup.php – AutoInv
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

namespace jacknoordhuis\autoinv\event\handle;

use jacknoordhuis\autoinv\event\EventHandler;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;

class BlockBreakPickup extends EventHandler {

	public function handles() : array {
		return [
			BlockBreakEvent::class => "handleBlockBreak",
		];
	}

	/**
	 * Handle automatic block pickup on break
	 *
	 * @param BlockBreakEvent $event
	 */
	public function handleBlockBreak(BlockBreakEvent $event) : void {
		foreach($event->getDrops() as $drop) {
			$event->getPlayer()->getInventory()->addItem($drop);
		}
		$event->setDrops([]);
	}

	public function getEventPriority() : int {
		return EventPriority::HIGHEST;
	}

}