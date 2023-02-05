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
use pocketmine\plugin\PluginException;
use pocketmine\utils\TextFormat;
use function is_array;
use function is_string;
use function strtolower;
use function yaml_parse_file;
use const DIRECTORY_SEPARATOR;

class AutoInvLoader extends PluginBase {

	private const SETTINGS_CONFIG = 'Settings.yml';

	/**
	 * Mapping of config option keys to listener classes.
	 */
	private const OPTIONAL_LISTENERS = [
		"block-break" => BlockBreakAutoPickup::class,
		"entity-death" => EntityDeathAutoPickup::class,
		"player-death" => PlayerDeathPickup::class,
		"entity-explosion" => EntityExplosionPickup::class
	];

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
		$settings = yaml_parse_file($this->getDataFolder() . DIRECTORY_SEPARATOR . self::SETTINGS_CONFIG);
		if(!is_array($settings)) {
			throw new PluginException("Could not parse " . self::SETTINGS_CONFIG . ", please check the syntax and restart the server.");
		}
		$this->loadConfiguration($settings);
	}

	/**
	 * @param array<string, array<string, bool|int|string|array<string, bool|int|string|array<string, bool|int|string|array<string, string>>>>> $settings
	 */
	private function loadConfiguration(array $settings) : void {
		$eventConfig = $settings["general"]["events"];

		foreach(self::OPTIONAL_LISTENERS as $configOption => $listenerClass) {
			$this->registerOptionalListener($configOption, $eventConfig, $listenerClass);
		}

		$remainingItemsConfig = $eventConfig["remaining-items"] ?? "drop";
		if($remainingItemsConfig === "drop") {
			$this->registerListener(new DropItemsAfterPickup);
		}

		$inventoryConfig = $eventConfig["inventory-full"] ?? null;
		if(!is_array($inventoryConfig)) {
			throw new PluginException("Invalid config settings for general.events.inventory-full");

		}
		$inventoryConfigActive = $inventoryConfig["active"] ?? false;
		$inventoryConfigSound = $inventoryConfig["sound"] ?? false;
		if(is_array($inventoryConfigActive) || is_array($inventoryConfigSound)) {
			throw new PluginException("Invalid config settings for general.events.inventory-full");
		}
		if(self::getConfigBool($inventoryConfigActive)) {
			$this->registerListener(new InventoryFullAlert(
					(int)($inventoryConfig["interval"] ?? 5),
					TextFormat::colorize($inventoryConfig["message"]["text"] ?? ''),
					TextFormat::colorize($inventoryConfig["message"]["secondary-text"] ?? ''),
					strtolower($inventoryConfig["message"]["type"] ?? "message"),
					self::getConfigBool($inventoryConfigSound))
			);
		}
	}

	/**
	 * @param string                                                                                              $configOption
	 * @param array<string, array<string, array<string, string>|bool|int|string>|bool|int|string>|bool|int|string $configSettings
	 * @param class-string<\pocketmine\event\Listener>                                                            $listenerClass
	 */
	private function registerOptionalListener(string $configOption, mixed $configSettings, string $listenerClass) : void {
		$opt = $configSettings[$configOption] ?? false;
		if(is_array($opt)) {
			throw new PluginException("Invalid config settings for general.events." . $configOption);
		}
		if(self::getConfigBool($opt)) {
			$this->registerListener(new $listenerClass);
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
		return match (is_string($value) ? strtolower($value) : $value) {
			false, 'off', 'false', 'no', 0 => false,
			default => true,
		};
	}

}
