<?php

namespace LousWiteMC\RankShop\Forms;

use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use LousWiteMC\RankShop\RankShop;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use onebone\economyapi\EconomyAPI;

class ShopForm{

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function getPlugin() : RankShop{
		return RankShop::getInstance();
	}

	public function sendForm(Player $player){
		$form = new SimpleForm(function (Player $player, $data){
			if(is_null($data)) return true;
			if($data == 0){
			}else{
				$money = EconomyAPI::getInstance()->myMoney($player);
				$all = $this->getPlugin()->data->getAll();
				$cost = $all[$data]["Cost"];
				$rank = $all[$data]["PurePerms-Group-Name"];
				if($money >= $cost){
					$aform = new ModalForm(function(Player $player, $adata) use ($data){
						if(is_null($adata)) return true;
						if($adata == true){
							$all = $this->getPlugin()->data->getAll();
							$cost = $all[$data]["Cost"];
							$rank = $all[$data]["PurePerms-Group-Name"];
							$pp = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
							$money = EconomyAPI::getInstance()->myMoney($player);
							$r = $pp->getUserDataMgr()->getGroup($player);
							if($r == $rank){
								$player->sendMessage($this->getPlugin()->settings->get("Message-Had-Rank"));
							}else{
								EconomyAPI::getInstance()->reduceMoney($player, $cost);
								$msg = str_replace(["{cost}", "{rank}", "\n"], [$cost, $rank, "\n"], $this->getPlugin()->settings->get("Message-When-Bought"));
								$player->sendMessage($msg);
								Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setgroup ".$player->getName(). " ".$rank);
							}
						}
					});
					$all = $this->getPlugin()->data->getAll();
					$cost = $all[$data]["Cost"];
					$rank = $all[$data]["PurePerms-Group-Name"];
					$money = EconomyAPI::getInstance()->myMoney($player);
					$content = str_replace(["{cost}", "{rank}", "{money}", "\n"], [$cost, $rank, $money, "\n"], $this->getPlugin()->settings->get("AcceptForm-Content"));
					$aform->setTitle($this->getPlugin()->settings->get("AcceptForm-Title"));
					$aform->setContent($content);
					$aform->setButton1($this->getPlugin()->settings->get("AcceptForm-YesButton"));
					$aform->setButton2($this->getPlugin()->settings->get("AcceptForm-NoButton"));
					$aform->sendToPlayer($player);
				}else{
					$m = str_replace(["{cost}", "{rank}", "\n"], [$cost, $rank, "\n"], $this->getPlugin()->get("Message-Not-Enough-Money"));
					$player->sendMessage($m);
				}
			}
		});
		$form->setTitle($this->getPlugin()->settings->get("ShopForm-Title"));
		$all = $this->getPlugin()->data->getAll();
		$money = EconomyAPI::getInstance()->myMoney($player);
		$content = str_replace(["{money}", "\n"], [$money, "\n"], $this->getPlugin()->settings->get("ShopForm-Content"));
		$form->setContent($content);
		$form->addButton($this->getPlugin()->settings->get("Exit-Button"));
		foreach(array_keys($this->getPlugin()->data->getAll()) as $data){
			$cost = $all[$data]["Cost"];
			$rank = $all[$data]["PurePerms-Group-Name"];
			$money = EconomyAPI::getInstance()->myMoney($player);
			$cl = str_replace("{cost}", $cost, $this->getPlugin()->settings->get("ShopForm-Cost-Line"));
			$button = $rank."\n".$cl;
			$pp = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
			$r = $pp->getUserDataMgr()->getGroup($player);
			$form->addButton($button);
		}
		$form->sendToPlayer($player);		
	}
}
