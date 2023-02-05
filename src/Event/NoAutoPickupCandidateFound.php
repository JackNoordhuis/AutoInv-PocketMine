<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\Event;

use pocketmine\world\Position;

class NoAutoPickupCandidateFound extends AutoInvEvent {

	/**
	 * @param \pocketmine\item\Item[]    $items
	 * @param \pocketmine\world\Position $dropPosition
	 */
	public function __construct(
		private array    $items,
		private Position $dropPosition
	) {
	}

	/**
	 * @return \pocketmine\item\Item[]
	 */
	public function getItems() : array {
		return $this->items;
	}

	/**
	 * @param \pocketmine\item\Item[] $items
	 */
	public function setItems(array $items) : void {
		$this->items = $items;
	}

	public function getDropPosition() : Position {
		return $this->dropPosition;
	}

	public function setDropPosition(Position $dropPosition) : void {
		$this->dropPosition = $dropPosition;
	}

}
