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
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\inventory\InventoryHolder;

class PlayerDeathPickup implements Listener {

	/**
	 * Handle automatic item pickup on player death.
	 *
	 * @param PlayerDeathEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function handlePlayerDeath(PlayerDeathEvent $event) : void {
		$victim = $event->getEntity();
		$cause = $victim->getLastDamageCause();
		$items = $event->getDrops();
		$event->setDrops([]);
		if(
			!($cause instanceof EntityDamageByEntityEvent) ||
			!(($killer = $cause->getDamager()) instanceof InventoryHolder)
		) {
			(new NoAutoPickupCandidateFound($items, $victim->getPosition()))
				->call();
			return;
		}
		$remaining = $killer->getInventory()->addItem(...$items);
		if(!empty($remaining)) {
			(new ItemsRemainingAfterAutoPickup($remaining, $killer))
				->call();
		}
	}

}
