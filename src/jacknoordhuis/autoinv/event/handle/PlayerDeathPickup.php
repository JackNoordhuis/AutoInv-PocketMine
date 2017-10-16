<?php

namespace jacknoordhuis\autoinv\event\handle;

use jacknoordhuis\autoinv\event\EventHandler;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\inventory\InventoryHolder;

class PlayerDeathPickup extends EventHandler {

	public function handles() : array {
		return [
			PlayerDeathEvent::class => "handlePlayerDeath",
		];
	}

	/**
	 * Handle automatic item pickup on player death
	 *
	 * @param PlayerDeathEvent $event
	 */
	public function handleBPlayerDeath(PlayerDeathEvent $event) {
		$victim = $event->getEntity();
		$cause = $victim->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent) {
			$killer = $cause->getDamager();
			if($killer instanceof InventoryHolder) {
				$drops = [];
				foreach($event->getDrops() as $drop) {
					if($killer->getInventory()->canAddItem($drop)) {
						$killer->getInventory()->addItem($drop);
					} else {
						$drops[] = $drop;
					}
				}
				$event->setDrops($drops);
			}
		}
	}

	public function getEventPriority() : int {
		return EventPriority::HIGHEST;
	}

}