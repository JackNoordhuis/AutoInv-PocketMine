<?php

/**
 * EventConfigurationLoader.php – AutoInv
 *
 * Copyright (C) 2015-2017 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack Noordhuiss
 *
 * Last modified on 16/10/2017 at 7:12 PM
 *
 */

namespace jacknoordhuis\autoinv\util\config;

use jacknoordhuis\autoinv\event\handle\BlockBreakPickup;
use jacknoordhuis\autoinv\event\handle\EntityExplosionPickup;
use jacknoordhuis\autoinv\event\handle\InventoryFullPickup;
use jacknoordhuis\autoinv\event\handle\PlayerDeathPickup;
use jacknoordhuis\autoinv\util\ColorUtils;

class EventConfigurationLoader extends ConfigurationLoader {

	public function onLoad(array $data) {
		$manager = $this->getPlugin()->getEventManager();
		$eventData = $data["general"]["events"];

		if(self::getBoolean($eventData["block-break"] ?? false)) {
			$manager->registerHandler(new BlockBreakPickup($manager));
		}

		if(self::getBoolean($eventData["player-death"] ?? false)) {
			$manager->registerHandler(new PlayerDeathPickup($manager));
		}

		if(self::getBoolean($eventData["entity-explosion"] ?? false)) {
			$manager->registerHandler(new EntityExplosionPickup($manager));
		}

		if((($inventoryData = $eventData["inventory-full"]) ?? false) and self::getBoolean($inventoryData["active"])) {
			$manager->registerHandler(new InventoryFullPickup($manager, $inventoryData["interval"], ColorUtils::translateColors($inventoryData["message"]["text"] ?? ""), ColorUtils::translateColors($inventoryData["message"]["secondary-text"] ?? ""),strtolower($inventoryData["message"]["type"] ?? "message"), $inventoryData["sound"] ?? false));
		}
	}

}