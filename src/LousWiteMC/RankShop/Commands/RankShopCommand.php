<?php

namespace LousWiteMC\RankShop\Commands;

use pocketmine\Player;
use pocketmine\command\{CommandSender, Command};
use LousWiteMC\RankShop\RankShop;
use LousWiteMC\RankShop\Forms\ShopForm;

class RankShopCommand extends Command{

	private $plugin;

	public function __construct(RankShop $plugin){
		$this->plugin = $plugin;
		parent::__construct(
			"rshop",
			"Buy Ranks Now!",
			"/rshop"
		);
		$this->setUsage("Usage: /rshop");
	}

	public function execute(CommandSender $player, string $commandLabel, array $args){
		if($player instanceof Player){
			$form = new ShopForm($player);
			$form->sendForm($player);
		}else{
			$player->sendMessage("Use this in-game!");
		}
	}
}
