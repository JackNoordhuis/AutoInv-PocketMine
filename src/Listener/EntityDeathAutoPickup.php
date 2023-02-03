<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\listener;

use JackNoordhuis\AutoInv\event\ItemsRemainingAfterAutoPickup;
use JackNoordhuis\AutoInv\event\NoAutoPickupCandidateFound;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\InventoryHolder;

class EntityDeathAutoPickup implements Listener {

	/**
	 * Handle automatic pickup on entity death.
	 *
	 * @param \pocketmine\event\entity\EntityDeathEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function handleEntityDeath(EntityDeathEvent $event) : void {
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
