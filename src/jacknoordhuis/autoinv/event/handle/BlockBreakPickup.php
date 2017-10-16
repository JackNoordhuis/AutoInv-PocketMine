<?php

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
	public function handleBlockBreak(BlockBreakEvent $event) {
		foreach($event->getDrops() as $drop) {
			$event->getPlayer()->getInventory()->addItem($drop);
		}
		$event->setDrops([]);
	}

	public function getEventPriority() : int {
		return EventPriority::HIGHEST;
	}

}