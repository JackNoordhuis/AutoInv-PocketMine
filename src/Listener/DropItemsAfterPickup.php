<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\Listener;

use JackNoordhuis\AutoInv\Event\ItemsRemainingAfterAutoPickup;
use JackNoordhuis\AutoInv\Event\NoAutoPickupCandidateFound;
use pocketmine\event\Listener;
use function random_int;

class DropItemsAfterPickup implements Listener {

	/**
	 * Handle dropping remaining items from auto pickup.
	 *
	 * @param \JackNoordhuis\AutoInv\Event\ItemsRemainingAfterAutoPickup $event
	 *
	 * @priority HIGHEST
	 */
	public function dropRemainingItems(ItemsRemainingAfterAutoPickup $event) : void {
		$world = $event->getPickedUpBy()->getWorld();
		$pos = $event->getPickedUpBy()->getPosition();
		$items = $event->getRemainingItems();
		$event->setRemainingItems([]);
		foreach($items as $item) {
			$world->dropItem(
				$pos,
				$item
			);
		}
	}

	/**
	 * Handle dropping items when no player was found for auto pickup.
	 *
	 * @param \JackNoordhuis\AutoInv\Event\NoAutoPickupCandidateFound $event
	 *
	 * @priority HIGHEST
	 */
	public function dropNoPickupCandidateItems(NoAutoPickupCandidateFound $event) : void {
		$pos = $event->getDropPosition();
		$world = $pos->getWorld();
		$items = $event->getItems();
		$event->setItems([]);
		foreach($items as $item) {
			$world->dropItem(
				$pos->add(random_int(-3, 3), 0, random_int(-3, 3)),
				$item
			);
		}
	}

}
