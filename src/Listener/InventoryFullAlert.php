<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv\listener\alert;

use JackNoordhuis\AutoInv\event\ItemsRemainingAfterAutoPickup;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\player\Player;
use function microtime;
use function spl_object_hash;

class InventoryFullAlert implements Listener {

	/**
	 * @var array<string, int>
	 */
	private array $recentAlerts = [];

	public function __construct(
		private readonly int    $alertInterval = 5,
		private readonly string $text = "",
		private readonly string $secondaryText = "",
		private readonly string $messageType = "title",
		private readonly bool   $playSound = true
	) {}

	/**
	 * Handle inventory full alerts on block break.
	 *
	 * @param \JackNoordhuis\AutoInv\event\ItemsRemainingAfterAutoPickup $event
	 *
	 * @priority MONITOR
	 */
	public function sendAlertWhenInventoryFull(ItemsRemainingAfterAutoPickup $event) : void {
		$entity = $event->getPickedUpBy();
		if(!($entity instanceof Player)) {
			return;
		}
		$this->alert($entity);
	}

	/**
	 * Handle removing recent alerts on player quit.
	 *
	 * @param PlayerQuitEvent $event
	 *
	 * @priority MONITOR
	 */
	public function cleanupRecentAlerts(PlayerQuitEvent $event) : void {
		if(isset($this->recentAlerts[$hash = spl_object_hash($event->getPlayer())])) {
			unset($this->recentAlerts[$hash]);
		}
	}

	/**
	 * Send inventory full alert to a player.
	 *
	 * @param \pocketmine\player\Player $player
	 */
	protected function alert(Player $player) : void {
		if(isset($this->recentAlerts[$hash = spl_object_hash($player)]) && (isset($this->recentAlerts[$hash]) and ($time = microtime(true)) - $this->recentAlerts[$hash] >= $this->alertInterval)) {
			return;
		}
		$this->recentAlerts[$hash] = $time ?? microtime(true);
		switch($this->messageType) {
			case "":
				break; // allow blank to disable message alert
			case "message":
				$player->sendMessage($this->text);
				break;
			case "popup":
				$player->sendPopup($this->text);
				break;
			case "tip":
				$player->sendTip($this->text);
				break;
			default:
				$player->sendTitle($this->text, $this->secondaryText);
				break;
		}

		if($this->playSound) {
			$pk = new LevelEventPacket();
			$pk->position = $player->getPosition()->asVector3();
			$pk->eventId = LevelEvent::SOUND_ENDERMAN_TELEPORT;
			$pk->eventData = 0;
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

}
