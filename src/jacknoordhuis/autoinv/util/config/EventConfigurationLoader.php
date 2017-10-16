<?php

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