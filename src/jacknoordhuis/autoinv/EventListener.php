<?php

/**
 * AutoInv EventListener class
 *
 * Created on Mar 24, 2016 at 10:12:22 PM
 *
 * @author Jack
 */

namespace jacknoordhuis\autoinv;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\plugin\MethodEventExecutor;

class EventListener implements Listener {

	/** @var Main */
	private $plugin;

	/**
	 * Construct a new event listener class
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$settings = $plugin->getSettings();
		if((bool) $settings->getNested("general.events.block-break")) $this->registerEventHandler(BlockBreakEvent::class, "onBreak", EventPriority::HIGHEST, true);
		if((bool) $settings->getNested("general.events.player-death")) $this->registerEventHandler(PlayerDeathEvent::class, "onDeath", EventPriority::HIGHEST, false);
		if((bool) $settings->getNested("general.events.entity-explosion")) $this->registerEventHandler(EntityExplodeEvent::class, "onExplode", EventPriority::HIGHEST, true);
	}

	/**
	 * Register an event handler function to the plugin manager for this class
	 *
	 * @param string $eventClass
	 * @param string $method
	 * @param int $priority
	 * @param bool $ignoreCancelled
	 */
	protected function registerEventHandler(string $eventClass, string $method, int $priority, bool $ignoreCancelled) {
		$this->plugin->getServer()->getPluginManager()->registerEvent($eventClass, $this, $priority, new MethodEventExecutor($method), $this->plugin, $ignoreCancelled);
	}

	/**
	 * @return Main
	 */
	public function getPlugin() : Main {
		return $this->plugin;
	}

	/**
	 * Handles autoinv block breaking
	 *
	 * @param BlockBreakEvent $event
	 */
	public function onBreak(BlockBreakEvent $event) {
		foreach($event->getDrops() as $drop) {
			$event->getPlayer()->getInventory()->addItem($drop);
		}
		$event->setDrops([]);
	}

	/**
	 * Handles autoinv entity death
	 *
	 * @param PlayerDeathEvent $event
	 */
	public function onDeath(PlayerDeathEvent $event) {
		$victim = $event->getEntity();
		$cause = $victim->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent) {
			$killer = $cause->getDamager();
			if($killer instanceof InventoryHolder) {
				$drops = [];
				foreach($event->getDrops() as $drop) {
					if($killer->getInventory()->canAddItem($drop)) {
						$killer->getInventory()->addItem($drop);
					} else {
						$drops[] = $drop;
					}
				}
				$event->setDrops($drops);
			}
		}
	}

	/**
	 * Handles autoinv entity exploding
	 *
	 * @param EntityExplodeEvent $event
	 */
	public function onExplode(EntityExplodeEvent $event) {
		$explosive = $event->getEntity();
		$closest = PHP_INT_MAX;
		$entity = null;
		foreach($explosive->getLevel()->getNearbyEntities($explosive->getBoundingBox()->grow(24, 24, 24)) as $nearby) {
			if($nearby instanceof InventoryHolder and $explosive->distance($nearby) <= $closest) {
				$entity = $nearby;
				$closest = $explosive->distance($nearby);
			}
		}

		$blocks = $event->getBlockList();
		$yield = $event->getYield();
		$event->setYield(0); // Make sure no item entities are dropped by the explosion
		if($entity !== null) {
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
}
