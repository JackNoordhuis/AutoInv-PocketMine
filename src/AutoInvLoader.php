<?php

declare(strict_types=1);

/**
 * Copyright (C) 2016-2023 Jack Noordhuis
 *
 * Permission is granted to use and/or modify this software under the terms of the MIT License.
 */

namespace JackNoordhuis\AutoInv;

use JackNoordhuis\AutoInv\Listener\InventoryFullAlert;
use JackNoordhuis\AutoInv\Listener\BlockBreakAutoPickup;
use JackNoordhuis\AutoInv\Listener\DropItemsAfterPickup;
use JackNoordhuis\AutoInv\Listener\EntityDeathAutoPickup;
use JackNoordhuis\AutoInv\Listener\EntityExplosionPickup;
use JackNoordhuis\AutoInv\Listener\PlayerDeathPickup;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use SplFileInfo;
use function is_bool;
use function is_string;
use function strtolower;
use function yaml_parse_file;
use const DIRECTORY_SEPARATOR;

class AutoInvLoader extends PluginBase {

	private const SETTINGS_CONFIG = 'Settings.yml';

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	protected function onLoad() : void {
		$this->saveResource(self::SETTINGS_CONFIG);
	}

	/**
	 * Called when the plugin is enabled
	 */
	protected function onEnable() : void {
		$this->loadConfiguration(new SplFileInfo(
			$this->getDataFolder() . DIRECTORY_SEPARATOR . self::SETTINGS_CONFIG
		));
	}

	private function loadConfiguration(SplFileInfo $settingsConfig) : void {
		$config = yaml_parse_file($settingsConfig->getPathname());
		$eventConfig = $config["general"]["events"];

		if(self::getConfigBool($eventConfig["block-break"] ?? false)) {
			$this->registerListener(new BlockBreakAutoPickup);
		}

		if(self::getConfigBool($eventConfig["entity-death"] ?? false)) {
			$this->registerListener(new EntityDeathAutoPickup);
		}

		if(self::getConfigBool($eventConfig["player-death"] ?? false)) {
			$this->registerListener(new PlayerDeathPickup);
		}

		if(self::getConfigBool($eventConfig["entity-explosion"] ?? false)) {
			$this->registerListener(new EntityExplosionPickup);
		}

		$inventoryConfig = $eventConfig["inventory-full"] ?? null;
		if($inventoryConfig !== null && self::getConfigBool($inventoryConfig["active"])) {
			$this->registerListener(new InventoryFullAlert(
				$inventoryConfig["interval"],
				TextFormat::colorize($inventoryConfig["message"]["text"] ?? ''),
				TextFormat::colorize($inventoryConfig["message"]["secondary-text"] ?? ''),
				strtolower($inventoryConfig["message"]["type"] ?? "message"),
				$inventoryConfig["sound"] ?? false)
			);
		}

		$remainingItemsConfig = $eventConfig["remaining-items"] ?? "drop";
		if($remainingItemsConfig === "drop") {
			$this->registerListener(new DropItemsAfterPickup);
		}
	}

	private function registerListener(Listener $listener) : void {
		$this->getServer()->getPluginManager()->registerEvents($listener, $this);
	}

	/**
	 * Retrieve a boolean value from string.
	 *
	 * @param string|bool|int $value
	 *
	 * @return bool
	 */
	private static function getConfigBool(mixed $value) : bool {
		if(is_bool($value)) {
			return $value;
		}

		return match (is_string($value) ? strtolower($value) : $value) {
			'off', 'false', 'no', 0 => false,
			default => true,
		};
	}

}
