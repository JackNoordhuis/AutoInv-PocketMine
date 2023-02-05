<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\Event;

use pocketmine\entity\Entity;
use pocketmine\inventory\InventoryHolder;

class ItemsRemainingAfterAutoPickup extends AutoInvEvent {

	/**
	 * @param \pocketmine\item\Item[]                                         $remainingItems
	 * @param \pocketmine\entity\Entity&\pocketmine\inventory\InventoryHolder $pickedUpBy
	 */
	public function __construct(
		private array                  $remainingItems,
		private Entity&InventoryHolder $pickedUpBy
	) {
	}

	/**
	 * @return \pocketmine\item\Item[]
	 */
	public function getRemainingItems() : array {
		return $this->remainingItems;
	}

	/**
	 * @param \pocketmine\item\Item[] $remainingItems
	 */
	public function setRemainingItems(array $remainingItems) : void {
		$this->remainingItems = $remainingItems;
	}

	/**
	 * @return \pocketmine\entity\Entity&\pocketmine\inventory\InventoryHolder
	 */
	public function getPickedUpBy() : Entity&InventoryHolder {
		return $this->pickedUpBy;
	}

}
