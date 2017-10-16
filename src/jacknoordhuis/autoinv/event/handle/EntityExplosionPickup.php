<?php

namespace jacknoordhuis\autoinv\event\handle;

use jacknoordhuis\autoinv\event\EventHandler;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\EventPriority;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;

class EntityExplosionPickup extends EventHandler {

	public function handles() : array {
		return [
			EntityExplodeEvent::class => "handleEntityExplode",
		];
	}

	/**
	 * Handle automatic block pickup on break
	 *
	 * @param EntityExplodeEvent $event
	 */
	public function handleEntityExplode(EntityExplodeEvent $event) {
		$explosive = $event->getEntity();
		$closest = PHP_INT_MAX;
		$entity = null;
		foreach($explosive->getLevel()->getNearbyEntities($explosive->getBoundingBox()->grow(24, 24, 24)) as $nearby) {
			if($nearby instanceof InventoryHolder and $explosive->distance($nearby) <= $closest) {
				$entity = $nearby;
				$closest = $explosive->distance($nearby);
			}
		}

		if($entity !== null) {
			$blocks = $event->getBlockList();
			$yield = $event->getYield();
			$event->setYield(0); // Make sure no item entities are dropped by the explosion
			$air = Item::get(Item::AIR);
			/** @var Block $block */
			foreach($blocks as $key => $block) {
				if(mt_rand(0, 100) < $yield) {
					foreach($block->getDrops($air) as $item) {
						$entity->getInventory()->addItem(Item::get(...$item));
					}
				}
			}
		}
	}

	public function getEventPriority() : int {
		return EventPriority::HIGHEST;
	}

}