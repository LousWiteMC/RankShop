<?php

namespace LousWiteMC\RankShop;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\Server;
use pocketmine\utils\Config;
use LousWiteMC\RankShop\Commands\RankShopCommand;

class RankShop extends PluginBase{

	public static $instance;

	public $pp;

	public $eco;

	public $settings;

	public $data;

	public function onLoad() : void{
		self::$instance = $this;	
	}

	public static function getInstance() : self{
		return self::$instance;
	}

	public function checkProvider(){
		$eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		if(is_null($pp)){
			$this->getServer()->getLogger()->info("[RankShop] Required PurePerms Plugin!");
			$this->getServer()->getPluginLoader()->disablePlugin($this);
		}else{			
			if(is_null($eco)){
				$this->getServer()->getLogger()->info("[RankShop] Required EconomyAPI Plugin!");
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}
		}
	}

	public function onEnable(): void{
		$this->saveResource("shop.yml");
		$this->saveResource("settings.yml");
		$this->data = new Config($this->getDataFolder() . "shop.yml", Config::YAML);
		$this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
		$this->pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->checkProvider();
		$this->getServer()->getCommandMap()->register("rshop", new RankShopCommand($this));
	}
}
