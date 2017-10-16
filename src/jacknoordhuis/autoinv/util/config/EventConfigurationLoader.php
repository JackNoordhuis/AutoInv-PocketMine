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
use jacknoordhuis\autoinv\event\handle\PlayerDeathPickup;

class EventConfigurationLoader extends ConfigurationLoader {

	public function onLoad(array $data) {
		$manager = $this->getPlugin()->getEventManager();
		$eventData = $data["general"]["events"];

		if(self::getBoolean($eventData["block-break"])) {
			$manager->registerHandler(new BlockBreakPickup($manager));
		}

		if(self::getBoolean($eventData["player-death"])) {
			$manager->registerHandler(new PlayerDeathPickup($manager));
		}

		if(self::getBoolean($eventData["entity-explosion"])) {
			$manager->registerHandler(new EntityExplosionPickup($manager));
		}
	}

}