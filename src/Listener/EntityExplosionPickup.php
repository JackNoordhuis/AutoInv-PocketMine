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
use pocketmine\block\tile\Container;
use pocketmine\entity\Entity;
use pocketmine\entity\Explosive;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\VanillaItems;
use function array_merge;
use function floor;
use function random_int;
use const PHP_INT_MAX;

class EntityExplosionPickup implements Listener {

	/**
	 * Handle automatic item pickup on explosion.
	 *
	 * @param EntityExplodeEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function handleEntityExplode(EntityExplodeEvent $event) : void {
		$entity = $this->findEntityInventoryHolder($event->getEntity());
		$items = $this->getExplosionDrops($event->getBlockList(), $event->getYield());
		$event->setBlockList([]); // remove pocketmine drops
		$event->setYield(0); // set yield percentage to 0
		if($entity === null) {
			(new NoAutoPickupCandidateFound($items, $event->getEntity()->getPosition()))
				->call();
			return;
		}
		$remaining = $entity->getInventory()->addItem(...$items);
		if(!empty($remaining)) {
			(new ItemsRemainingAfterAutoPickup($remaining, $entity))
				->call();
		}
	}

	/**
	 * Find the entity closest to an explosion given a search range.
	 *
	 * @param \pocketmine\entity\Entity $near
	 * @param int                       $searchRange
	 *
	 * @return (\pocketmine\entity\Entity&\pocketmine\inventory\InventoryHolder)|null
	 */
	private function findEntityInventoryHolder(Entity $near, int $searchRange = 24) : (Entity&InventoryHolder)|null {
		$searchBb = $near->getBoundingBox()->expand($searchRange, $searchRange, $searchRange);
		$pos = $near->getPosition();
		$closest = PHP_INT_MAX;
		/** @var Entity&InventoryHolder|null $entity */
		$entity = null;
		foreach($near->getWorld()->getNearbyEntities($searchBb) as $nearby) {
			$dist = $pos->distance($nearby->getPosition());
			if($nearby instanceof InventoryHolder and $dist <= $closest) {
				$entity = $nearby;
				$closest = $dist;
			}
		}

		return $entity;
	}

	/**
	 * Get the items that would normally be dropped in an explosion.
	 *
	 * @param \pocketmine\block\Block[] $blocks
	 * @param float                     $yield
	 *
	 * @return \pocketmine\item\Item[] Items that could not be added to the inventory.
	 */
	private function getExplosionDrops(array $blocks, float $yield) : array {
		$air = VanillaItems::AIR();
		$items = [];
		foreach($blocks as $key => $block) {
			$pos = $block->getPosition();
			$world = $pos->getWorld();
			if(random_int(0, 100) < $yield) {
				$items = array_merge($items, $block->getDrops($air));
			}
			if(($tile = $world->getTileAt((int)floor($pos->x), (int)floor($pos->y), (int)floor($pos->z))) !== null) {
				if($tile instanceof Container) {
					$tileInventory = $tile->getRealInventory();
					$items = array_merge($items, $tileInventory->getContents());
					$tileInventory->setContents([]);
				}
				$tile->onBlockDestroyed();
			}
		}

		return $items;
	}

}
