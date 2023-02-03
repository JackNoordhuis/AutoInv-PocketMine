<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\listener;

use JackNoordhuis\AutoInv\event\ItemsRemainingAfterAutoPickup;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class BlockBreakAutoPickup implements Listener {

	/**
	 * Handle automatic block pickup on break.
	 *
	 * @param BlockBreakEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function handleBlockBreak(BlockBreakEvent $event) : void {
		$remaining = $event->getPlayer()->getInventory()->addItem(...$event->getDrops());
		$event->setDrops([]);
		if(!empty($remaining)) {
			(new ItemsRemainingAfterAutoPickup($remaining, $event->getPlayer()))
				->call();
		}
	}

}
