<?php

namespace GeoUtils;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getServer()->getLogger()->info("§bGeoUtils enabled!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onDisable(){
		$this->getServer()->getLogger()->info("GeoUtils disabled!");
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$player->sendMessage(TextFormat::GREEN . "[GeoUtils] Bentornato " . TextFormat::BLUE . $name);
  }
  
  public function onToManyCaps(PlayerChatEvent $event){
		$this->maxcaps = intval($this->getConfig()->get("max-caps"));
		$player = $event->getPlayer();
		$message = $event->getMessage();
		$strlen = strlen($message);
		$asciiA = ord("A");
		$asciiZ = ord("Z");
		$count = 0;
		for($i = 0; $i < $strlen; $i++){
			$char = $message[$i];
			$ascii = ord($char);
			if($asciiA <= $ascii and $ascii <= $asciiZ){
				$count++;
			}
		}
		if($count > $this->getMaxCaps()){
			$event->setCancelled(true);
			$player->sendMessage("§7[§bGeo§9Utils§7] §cStai usando troppe maiuscole!");
		}
	}
  
  public function onDeath(PlayerDeathEvent $event){
		$cause = $event->getEntity()->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$player = $event->getEntity();
			$killer = $cause->getDamager();
			if($killer instanceof Player){
				$event->setDeathMessage("");
				$player->sendMessage("§cSei stato killato da " . $killer->getName());
				$killer->sendMessage("§bHai killato " . $player->getName());
				$player->setMaxHealth(20);
				$player->getInventory()->clearAll();
			}
		}
	}
	 public function getMaxCaps(){

       return $this->maxcaps;

  }

  public function saveConfig(){

       $this->getConfig()->set("max-caps", $this->getMaxCaps());

       $this->getConfig()->save();

   }

  }
