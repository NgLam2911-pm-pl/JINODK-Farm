<?php

namespace LamPocketVN\Farm;

use pocketmine\plugin\PluginBase; 
use pocketmine\utils\config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use LamPocketVN\BlockStorage\BlockStorage;
use LamPocketVN\libPointAPI\libPointAPI;

use onebone\economyapi\EconomyAPI;
use onebone\pointapi\PointAPI;

class Farm extends PluginBase
{
	public $data, $setting;
	
	public $chance = [];
	
	public $tapable = [];
	public $view = [];
	
	public function getData()
	{
		return $this->data->getAll();
	}
	public function getSetting()
	{
		return $this->setting->getAll();
	}
	
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->saveResource("data.yml");
		$this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
		
		$this->saveResource("setting.yml");
		$this->setting = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
		$this->buildProbability();
	}
	public function buildProbability()
	{
		foreach (array_keys($this->getData()) as $tier)
		{
			$list = [];
			foreach(array_keys($this->getData()[$tier]) as $ore)
			{
				$max = $this->getData()[$tier][$ore];
				for ($i = 0; $i < $max; $i++)
				{
					$this->chance[$tier][1] = array_push($list ,$ore);
				}
			}
			$this->getLogger()->info("Build Probability Tier" .$tier . " have " . count($list) . "blocks");
			$this->chance[$tier] = $list;
		}
	}
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
	{
		switch(strtolower($cmd->getName()))
		{
			case "upgrade":
				$sender->sendMessage($this->getSetting()['msg']['up-fence']);
				$this->setTapFence($sender, true);
				return true;
				break;
			case "viewtier":
				$this->view[$sender->getName()] = true;
				$sender->sendMessage($this->getSetting()['msg']['tap-tier']);
				return true;
				break;
			case "givefence":
				if ($sender->hasPermission("farm.give"))
				{
					if (isset($args[0]))
					{
						$tier = $args[0];
						if ($this->isInListTier($tier))
						{
							$item = Item::get(85,0,1);
							$setlore = str_replace("{tier}", $tier, $this->getSetting()['item']['lore']);
							$item->setLore([$setlore]);
							$sender->getInventory()->addItem($item);
						}
						else 
						{
							$sender->sendMessage($this->getSetting()['msg']['give-fail']);
						}
					}
					else
					{
						$sender->sendMessage($this->getSetting()['msg']['give-usage']);
					}
				}
				else
				{
					$sender->sendMessage("You not have permission to use this command !");
				}
				return true;
				break;
		}
	}
	
	public function setTapFence($player, $set)
	{
		if ($set == true)
		{
			$this->tapable[$player->getName()] = true;
		}
		if ($set == false)
		{
			$this->tapable[$player->getName()] = false;
		}
	}
	public function isTapFence($player)
	{
		if ($this->tapable[$player->getName()] == true)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	public function UpgradeFence ($block, $player)
	{
		$tier = $this->getTier($block);
		if (isset($this->getSetting()['tier'][$tier]['next']))
		{
			$ntier = $this->getSetting()['tier'][$tier]['next'];
			$money = EconomyAPI::getInstance()->myMoney($player);
			$point = PointAPI::getInstance()->myPoint($player);
			$costs = explode(" ",$this->getSetting()['tier'][$ntier]['price']);
			$cost = $costs[0];
			$cur = $costs[1];
			if ($cur === "money")
			{
				if ($money >= $cost)
				{
					$this->setTier($block, $ntier);
					$msg = str_replace(["{lv1}", "{lv2}"], [$tier, $ntier], $this->getSetting()['msg']['up-done']);
					$player->sendMessage($msg);
					EconomyAPI::getInstance()->reduceMoney($player, $cost);
				}
				else 
				{
					$msg = str_replace("{price}", $cost-$money, $this->getSetting()['msg']['up-fail']);
					$player->sendMessage($msg);
				}
			}
			else
			{
				if ($point >= $cost)
				{
					$this->setTier($block, $tier+1);
					$msg = str_replace(["{lv1}", "{lv2}"], [$tier, $ntier], $this->getSetting()['msg']['up-done']);
					$player->sendMessage($msg);
					PointAPI::getInstance()->reducePoint($player, $cost);
				}
				else 
				{
					$msg = str_replace("{price}", $cost-$point, $this->getSetting()['msg']['up-fail-point']);
					$player->sendMessage($msg);
				}
			}
		}
		else
		{
			$player->sendMessage($this->getSetting()['msg']['max-tier']);
		}
	}
	
	public function getTier($block)
	{
		$tier = BlockStorage::getInstance()->getBlockData($block);
		if ($tier === "")
		{
			return 0;
		}
		else 
		{
			return $tier;
		}
	}
	public function setTier($block, $tier)
	{
		BlockStorage::getInstance()->setBlockData($block, $tier);
	}
	public function isInListTier($tier)
	{
		foreach (array_keys($this->getSetting()['tier']) as $tiers)
		{
			if ($tier === $tiers)
			{
				return true;
			}
		}
		return false;
	}
	
}